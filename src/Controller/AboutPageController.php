<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AboutPageController extends AbstractController
{
    #[Route('/about-page', name: 'app_about_page')]
    public function index(): Response
    {
        return $this->render('about_page/index.html.twig', [
            'team' => [
                ['image' => 'sample-pic3.jpg', 'focus' => '30% 22%', 'role' => 'FOUNDER & MASTER STYLIST', 'name' => 'Pshyren May Viray', 'text' => 'Started Curlétte after years of cutting curls dry in her own kitchen for friends who’d given up on salons. Still does the first cut for every new client.'],
                ['image' => 'sample-pic2.jpg', 'focus' => '50% 15%', 'role' => 'CURL SPECIALIST', 'name' => 'Ren Cruz', 'text' => 'Trained in curl-by-curl cutting and lives for a good curly bob transformation. If you’re nervous about cutting your curls, ask for Ren.'],
                ['image' => 'sample-pic1.jpg', 'focus' => '50% 18%', 'role' => 'COLORIST', 'name' => 'Leah Santos', 'text' => 'Specializes in color that grows out gracefully — balayage and money-piece highlights using curl-safe formulas that won’t loosen your pattern.'],
                ['image' => 'sample-pic.jpg', 'focus' => '50% 20%', 'role' => 'CURL SPECIALIST & EDUCATOR', 'name' => 'Bea Rodriguez', 'text' => 'Runs our styling workshops and will absolutely rewrite how you think about the “plopping” technique.'],
            ],
        ]);
    }
}
