<?php

namespace App\Controller;

use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductsPageController extends AbstractController
{
    #[Route('/products-page', name: 'app_products_page')]
    public function index(ProductsRepository $productsRepository): Response
    {
        return $this->render('products_page/index.html.twig', [
            'products' => $productsRepository->findBy([], ['productName' => 'ASC']),
        ]);
    }
}
