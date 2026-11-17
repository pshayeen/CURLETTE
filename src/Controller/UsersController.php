<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/users')]
#[IsGranted('ROLE_ADMIN')]
final class UsersController extends AbstractController
{
    #[Route(name: 'app_users_index', methods: ['GET', 'POST'])]
    public function index(UserRepository $userRepository, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $user = new User();
        $user->setRoles(['ROLE_STAFF']);
        $form = $this->createForm(UserType::class, $user, ['include_status' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $form->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'User added.');

            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }

        $term = trim($request->query->getString('q'));
        $role = $request->query->getString('role');

        return $this->render('users/index.html.twig', [
            'users' => $userRepository->search($term, $role),
            'term' => $term,
            'role' => $role,
            'form' => $form,
            'openAddModal' => $form->isSubmitted(),
        ], $form->isSubmitted() ? new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY) : null);
    }

    #[Route('/{id}/edit', name: 'app_users_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $currentUser = $this->getUser();
        $isSelf = $currentUser instanceof User && $currentUser->getId() === $user->getId();

        $form = $this->createForm(UserType::class, $user, [
            'require_password' => false,
            'lock_role' => $isSelf,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            if ($plainPassword) {
                // encode the plain password
                $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            }

            $entityManager->flush();

            $this->addFlash('success', 'User updated.');

            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('users/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/toggle', name: 'app_users_toggle', methods: ['POST'])]
    public function toggle(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();

        if ($currentUser instanceof User && $currentUser->getId() === $user->getId()) {
            $this->addFlash('danger', 'You can’t disable your own account.');
        } elseif ($this->isCsrfTokenValid('toggle'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $user->setIsActive(!$user->isActive());
            $entityManager->flush();

            $this->addFlash('success', $user->isActive() ? 'Account enabled.' : 'Account disabled.');
        }

        return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_users_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $currentUser = $this->getUser();

        if ($currentUser instanceof User && $currentUser->getId() === $user->getId()) {
            $this->addFlash('danger', 'You can’t remove the account you are logged in with.');

            return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();

            $this->addFlash('success', 'User removed.');
        }

        return $this->redirectToRoute('app_users_index', [], Response::HTTP_SEE_OTHER);
    }
}