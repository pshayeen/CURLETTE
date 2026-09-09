<?php
session_start();

require '../database/config.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ../account.php?mode=login');
    exit;
}

$orderId = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
$userId = (int) $_SESSION['user_id'];

if (!$orderId) {
    header('Location: ../my-orders.php?status=error&message=' . urlencode('Invalid order.'));
    exit;
}

$pdo = getConnection();

try {
    $pdo->beginTransaction();

    // must be this user's order and still cancellable
    $stmt = $pdo->prepare(
        "SELECT id FROM shop_order WHERE id = ? AND user_id = ? AND status = 'placed' FOR UPDATE"
    );
    $stmt->execute([$orderId, $userId]);

    if (!$stmt->fetch(PDO::FETCH_ASSOC)) {
        $pdo->rollBack();
        header('Location: ../my-orders.php?status=error&message=' . urlencode('That order can no longer be cancelled.'));
        exit;
    }

    // Restock every item in the order
    $itemsStmt = $pdo->prepare('SELECT product_id, quantity FROM shop_order_item WHERE order_id = ?');
    $itemsStmt->execute([$orderId]);
    $items = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);

    $restockStmt = $pdo->prepare('UPDATE product SET stock_quantity = stock_quantity + ? WHERE id = ?');
    foreach ($items as $item) {
        if ($item['product_id'] !== null) {
            $restockStmt->execute([$item['quantity'], $item['product_id']]);
        }
    }

    $cancelStmt = $pdo->prepare("UPDATE shop_order SET status = 'cancelled' WHERE id = ?");
    $cancelStmt->execute([$orderId]);

    $pdo->commit();

    header('Location: ../my-orders.php?status=success&message=' . urlencode('Order cancelled.'));
    exit;
} catch (PDOException $e) {
    $pdo->rollBack();
    header('Location: ../my-orders.php?status=error&message=' . urlencode('Something went wrong cancelling your order.'));
    exit;
}
