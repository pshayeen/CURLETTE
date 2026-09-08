<?php
session_start();

require '../database/config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ../account.php?mode=login');
    exit;
}

$appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
$userId = (int) $_SESSION['user_id'];

if (!$appointmentId) {
    header('Location: ../my-appointments.php?status=error&message=' . urlencode('Invalid appointment.'));
    exit;
}

$pdo = getConnection();

// Only cancel if it's really this user's appointment and still upcoming
$stmt = $pdo->prepare(
    "UPDATE appointment_booking
     SET status = 'cancelled'
     WHERE id = ? AND user_id = ? AND status = 'upcoming'"
);
$stmt->execute([$appointmentId, $userId]);

if ($stmt->rowCount() === 0) {
    header('Location: ../my-appointments.php?status=error&message=' . urlencode('That appointment could not be cancelled.'));
    exit;
}

header('Location: ../my-appointments.php?status=success&message=' . urlencode('Appointment cancelled.'));
exit;
