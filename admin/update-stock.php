<?php
require 'guard.php';
require '../database/config.php';

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
$stock     = filter_input(INPUT_POST, 'stock_quantity', FILTER_VALIDATE_INT);

if (!$productId || $stock === null || $stock === false || $stock < 0) {
    header('Location: products.php?status=error&message=' . urlencode('Enter a valid stock quantity.'));
    exit;
}

$pdo = getConnection();
$stmt = $pdo->prepare('UPDATE product SET stock_quantity = ? WHERE id = ?');
$stmt->execute([$stock, $productId]);

header('Location: products.php?status=success');
exit;
