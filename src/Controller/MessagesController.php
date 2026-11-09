<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Repository\MessagesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/messages')]
final class MessagesController extends AbstractController
{
    #[Route(name: 'app_messages_index', methods: ['GET'])]
    public function index(MessagesRepository $messagesRepository): Response
    {
        return $this->render('messages/index.html.twig', [
            'contactMessages' => $messagesRepository->findBy([], ['createdAt' => 'DESC']),
            'unreadMessages' => $messagesRepository->findByReadStatus(false),
        ]);
    }

    #[Route('/{id}', name: 'app_messages_show', methods: ['GET'])]
    public function show(Messages $message): Response
    {
        return $this->render('messages/show.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/{id}/read', name: 'app_messages_toggle_read', methods: ['POST'])]
    public function toggleRead(Request $request, Messages $message, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('read' . $message->getId(), $request->getPayload()->getString('_token'))) {
            $message->setIsRead(!$message->isRead());
            $entityManager->flush();

            $this->addFlash('success', $message->isRead() ? 'Message marked as read.' : 'Message marked as unread.');
        }

        return $this->redirectToRoute('app_messages_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_messages_delete', methods: ['POST'])]
    public function delete(Request $request, Messages $message, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $message->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();

            $this->addFlash('success', 'Message deleted.');
        }

        return $this->redirectToRoute('app_messages_index', [], Response::HTTP_SEE_OTHER);
    }
}