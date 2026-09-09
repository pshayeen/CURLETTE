<?php
require 'guard.php';
require '../database/config.php';

$orderId = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
$status  = $_POST['status'] ?? '';
$allowedStatuses = ['placed', 'processing', 'fulfilled', 'cancelled'];

if (!$orderId || !in_array($status, $allowedStatuses, true)) {
    header('Location: orders.php?status=error&message=' . urlencode('Invalid order or status.'));
    exit;
}

$pdo = getConnection();
$stmt = $pdo->prepare('UPDATE shop_order SET status = ? WHERE id = ?');
$stmt->execute([$status, $orderId]);

header('Location: orders.php?status=success');
exit;
