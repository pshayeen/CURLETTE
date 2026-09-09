<?php
require 'guard.php';
require '../database/config.php';

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$price = trim($_POST['price'] ?? '');

if (!$productId || $price === '' || !is_numeric($price) || (float) $price <= 0) {
    header('Location: products.php?status=error&message=' . urlencode('Enter a valid price.'));
    exit;
}

$pdo = getConnection();
$stmt = $pdo->prepare('UPDATE product SET price = ? WHERE id = ?');
$stmt->execute([$price, $productId]);

header('Location: products.php?status=success&message=' . urlencode('Price updated.'));
exit;
