<?php

namespace App\Controller;

use App\Entity\Appointments;
use App\Entity\User;
use App\Form\AppointmentsType;
use App\Repository\ServicesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/book-appointment')]
final class BookingController extends AbstractController
{
    private const DEPOSIT_AMOUNT = '100.00';

    #[Route(name: 'app_booking', methods: ['GET', 'POST'])]
    public function index(Request $request, ServicesRepository $servicesRepository, EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $services = $servicesRepository->findBy([], ['id' => 'ASC']);

        $appointment = new Appointments();

        $preselected = $request->query->getString('service');
        if ('' !== $preselected) {
            $appointment->setService($preselected);
        }

        $form = $this->createForm(AppointmentsType::class, $appointment, [
            'services' => $services,
            'include_status' => false,
            'include_customer' => false,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appointment->setCustomerName($user->getFullName() ?: $user->getUserIdentifier());
            $appointment->setCustomerEmail($user->getEmail() ?? '');
            $appointment->setCustomerPhone($user->getPhoneNumber());
            $appointment->setCustomer($user);
            $appointment->setDepositAmount(self::DEPOSIT_AMOUNT);
            $appointment->setStatus('upcoming');
            $appointment->setCreatedAt(new \DateTimeImmutable());

            $entityManager->persist($appointment);
            $entityManager->flush();

            $this->addFlash('success', 'Your appointment is booked. See you soon!');

            return $this->redirectToRoute('app_account_appointments', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('booking/index.html.twig', [
            'services' => $services,
            'depositAmount' => self::DEPOSIT_AMOUNT,
            'form' => $form->createView(),
        ], $form->isSubmitted() ? new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY) : null);
    }
}
