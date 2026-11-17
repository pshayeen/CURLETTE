<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Entity\User;
use App\Form\ContactFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactPageController extends AbstractController
{
    #[Route('/contact-page', name: 'app_contact_page')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $message = new Messages();

        $user = $this->getUser();
        if ($user instanceof User) {
            $message->setSenderName($user->getFullName() ?: $user->getUserIdentifier());

            if ($user->getEmail()) {
                $message->setSenderEmail($user->getEmail());
            }
            if ($user->getPhoneNumber()) {
                $message->setSenderPhone($user->getPhoneNumber());
            }
        }

        $form = $this->createForm(ContactFormType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            $this->addFlash('contact_success', 'Thanks! Your message has been sent — we’ll get back to you soon.');

            return $this->redirectToRoute('app_contact_page', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('contact_page/index.html.twig', [
            'contactForm' => $form->createView(),
        ], $form->isSubmitted() ? new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY) : null);
    }
}
