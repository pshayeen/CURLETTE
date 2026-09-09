<?php
session_start();

require '../database/config.php';
require '../data/cart.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ../account.php?mode=login&redirect=cart');
    exit;
}

if (!isset($_POST['place_order'])) {
    header('Location: ../cart.php');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$pdo = getConnection();
$cartItems = getCartItems($userId);

if (empty($cartItems)) {
    header('Location: ../cart.php?cart_status=error&cart_message=' . urlencode('Your cart is empty.'));
    exit;
}

try {
    $pdo->beginTransaction();

    // recheck stock before charging
    foreach ($cartItems as $item) {
        if ((int) $item['quantity'] > (int) $item['stock_quantity']) {
            $pdo->rollBack();
            header('Location: ../cart.php?cart_status=error&cart_message=' . urlencode(
                $item['name'] . ' no longer has enough stock — please update your cart.'
            ));
            exit;
        }
    }

    $total = getCartTotal($cartItems);

    $orderStmt = $pdo->prepare("INSERT INTO shop_order (user_id, status, total) VALUES (?, 'placed', ?)");
    $orderStmt->execute([$userId, $total]);
    $orderId = (int) $pdo->lastInsertId();

    $itemStmt = $pdo->prepare(
        'INSERT INTO shop_order_item (order_id, product_id, product_name, unit_price, quantity)
         VALUES (:order_id, :product_id, :product_name, :unit_price, :quantity)'
    );
    $stockStmt = $pdo->prepare(
        'UPDATE product SET stock_quantity = stock_quantity - :qty WHERE id = :id'
    );

    foreach ($cartItems as $item) {
        $itemStmt->execute([
            ':order_id'     => $orderId,
            ':product_id'   => $item['product_id'],
            ':product_name' => $item['name'],
            ':unit_price'   => $item['price'],
            ':quantity'     => $item['quantity'],
        ]);
        $stockStmt->execute([':qty' => $item['quantity'], ':id' => $item['product_id']]);
    }

    $clearStmt = $pdo->prepare('DELETE FROM cart_item WHERE user_id = ?');
    $clearStmt->execute([$userId]);

    $pdo->commit();

    header('Location: ../cart.php?status=success&id=' . $orderId);
    exit;
} catch (PDOException $e) {
    $pdo->rollBack();
    header('Location: ../cart.php?cart_status=error&cart_message=' . urlencode(
        'Something went wrong placing your order. Please try again.'
    ));
    exit;
}
