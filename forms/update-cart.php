<?php
session_start();

require '../database/config.php';
require '../data/products.php';
require '../data/cart.php';
require '../includes/helpers.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ../account.php?mode=login&redirect=cart');
    exit;
}

$userId = (int) $_SESSION['user_id'];
$pdo = getConnection();
$isAjax = ($_POST['ajax'] ?? '') === '1';

// safe redirect targets only
function cartReturnTarget(string $key): string
{
    $map = [
        'index' => '../index.php',
        'products' => '../products.php',
        'cart' => '../cart.php',
    ];
    return $map[$key] ?? '../cart.php';
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

// sends back JSON for AJAX calls, or falls back to the normal redirect
// (so the site still works fine with JavaScript turned off)
function respond(bool $isAjax, string $target, string $status, string $message = '', array $extra = []): never
{
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(array_merge([
            'success' => $status !== 'error',
            'status' => $status,
            'message' => $message,
        ], $extra));
        exit;
    }
    redirectWithStatus($target, $status, $message);
}

$returnTo = cartReturnTarget((string) ($_POST['return_to'] ?? 'cart'));

if (isset($_POST['add_to_cart'])) {
    $productId = filter_input(INPUT_POST, 'add_to_cart', FILTER_VALIDATE_INT);
    $addQty = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $addQty = ($addQty && $addQty > 0) ? $addQty : 1;
    $product = $productId ? getProductById($productId) : null;

    if (!$product) {
        respond($isAjax, $returnTo, 'error', 'That product could not be found.');
    }

    $existingStmt = $pdo->prepare('SELECT quantity FROM cart_item WHERE user_id = ? AND product_id = ?');
    $existingStmt->execute([$userId, $productId]);
    $existingQty = (int) ($existingStmt->fetchColumn() ?: 0);

    if ((int) $product['stock_quantity'] <= 0 || $existingQty >= (int) $product['stock_quantity']) {
        respond($isAjax, $returnTo, 'error', $product['name'] . ' is out of stock.');
    }

    // don't let the add push quantity past what's actually in stock
    $addQty = min($addQty, (int) $product['stock_quantity'] - $existingQty);

    $stmt = $pdo->prepare(
        'INSERT INTO cart_item (user_id, product_id, quantity)
         VALUES (:user_id, :product_id, :add_qty)
         ON DUPLICATE KEY UPDATE quantity = quantity + :add_qty2'
    );
    $stmt->execute([
        ':user_id' => $userId,
        ':product_id' => $productId,
        ':add_qty' => $addQty,
        ':add_qty2' => $addQty,
    ]);

    respond($isAjax, $returnTo, 'added', $product['name'] . ' added to your cart.', [
        'cartCount' => getCartItemCount($userId),
    ]);
}

if (isset($_POST['update_quantity'])) {
    $productId = filter_input(INPUT_POST, 'product_id', FILTER_VALIDATE_INT);
    $quantity  = filter_input(INPUT_POST, 'quantity', FILTER_VALIDATE_INT);
    $product   = $productId ? getProductById($productId) : null;

    if (!$product) {
        respond($isAjax, '../cart.php', 'error', 'That product could not be found.');
    }

    if ($quantity === null || $quantity < 1) {
        $stmt = $pdo->prepare('DELETE FROM cart_item WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$userId, $productId]);
        respond($isAjax, '../cart.php', 'removed', $product['name'] . ' removed from your cart.', [
            'removed' => true,
            'productId' => $productId,
            'cartTotal' => formatPrice(getCartTotal(getCartItems($userId))),
            'cartCount' => getCartItemCount($userId),
        ]);
    }

    $quantity = min($quantity, max((int) $product['stock_quantity'], 0));

    if ($quantity < 1) {
        $stmt = $pdo->prepare('DELETE FROM cart_item WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$userId, $productId]);
        respond($isAjax, '../cart.php', 'error', $product['name'] . ' is out of stock and was removed from your cart.', [
            'removed' => true,
            'productId' => $productId,
            'cartTotal' => formatPrice(getCartTotal(getCartItems($userId))),
            'cartCount' => getCartItemCount($userId),
        ]);
    }

    $stmt = $pdo->prepare('UPDATE cart_item SET quantity = ? WHERE user_id = ? AND product_id = ?');
    $stmt->execute([$quantity, $userId, $productId]);

    respond($isAjax, '../cart.php', 'updated', '', [
        'productId' => $productId,
        'quantity' => $quantity,
        'atMaxStock' => $quantity >= (int) $product['stock_quantity'],
        'subtotal' => formatPrice((float) $product['price'] * $quantity),
        'cartTotal' => formatPrice(getCartTotal(getCartItems($userId))),
        'cartCount' => getCartItemCount($userId),
    ]);
}

if (isset($_POST['remove_item'])) {
    $productId = filter_input(INPUT_POST, 'remove_item', FILTER_VALIDATE_INT);
    if ($productId) {
        $stmt = $pdo->prepare('DELETE FROM cart_item WHERE user_id = ? AND product_id = ?');
        $stmt->execute([$userId, $productId]);
    }
    respond($isAjax, '../cart.php', 'removed', '', [
        'removed' => true,
        'productId' => $productId,
        'cartTotal' => formatPrice(getCartTotal(getCartItems($userId))),
        'cartCount' => getCartItemCount($userId),
    ]);
}

header('Location: ../cart.php');
exit;
