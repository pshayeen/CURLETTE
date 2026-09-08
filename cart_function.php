<?php
session_start();

require 'database/config.php';
require 'data/products.php';

if (empty($_SESSION['user_id'])) {
    header('Location: account.php?mode=login&redirect=cart');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$pdo = getConnection();


function cartReturnTarget(string $key): string
{
    $map = [
        'index' => 'index.php',
        'products' => 'products.php',
        'cart' => 'cart.php',
    ];
    return $map[$key] ?? 'cart.php';
}

function redirectWithStatus(string $target, string $status, string $message = ''): never
{
    $separator = str_contains($target, '?') ? '&' : '?';
    $url = $target . $separator . 'cart_status=' . urlencode($status);
    if ($message !== '') {
        $url .= '&cart_message=' . urlencode($message);
    }
    header('Location: ' . $url);
    exit;
}

$returnTo = cartReturnTarget((string) ($_POST['return_to'] ?? 'cart'));

if (isset($_POST['add_to_cart'])) {
    $productId = filter_input(INPUT_POST, 'add_to_cart', FILTER_VALIDATE_INT);
    $product = $productId ? getProductById($productId) : null;

    if (!$product) {
        redirectWithStatus($returnTo, 'error', 'That product could not be found.');
    }

    $existingStmt = $pdo->prepare('SELECT quantity FROM cart_item WHERE user_id = ? AND product_id = ?');
    $existingStmt->execute([$userId, $productId]);
    $existingQty = (int) ($existingStmt->fetchColumn() ?: 0);

    if ((int) $product['stock_quantity'] <= 0 || $existingQty >= (int) $product['stock_quantity']) {
        redirectWithStatus($returnTo, 'error', $product['name'] . ' is out of stock.');
    }

    $stmt = $pdo->prepare(
        'INSERT INTO cart_item (user_id, product_id, quantity)
         VALUES (:user_id, :product_id, 1)
         ON DUPLICATE KEY UPDATE quantity = quantity + 1'
    );
    $stmt->execute([':user_id' => $userId, ':product_id' => $productId]);

    redirectWithStatus($returnTo, 'added', $product['name'] . ' added to your cart.');
}

if (isset($_POST['update_quantity'])) {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity  = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $product   = $productId ? getProductById($productId) : null;

    if (!$product) {
        redirectWithStatus('cart.php', 'error', 'That product could not be found.');
    }

    if ($quantity === null || $quantity < 1) {
        $stmt = $pdo->prepare('DELETE FROM cart_item WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$userId, $productId]);
        redirectWithStatus('cart.php', 'removed', $product['name'] . ' removed from your cart.');
    }

    $quantity = min($quantity, max((int) $product['stock_quantity'], 0));

    if ($quantity < 1) {
        $stmt = $pdo->prepare('DELETE FROM cart_item WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$userId, $productId]);
        redirectWithStatus('cart.php', 'error', $product['name'] . ' is out of stock and was removed from your cart.');
    }

    $stmt = $pdo->prepare('UPDATE cart_item SET quantity = ? WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$quantity, $userId, $productId]);
    redirectWithStatus('cart.php', 'updated');
}

if (isset($_POST['remove_item'])) {
    $productId = filter_input(INPUT_POST, 'remove_item', FILTER_VALIDATE_INT);
    if ($productId) {
        $stmt = $pdo->prepare('DELETE FROM cart_item WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$userId, $productId]);
    }
    redirectWithStatus('cart.php', 'removed');
}

header('Location: cart.php');
exit;
