<?php
session_start();

require '../database/config.php';
require '../includes/validation.php';
require '../includes/helpers.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ../account.php?mode=login&redirect=book-appointment');
    exit;
}

if (!isset($_POST['book-appointment'])) {
    header('Location: ../bookAppointment.php');
    exit;
}

$result = validateAppointmentInput($_POST);
$errors = $result['errors'];

if (!empty($errors)) {
    $message = implode(' ', $errors);
    $_SESSION['old_booking_input'] = $_POST;
    header('Location: ../bookAppointment.php?status=error&message=' . urlencode($message));
    exit;
}

try {
    $pdo = getConnection();

    $sql = "INSERT INTO appointment_booking (user_id, service, appointment_date, appointment_time, notes, deposit_amount, payment_method, payment_reference)
            VALUES (:user_id, :service, :appointment_date, :appointment_time, :notes, :deposit_amount, :payment_method, :payment_reference)";

    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindValue(':service', $result['data']['service']);
    $stmt->bindValue(':appointment_date', $result['data']['appointment_date']);
    $stmt->bindValue(':appointment_time', $result['data']['appointment_time']);
    $stmt->bindValue(':notes', $result['data']['notes'] !== '' ? $result['data']['notes'] : null);
    $stmt->bindValue(':deposit_amount', getDepositAmount());
    $stmt->bindValue(':payment_method', $result['data']['payment_method']);
    $stmt->bindValue(':payment_reference', $result['data']['payment_reference']);
    $stmt->execute();

    $newId = $pdo->lastInsertId();
    header('Location: ../bookAppointment.php?status=success&id=' . $newId);
    exit;
} catch (PDOException $e) {
    header('Location: ../bookAppointment.php?status=error&message=' . urlencode('Something went wrong. Please try again.'));
    exit;
}
