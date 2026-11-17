<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use App\Repository\ServicesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductsRepository $productsRepository, ServicesRepository $servicesRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'services' => $servicesRepository->findFeatured(4),
            'products' => $productsRepository->findLatest(4),
            'testimonials' => [
                ['image' => 'c5.jpg', 'name' => 'Yelena V.', 'text' => 'My curls have never looked this defined. I also loved how they taught me to care for them.'],
                ['image' => 'c1.jpg', 'name' => 'Amara S.', 'text' => 'Everyone was so welcoming. I loved that they didn’t try to change my natural curls.'],
                ['image' => 'c2.jpg', 'name' => 'Nicole M.', 'text' => 'Finally found a salon that understands curly hair. The result was amazing.'],
                ['image' => 'c3.jpg', 'name' => 'Sofia R.', 'text' => 'The whole experience was relaxing, and my curls felt healthier after my appointment.'],
                ['image' => 'c4.jpg', 'name' => 'Clara M.', 'text' => 'I finally understand how to care for my natural curls. I’ll definitely be coming back.'],
            ],
        ]);
    }
}
