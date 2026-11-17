<?php

namespace App\Controller;

use App\Repository\AppointmentsRepository;
use App\Repository\MessagesRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use App\Repository\ServicesRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_STAFF')]
final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        OrdersRepository $ordersRepository,
        AppointmentsRepository $appointmentsRepository,
        ProductsRepository $productsRepository,
        MessagesRepository $messagesRepository,
        ServicesRepository $servicesRepository,
        UserRepository $userRepository,
    ): Response {
        $user = $this->getUser();

        $stats = [
            'my_orders'             => $ordersRepository->count(['createdBy' => $user]),
            'my_appointments'       => $appointmentsRepository->count(['createdBy' => $user]),
            'pending_orders'        => $ordersRepository->countPending(),
            'upcoming_appointments' => $appointmentsRepository->countUpcoming(),
        ];

        if ($this->isGranted('ROLE_ADMIN')) {
            $stats += [
                'total_users'     => $userRepository->count([]),
                'total_staff'     => $userRepository->countByRole('ROLE_STAFF'),
                'total_records'   => $ordersRepository->count([]) + $appointmentsRepository->count([]) + $productsRepository->count([]) + $servicesRepository->count([]) + $messagesRepository->count([]),
                'total_revenue'   => $ordersRepository->sumRevenue(),
                'total_orders'    => $ordersRepository->count([]),
                'low_stock_count' => count($productsRepository->findLowStock(5)),
                'unread_messages' => $messagesRepository->count(['isRead' => false]),
            ];
        }

        return $this->render('dashboard/index.html.twig', [
            'stats' => $stats,
            'recentOrders' => $ordersRepository->findRecent(5),
            'upcomingAppointments' => $appointmentsRepository->findUpcoming(5),
        ]);
    }
}