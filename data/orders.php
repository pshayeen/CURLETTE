<?php

require_once __DIR__ . '/../database/config.php';

// A user's own orders, most recent first.
function getUserOrders(int $userId): array
{
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        'SELECT id, status, total, payment_method, payment_reference, created_at
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

// one order with customer info, for the receipt page.
// pass $userId to restrict to that user's own order, or null for admin
function getOrderById(int $orderId, ?int $userId = null): ?array
{
    $pdo = getConnection();
    $sql = 'SELECT o.*, u.full_name, u.email
            FROM shop_order o
            INNER JOIN user_account u ON u.id = o.user_id
            WHERE o.id = ?';
    $params = [$orderId];

    if ($userId !== null) {
        $sql .= ' AND o.user_id = ?';
        $params[] = $userId;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);
    return $order ?: null;
}
