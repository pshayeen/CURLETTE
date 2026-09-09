<?php
require 'guard.php';
require '../database/config.php';

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

if (!$productId) {
    header('Location: products.php?status=error&message=' . urlencode('Invalid product.'));
    exit;
}

$pdo = getConnection();
$stmt = $pdo->prepare('DELETE FROM product WHERE id = ?');
$stmt->execute([$productId]);

header('Location: products.php?status=success&message=' . urlencode('Product removed.'));
exit;
