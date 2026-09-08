<?php
session_start();

require '../database/config.php';
require '../includes/validation.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ../account.php?mode=login');
    exit;
}

$appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
$userId = (int) $_SESSION['user_id'];
$date = trim($_POST['appointment_date'] ?? '');
$time = trim($_POST['appointment_time'] ?? '');

if (!$appointmentId) {
    header('Location: ../my-appointments.php?status=error&message=' . urlencode('Invalid appointment.'));
    exit;
}

$errors = array_filter([
    validateRequired($date, 'Date'),
    validateRequired($time, 'Time'),
    $date !== '' ? validateDateNotPast($date, 'Date') : null,
]);

if ($errors) {
    header('Location: ../my-appointments.php?status=error&message=' . urlencode(implode(' ', $errors)));
    exit;
}

$pdo = getConnection();

$stmt = $pdo->prepare(
    "UPDATE appointment_booking
     SET appointment_date = ?, appointment_time = ?
     WHERE id = ? AND user_id = ? AND status = 'upcoming'"
);
$stmt->execute([$date, $time, $appointmentId, $userId]);

if ($stmt->rowCount() === 0) {
    header('Location: ../my-appointments.php?status=error&message=' . urlencode('That appointment could not be rescheduled.'));
    exit;
}

header('Location: ../my-appointments.php?status=success&message=' . urlencode('Appointment rescheduled.'));
exit;
