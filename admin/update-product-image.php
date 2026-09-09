<?php
require 'guard.php';
require '../database/config.php';
require '../includes/uploads.php';

$productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);

if (!$productId) {
    header('Location: products.php?status=error&message=' . urlencode('Invalid product.'));
    exit;
}

$upload = handleProductImageUpload($_FILES['image'] ?? []);

if (!$upload['success']) {
    $message = $upload['error'] === 'no_file' ? 'Please choose a photo to upload.' : $upload['error'];
    header('Location: products.php?status=error&message=' . urlencode($message));
    exit;
}

$pdo = getConnection();
$stmt = $pdo->prepare('UPDATE product SET image = ? WHERE id = ?');
$stmt->execute([$upload['path'], $productId]);

header('Location: products.php?status=success&message=' . urlencode('Photo updated.'));
exit;
