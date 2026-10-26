<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        return $this->render('dashboard/index.html.twig', [
            'adminUsername' => 'Admin',

            // sample data only for now
            'stats' => [
                'total_revenue'         => 12500,
                'pending_orders'        => 3,
                'total_orders'          => 18,
                'upcoming_appointments' => 5,
                'low_stock_count'       => 2,
                'unread_messages'       => 1,
            ],

            'recentOrders' => [
                ['id' => 18, 'full_name' => 'Ana Cruz',    'date' => 'Oct 5, 2026', 'status' => 'placed'],
                ['id' => 17, 'full_name' => 'Bea Santos',  'date' => 'Oct 4, 2026', 'status' => 'processing'],
                ['id' => 16, 'full_name' => 'Carla Reyes', 'date' => 'Oct 2, 2026', 'status' => 'fulfilled'],
            ],

            'upcomingAppointments' => [
                ['full_name' => 'Dana Lim',    'service' => 'Curl Styling',  'date' => 'Oct 8, 2026',  'time' => '2:00 PM'],
                ['full_name' => 'Ella Torres', 'service' => 'Curl Hair Cut', 'date' => 'Oct 9, 2026',  'time' => '10:30 AM'],
                ['full_name' => 'Faye Gomez',  'service' => 'Wash & Define', 'date' => 'Oct 10, 2026', 'time' => '1:00 PM'],
            ],
        ]);
    }
}