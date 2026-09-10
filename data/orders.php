<?php

require_once __DIR__ . '/../database/config.php';

// A user's own orders, most recent first.
function getUserOrders(int $userId): array
{
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        'SELECT id, status, total, payment_method, created_at
         FROM shop_order
         WHERE user_id = ?
         ORDER BY created_at DESC'
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// items for one order, checked against the user
function getUserOrderItems(int $orderId, int $userId): array
{
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        'SELECT oi.product_name, oi.unit_price, oi.quantity
         FROM shop_order_item oi
         INNER JOIN shop_order o ON o.id = oi.order_id
         WHERE oi.order_id = ? AND o.user_id = ?'
    );
    $stmt->execute([$orderId, $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
