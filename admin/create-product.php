<?php
require 'guard.php';
require '../database/config.php';
require '../includes/validation.php';
require '../includes/uploads.php';

$name        = trim($_POST['name'] ?? '');
$price       = trim($_POST['price'] ?? '');
$description = trim($_POST['description'] ?? '');
$stock       = filter_input(INPUT_POST, 'stock_quantity', FILTER_VALIDATE_INT);

$errors = array_filter([
    validateRequired($name, 'Product name'),
    validateRequired($price, 'Price'),
    validateRequired($description, 'Description'),
]);

if ($price !== '' && (!is_numeric($price) || (float) $price <= 0)) {
    $errors[] = 'Price must be a positive number.';
}

if ($stock === null || $stock === false || $stock < 0) {
    $errors[] = 'Stock must be zero or a positive whole number.';
}

$upload = handleProductImageUpload($_FILES['image'] ?? []);

if (!$upload['success']) {
    $errors[] = $upload['error'] === 'no_file' ? 'Please choose a product photo.' : $upload['error'];
}

if ($errors) {
    header('Location: add-product.php?status=error&message=' . urlencode(implode(' ', $errors)));
    exit;
}

try {
    $pdo = getConnection();
    $nextSort = (int) $pdo->query('SELECT COALESCE(MAX(sort_order), 0) FROM product')->fetchColumn() + 1;

    $stmt = $pdo->prepare(
        'INSERT INTO product (name, price, image, description, stock_quantity, sort_order)
         VALUES (:name, :price, :image, :description, :stock_quantity, :sort_order)'
    );
    $stmt->execute([
        ':name' => $name,
        ':price' => $price,
        ':image' => $upload['path'],
        ':description' => $description,
        ':stock_quantity' => $stock,
        ':sort_order' => $nextSort,
    ]);

    header('Location: products.php?status=success&message=' . urlencode('Product added.'));
    exit;
} catch (PDOException $e) {
    header('Location: add-product.php?status=error&message=' . urlencode('Something went wrong adding the product. Please try again.'));
    exit;
}
