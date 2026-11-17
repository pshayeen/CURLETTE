<?php

namespace App\Controller;

use App\Entity\Appointments;
use App\Form\AppointmentsType;
use App\Repository\AppointmentsRepository;
use App\Repository\ServicesRepository;
use App\Security\Voter\RecordOwnerVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/appointments')]
#[IsGranted('ROLE_STAFF')]
final class AppointmentsController extends AbstractController
{
    private const DEPOSIT_AMOUNT = '100.00';

    #[Route(name: 'app_appointments_index', methods: ['GET', 'POST'])]
    public function index(AppointmentsRepository $appointmentsRepository, ServicesRepository $servicesRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $services = $servicesRepository->findBy([], ['id' => 'ASC']);

        $appointment = new Appointments();
        $form = $this->createForm(AppointmentsType::class, $appointment, [
            'services' => $services,
            'include_status' => false,
            'require_phone' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appointment->setDepositAmount(self::DEPOSIT_AMOUNT);
            $appointment->setStatus('upcoming');
            $appointment->setCreatedAt(new \DateTimeImmutable());
            $appointment->setCreatedBy($this->getUser());

            $entityManager->persist($appointment);
            $entityManager->flush();

            $this->addFlash('success', 'Appointment booked.');

            return $this->redirectToRoute('app_appointments_index', [], Response::HTTP_SEE_OTHER);
        }

        $term = trim($request->query->getString('q'));
        $status = $request->query->getString('status');

        return $this->render('appointments/index.html.twig', [
            'appointments' => $appointmentsRepository->search($term, $status),
            'upcomingAppointments' => $appointmentsRepository->findByStatus('upcoming'),
            'services' => $services,
            'depositAmount' => self::DEPOSIT_AMOUNT,
            'term' => $term,
            'status' => $status,
            'form' => $form->createView(),
            'openAddModal' => $form->isSubmitted(),
        ], $form->isSubmitted() ? new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY) : null);
    }

    #[Route('/new', name: 'app_appointments_new', methods: ['GET', 'POST'])]
    public function new(ServicesRepository $servicesRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $services = $servicesRepository->findBy([], ['id' => 'ASC']);

        $appointment = new Appointments();
        $form = $this->createForm(AppointmentsType::class, $appointment, [
            'services' => $services,
            'include_status' => false,
            'require_phone' => true,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appointment->setDepositAmount(self::DEPOSIT_AMOUNT);
            $appointment->setStatus('upcoming');
            $appointment->setCreatedAt(new \DateTimeImmutable());
            $appointment->setCreatedBy($this->getUser());

            $entityManager->persist($appointment);
            $entityManager->flush();

            $this->addFlash('success', 'Appointment booked.');

            return $this->redirectToRoute('app_appointments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('appointments/new.html.twig', [
            'form' => $form,
            'services' => $services,
            'depositAmount' => self::DEPOSIT_AMOUNT,
        ], $form->isSubmitted() ? new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY) : null);
    }

    #[Route('/{id}', name: 'app_appointments_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(Appointments $appointment): Response
    {
        return $this->render('appointments/show.html.twig', [
            'appointment' => $appointment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_appointments_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Appointments $appointment, EntityManagerInterface $entityManager, ServicesRepository $servicesRepository): Response
    {
        $this->denyAccessUnlessGranted(RecordOwnerVoter::EDIT, $appointment);

        $form = $this->createForm(AppointmentsType::class, $appointment, [
            'services' => $servicesRepository->findBy([], ['id' => 'ASC']),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Appointment updated.');

            return $this->redirectToRoute('app_appointments_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('appointments/edit.html.twig', [
            'appointment' => $appointment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_appointments_delete', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Appointments $appointment, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted(RecordOwnerVoter::DELETE, $appointment);

        if ($this->isCsrfTokenValid('delete' . $appointment->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($appointment);
            $entityManager->flush();

            $this->addFlash('success', 'Appointment deleted.');
        }

        return $this->redirectToRoute('app_appointments_index', [], Response::HTTP_SEE_OTHER);
    }
}