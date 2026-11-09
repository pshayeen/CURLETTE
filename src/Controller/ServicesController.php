<?php

namespace App\Controller;

use App\Entity\Services;
use App\Form\ServicesType;
use App\Repository\ServicesRepository;
use App\Service\ImageUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/services')]
final class ServicesController extends AbstractController
{
    #[Route(name: 'app_services_index', methods: ['GET', 'POST'])]
    public function index(ServicesRepository $servicesRepository, Request $request, EntityManagerInterface $entityManager, ImageUploader $imageUploader): Response
    {
        $service = new Services();
        $form = $this->createForm(ServicesType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $service->setImage($imageUploader->upload($imageFile, 'services'));
            }

            $entityManager->persist($service);
            $entityManager->flush();

            $this->addFlash('success', 'Service added.');

            return $this->redirectToRoute('app_services_index', [], Response::HTTP_SEE_OTHER);
        }

        $search = trim($request->query->getString('q'));

        return $this->render('services/index.html.twig', [
            'services' => $search !== ''
                ? $servicesRepository->searchByName($search)
                : $servicesRepository->findBy([], ['id' => 'ASC']),
            'search' => $search,
            'form' => $form->createView(),
            'openAddModal' => $form->isSubmitted(),   // reopen the modal when there are errors
        ], $form->isSubmitted() ? new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY) : null);
    }

    #[Route('/new', name: 'app_services_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ImageUploader $imageUploader): Response
    {
        $service = new Services();
        $form = $this->createForm(ServicesType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $service->setImage($imageUploader->upload($imageFile, 'services'));
            }

            $entityManager->persist($service);
            $entityManager->flush();

            $this->addFlash('success', 'Service added.');

            return $this->redirectToRoute('app_services_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('services/new.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_services_show', methods: ['GET'])]
    public function show(Services $service): Response
    {
        return $this->render('services/show.html.twig', [
            'service' => $service,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_services_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Services $service, EntityManagerInterface $entityManager, ImageUploader $imageUploader): Response
    {
        $form = $this->createForm(ServicesType::class, $service);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // a new photo replaces the old one; no file chosen keeps the current photo
            $imageFile = $form->get('imageFile')->getData();
            if ($imageFile) {
                $service->setImage($imageUploader->upload($imageFile, 'services'));
            }

            $entityManager->flush();

            $this->addFlash('success', 'Service updated.');

            return $this->redirectToRoute('app_services_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('services/edit.html.twig', [
            'service' => $service,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_services_delete', methods: ['POST'])]
    public function delete(Request $request, Services $service, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $service->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($service);
            $entityManager->flush();

            $this->addFlash('success', 'Service removed.');
        }

        return $this->redirectToRoute('app_services_index', [], Response::HTTP_SEE_OTHER);
    }
}