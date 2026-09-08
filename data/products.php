<?php

require_once __DIR__ . '/../database/config.php';

/**
 * Full product catalog, in display order. Each row includes stock_quantity
 * so pages can show "Out of Stock" and cart_function.php can enforce it.
 */
function getProducts(): array
{
    $pdo = getConnection();
    $stmt = $pdo->query(
        'SELECT id, name, price, image, description, stock_quantity
         FROM product
         ORDER BY sort_order ASC, id ASC'
    );
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * A single product by id, or null if it doesn't exist.
 */
function getProductById(int $id): ?array
{
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        'SELECT id, name, price, image, description, stock_quantity
         FROM product
         WHERE id = ?
         LIMIT 1'
    );
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    return $product ?: null;
}
