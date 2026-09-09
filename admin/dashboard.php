<?php
require 'guard.php';
require '../data/admin.php';

$adminActive = 'dashboard';
$stats = getDashboardStats();
$recentOrders = getRecentOrders(5);
$upcomingAppointments = getUpcomingAppointmentsPreview(5);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Curlétte</title>
    <link rel="icon" href="../assets/images/C-icon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500,600;1,500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'partials/nav.php'; ?>

<main class="admin-main">

    <div class="admin-page-header">
        <div>
            <h1>Dashboard</h1>
            <p>Welcome back, <?= htmlspecialchars($adminUsername, ENT_QUOTES, 'UTF-8') ?>.</p>
        </div>
    </div>

    <div class="admin-stats-grid">
        <div class="admin-stat-card">
            <div class="stat-icon"><i class="bi bi-bag-check"></i></div>
            <strong><?= $stats['pending_orders'] ?></strong>
            <span>Pending Orders</span>
        </div>
        <div class="admin-stat-card">
            <div class="stat-icon"><i class="bi bi-receipt"></i></div>
            <strong><?= $stats['total_orders'] ?></strong>
            <span>Total Orders</span>
        </div>
        <div class="admin-stat-card">
            <div class="stat-icon"><i class="bi bi-calendar-check"></i></div>
            <strong><?= $stats['upcoming_appointments'] ?></strong>
            <span>Upcoming Appointments</span>
        </div>
        <div class="admin-stat-card <?= $stats['low_stock_count'] > 0 ? 'alert' : '' ?>">
            <div class="stat-icon"><i class="bi bi-box-seam"></i></div>
            <strong><?= $stats['low_stock_count'] ?></strong>
            <span>Low Stock Items</span>
        </div>
        <div class="admin-stat-card <?= $stats['unread_messages'] > 0 ? 'alert' : '' ?>">
            <div class="stat-icon"><i class="bi bi-envelope"></i></div>
            <strong><?= $stats['unread_messages'] ?></strong>
            <span>Unread Messages</span>
        </div>
    </div>

    <div class="admin-panels">

        <div class="admin-panel">
            <h2>Recent Orders</h2>
            <?php if (empty($recentOrders)): ?>
                <p class="admin-empty">No orders yet.</p>
            <?php else: ?>
                <?php foreach ($recentOrders as $order): ?>
                    <div class="admin-preview-row">
                        <div>
                            <strong>#<?= (int) $order['id'] ?> — <?= htmlspecialchars($order['username'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <span><?= date('M j, Y', strtotime($order['created_at'])) ?></span>
                        </div>
                        <span class="admin-badge <?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?></span>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <p class="admin-panel-footer"><a href="orders.php" class="admin-link">View all orders →</a></p>
        </div>

        <div class="admin-panel">
            <h2>Upcoming Appointments</h2>
            <?php if (empty($upcomingAppointments)): ?>
                <p class="admin-empty">No upcoming appointments.</p>
            <?php else: ?>
                <?php foreach ($upcomingAppointments as $appt): ?>
                    <div class="admin-preview-row">
                        <div>
                            <strong><?= htmlspecialchars($appt['username'], ENT_QUOTES, 'UTF-8') ?> — <?= htmlspecialchars($appt['service'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <span><?= date('M j, Y', strtotime($appt['appointment_date'])) ?> at <?= date('g:i A', strtotime($appt['appointment_time'])) ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            <p class="admin-panel-footer"><a href="appointments.php" class="admin-link">View all appointments →</a></p>
        </div>

    </div>

</main>

</body>
</html>
