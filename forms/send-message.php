<?php
session_start();

require '../database/config.php';
require '../includes/validation.php';

if (!isset($_POST['send_message'])) {
    header('Location: ../contact.php');
    exit;
}

$name    = trim($_POST['name'] ?? '');
$email   = trim($_POST['email'] ?? '');
$phone   = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');

$errors = array_filter([
    validateRequired($name, 'Name'),
    validateRequired($email, 'Email'),
    validateEmailFormat($email),
    validateRequired($message, 'Message'),
]);

if ($errors) {
    header('Location: ../contact.php?status=error&message=' . urlencode(implode(' ', $errors)));
    exit;
}

try {
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        'INSERT INTO contact_message (name, email, phone, message)
         VALUES (:name, :email, :phone, :message)'
    );
    $stmt->execute([
        ':name' => $name,
        ':email' => $email,
        ':phone' => $phone !== '' ? $phone : null,
        ':message' => $message,
    ]);

    header('Location: ../contact.php?status=success');
    exit;
} catch (PDOException $e) {
    header('Location: ../contact.php?status=error&message=' . urlencode('Something went wrong sending your message. Please try again.'));
    exit;
}
