<?php
session_start();
require 'includes/helpers.php';
require 'data/appointments.php';

if (empty($_SESSION['user_id'])) {
    header('Location: account.php?mode=login');
    exit;
}

$isLoggedIn = true;
$username   = $_SESSION['username'] ?? 'Account';
$bookHref   = bookHref($isLoggedIn);
$appointments = getUserAppointments((int) $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments — Curlétte Hair Studio</title>
    <link rel="icon" href="assets/C-icon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-logged-in="1">

<?php include 'partials/nav.php'; ?>

<main class="booking-page">
    <section class="booking-hero">

        <div class="booking-heading">
            <p class="home-label">YOUR CURLÉTTE ACCOUNT</p>
            <h1>My <em>appointments.</em></h1>
            <p>Every visit you've booked with us, past and upcoming.</p>
        </div>

        <?php if (empty($appointments)): ?>
            <div class="cart-empty">
                <i class="bi bi-calendar-x"></i>
                <h2>No appointments yet.</h2>
                <p>When you book a visit, it'll show up here.</p>
                <a href="<?= $bookHref ?>" class="btn-main">BOOK AN APPOINTMENT</a>
            </div>
        <?php else: ?>
            <div class="my-list">
                <?php foreach ($appointments as $appt): ?>
                    <div class="my-list-item">
                        <div class="my-list-item-header">
                            <div>
                                <h3><?= htmlspecialchars($appt['service'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <span class="muted">
                                    <?= date('M j, Y', strtotime($appt['appointment_date'])) ?>
                                    at <?= date('g:i A', strtotime($appt['appointment_time'])) ?>
                                </span>
                                <?php if ($appt['notes']): ?>
                                    <span class="muted"><?= htmlspecialchars($appt['notes'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="status-badge <?= htmlspecialchars($appt['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($appt['status'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </section>
</main>

<?php include 'partials/footer.php'; ?>
<script src="assets/js/script.js"></script>
</body>
</html>
