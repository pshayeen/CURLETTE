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

if (isset($_POST['cancel_appointment'])) {
    // must be this user's upcoming appointment
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
}

if (isset($_POST['reschedule_appointment'])) {
    require '../includes/validation.php';

    $date = trim($_POST['appointment_date'] ?? '');
    $time = trim($_POST['appointment_time'] ?? '');

    $errors = array_filter([
        validateRequired($date, 'Date'),
        validateRequired($time, 'Time'),
        $date !== '' ? validateDateNotPast($date, 'Date') : null,
    ]);

    if ($errors) {
        header('Location: ../my-appointments.php?status=error&message=' . urlencode(implode(' ', $errors)));
        exit;
    }

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
}

header('Location: ../my-appointments.php');
exit;
