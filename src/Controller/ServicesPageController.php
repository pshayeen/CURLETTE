<?php

namespace App\Controller;

use App\Repository\ServicesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ServicesPageController extends AbstractController
{
    #[Route('/services-page', name: 'app_services_page')]
    public function index(ServicesRepository $servicesRepository): Response
    {
        return $this->render('services_page/index.html.twig', [
            'services' => $servicesRepository->findBy([], ['id' => 'ASC']),
        ]);
    }
}
