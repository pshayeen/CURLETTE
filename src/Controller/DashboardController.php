<?php

namespace App\Controller;

use App\Repository\AppointmentsRepository;
use App\Repository\MessagesRepository;
use App\Repository\OrdersRepository;
use App\Repository\ProductsRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(
        OrdersRepository $ordersRepository,
        AppointmentsRepository $appointmentsRepository,
        ProductsRepository $productsRepository,
        MessagesRepository $messagesRepository,
    ): Response {
        return $this->render('dashboard/index.html.twig', [
            'stats' => [
                'total_revenue'         => $ordersRepository->sumRevenue(),
                'pending_orders'        => $ordersRepository->countPending(),
                'total_orders'          => $ordersRepository->count([]),
                'upcoming_appointments' => $appointmentsRepository->countUpcoming(),
                'low_stock_count'       => count($productsRepository->findLowStock(5)),
                'unread_messages'       => $messagesRepository->count(['isRead' => false]),
            ],
            'recentOrders' => $ordersRepository->findRecent(5),
            'upcomingAppointments' => $appointmentsRepository->findUpcoming(5),
        ]);
    }
}