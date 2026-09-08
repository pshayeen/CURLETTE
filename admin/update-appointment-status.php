<?php
require 'guard.php';
require '../database/config.php';

$appointmentId = filter_input(INPUT_POST, 'appointment_id', FILTER_VALIDATE_INT);
$status = $_POST['status'] ?? '';
$allowedStatuses = ['upcoming', 'completed', 'cancelled'];

if (!$appointmentId || !in_array($status, $allowedStatuses, true)) {
    header('Location: appointments.php?status=error&message=' . urlencode('Invalid appointment or status.'));
    exit;
}

$pdo = getConnection();
$stmt = $pdo->prepare('UPDATE appointment_booking SET status = ? WHERE id = ?');
$stmt->execute([$status, $appointmentId]);

header('Location: appointments.php?status=success');
exit;
