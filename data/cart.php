<?php

require_once __DIR__ . '/../database/config.php';

/**
 * A user's cart, joined with live product data so price/stock/image are
 * always current even if the catalog changed since the item was added.
 */
function getCartItems(int $userId): array
{
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        'SELECT p.id AS product_id, p.name, p.price, p.image, p.stock_quantity, c.quantity
         FROM cart_item c
         INNER JOIN product p ON p.id = c.product_id
         WHERE c.user_id = ?
         ORDER BY c.updated_at DESC'
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * Sum of (price × quantity) across all cart line items.
 */
function getCartTotal(array $cartItems): float
{
    $total = 0.0;
    foreach ($cartItems as $item) {
        $total += (float) $item['price'] * (int) $item['quantity'];
    }
    return $total;
}
