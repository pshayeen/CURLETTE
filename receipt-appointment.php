<?php
session_start();
require 'includes/helpers.php';
require 'data/appointments.php';

if (empty($_SESSION['user_id'])) {
    header('Location: account.php?mode=login');
    exit;
}

$appointmentId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$isAdmin = !empty($_SESSION['is_admin']);

if (!$appointmentId) {
    header('Location: my-appointments.php');
    exit;
}

$appt = getAppointmentById($appointmentId, $isAdmin ? null : (int) $_SESSION['user_id']);

if (!$appt) {
    header('Location: my-appointments.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — Booking #<?= (int) $appt['id'] ?></title>
    <link rel="icon" href="assets/images/C-icon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-logged-in="1">

<div class="receipt-page">
    <div class="receipt">

        <div class="receipt-header">
            <img src="assets/images/logo.png" alt="Curlétte Hair Studio">
            <p>123 Curl Street, Cebu City, 6000 Cebu, Philippines</p>
            <p>0912 345 6789 &middot; curlettehairstudio@email.com</p>
        </div>

        <h1>Booking Receipt</h1>

        <div class="receipt-meta">
            <div><span>Booking</span><strong>#<?= (int) $appt['id'] ?></strong></div>
            <div><span>Booked On</span><strong><?= date('F j, Y', strtotime($appt['created_at'])) ?></strong></div>
            <div><span>Customer</span><strong><?= htmlspecialchars($appt['full_name'], ENT_QUOTES, 'UTF-8') ?></strong></div>
            <div><span>Status</span><strong><?= htmlspecialchars(ucfirst($appt['status']), ENT_QUOTES, 'UTF-8') ?></strong></div>
        </div>

        <table class="receipt-items">
            <thead>
                <tr>
                    <th>Service</th>
                    <th>Date</th>
                    <th>Time</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?= htmlspecialchars($appt['service'], ENT_QUOTES, 'UTF-8') ?></td>
                    <td><?= date('M j, Y', strtotime($appt['appointment_date'])) ?></td>
                    <td><?= date('g:i A', strtotime($appt['appointment_time'])) ?></td>
                </tr>
            </tbody>
        </table>

        <?php if (!empty($appt['notes'])): ?>
            <p class="receipt-notes"><strong>Notes:</strong> <?= htmlspecialchars($appt['notes'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>

        <div class="receipt-total">
            <span>Deposit Paid</span>
            <strong><?= formatPrice($appt['deposit_amount']) ?></strong>
        </div>

        <p class="receipt-payment">
            Paid via <?= paymentMethodLabel($appt['payment_method']) ?>
            <?php if ($appt['payment_reference']): ?> &mdash; Ref# <?= htmlspecialchars($appt['payment_reference'], ENT_QUOTES, 'UTF-8') ?><?php endif; ?>
        </p>

        <p class="receipt-footer-note">See you soon at Curlétte Hair Studio.</p>

        <div class="receipt-actions">
            <button type="button" onclick="window.print()" class="btn-main">PRINT RECEIPT</button>
            <a href="my-appointments.php" class="btn-outline">BACK TO MY APPOINTMENTS</a>
        </div>

    </div>
</div>

</body>
</html>
