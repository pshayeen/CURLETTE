<?php

namespace App\Controller;

use App\Entity\Appointments;
use App\Entity\Orders;
use App\Entity\User;
use App\Form\AccountType;
use App\Form\ChangePasswordType;
use App\Repository\AppointmentsRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/account')]
final class AccountController extends AbstractController
{
    #[Route(name: 'app_account', methods: ['GET', 'POST'])]
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $profileForm = $this->createForm(AccountType::class, $user);
        $passwordForm = $this->createForm(ChangePasswordType::class);

        $profileForm->handleRequest($request);
        $passwordForm->handleRequest($request);

        if ($profileForm->isSubmitted() && $profileForm->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Your details were updated.');

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        if ($passwordForm->isSubmitted() && $passwordForm->isValid()) {
            /** @var string $plainPassword */
            $plainPassword = $passwordForm->get('plainPassword')->getData();

            // encode the plain password
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));
            $entityManager->flush();

            $this->addFlash('success', 'Your password was changed.');

            return $this->redirectToRoute('app_account', [], Response::HTTP_SEE_OTHER);
        }

        $invalid = ($profileForm->isSubmitted() && !$profileForm->isValid())
            || ($passwordForm->isSubmitted() && !$passwordForm->isValid());

        return $this->render('account/index.html.twig', [
            'user' => $user,
            'profileForm' => $profileForm->createView(),
            'passwordForm' => $passwordForm->createView(),
        ], $invalid ? new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY) : null);
    }

    #[Route('/orders', name: 'app_account_orders', methods: ['GET'])]
    public function orders(OrdersRepository $ordersRepository): Response
    {
        return $this->render('account/orders.html.twig', [
            'orders' => $ordersRepository->findBy(['customer' => $this->getUser()], ['createdAt' => 'DESC']),
        ]);
    }

    #[Route('/orders/{id}/cancel', name: 'app_account_order_cancel', methods: ['POST'])]
    public function cancelOrder(Request $request, Orders $order, ProductsRepository $productsRepository, EntityManagerInterface $entityManager): Response
    {
        $this->assertOwner($order->getCustomer());

        if ($this->isCsrfTokenValid('cancel-order' . $order->getId(), $request->getPayload()->getString('_token')) && 'placed' === $order->getStatus()) {
            foreach ($order->getItems() as $item) {
                $product = $productsRepository->findOneBy(['productName' => $item->getProductName()]);

                if ($product) {
                    $product->setStockQuantity($product->getStockQuantity() + $item->getQuantity());
                }
            }

            $order->setStatus('cancelled');
            $entityManager->flush();

            $this->addFlash('success', 'Your order was cancelled.');
        }

        return $this->redirectToRoute('app_account_orders', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/appointments', name: 'app_account_appointments', methods: ['GET'])]
    public function appointments(AppointmentsRepository $appointmentsRepository): Response
    {
        return $this->render('account/appointments.html.twig', [
            'appointments' => $appointmentsRepository->findBy(['customer' => $this->getUser()], ['appointmentDate' => 'DESC', 'appointmentTime' => 'DESC']),
        ]);
    }

    #[Route('/appointments/{id}/cancel', name: 'app_account_appointment_cancel', methods: ['POST'])]
    public function cancelAppointment(Request $request, Appointments $appointment, EntityManagerInterface $entityManager): Response
    {
        $this->assertOwner($appointment->getCustomer());

        if ($this->isCsrfTokenValid('cancel-appointment' . $appointment->getId(), $request->getPayload()->getString('_token')) && 'upcoming' === $appointment->getStatus()) {
            $appointment->setStatus('cancelled');
            $entityManager->flush();

            $this->addFlash('success', 'Your appointment was cancelled.');
        }

        return $this->redirectToRoute('app_account_appointments', [], Response::HTTP_SEE_OTHER);
    }

    private function assertOwner(?User $owner): void
    {
        $current = $this->getUser();

        if (!$current instanceof User || null === $owner || $owner->getId() !== $current->getId()) {
            throw $this->createAccessDeniedException();
        }
    }
}
