<?php
session_start();

require 'includes/helpers.php';
require 'database/config.php';
require 'data/cart.php';

if (empty($_SESSION['user_id'])) {
    header('Location: account.php?mode=login&redirect=cart');
    exit;
}

$isLoggedIn = true;
$username   = $_SESSION['username'] ?? 'Account';
$bookHref   = bookHref($isLoggedIn);
$userId     = (int) $_SESSION['user_id'];

$status      = $_GET['status'] ?? null;
$orderId     = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$cartStatus  = $_GET['cart_status'] ?? null;
$cartMessage = $_GET['cart_message'] ?? null;

$order = null;
$orderItems = [];

if ($status === 'success' && $orderId) {
    $pdo = getConnection();

    $orderStmt = $pdo->prepare('SELECT id, total, created_at FROM shop_order WHERE id = ? AND user_id = ?');
    $orderStmt->execute([$orderId, $userId]);
    $order = $orderStmt->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        $itemsStmt = $pdo->prepare(
            'SELECT product_name, unit_price, quantity FROM shop_order_item WHERE order_id = ?'
        );
        $itemsStmt->execute([$orderId]);
        $orderItems = $itemsStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$cartItems = $order ? [] : getCartItems($userId);
$cartTotal = getCartTotal($cartItems);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Your Cart — Curlétte Hair Studio</title>
    <link rel="icon" href="assets/C-icon.png" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body data-logged-in="1">

<?php include 'partials/nav.php'; ?>

<main class="booking-page">
    <section class="booking-hero">

        <div class="booking-heading">
            <p class="home-label">YOUR CURLÉTTE CART</p>
            <h1>Your <em>cart.</em></h1>
            <p>Review your items, then place your order whenever you're ready.</p>
        </div>

        <?php if ($order): ?>
            <div class="booking-success-card">
                <div class="booking-success-icon"><i class="bi bi-check2"></i></div>
                <div>
                    <p class="booking-eyebrow">ORDER PLACED</p>
                    <h2>Thanks for your order!</h2>
                    <p>Confirmation #<?= htmlspecialchars((string) $order['id'], ENT_QUOTES, 'UTF-8') ?></p>
                </div>

                <div class="cart-order-summary">
                    <?php foreach ($orderItems as $item): ?>
                        <div class="cart-order-summary-row">
                            <span><?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?> × <?= (int) $item['quantity'] ?></span>
                            <strong><?= formatPrice((float) $item['unit_price'] * (int) $item['quantity']) ?></strong>
                        </div>
                    <?php endforeach; ?>
                    <div class="cart-order-summary-row cart-order-summary-total">
                        <span>Total</span>
                        <strong><?= formatPrice($order['total']) ?></strong>
                    </div>
                </div>

                <a href="products.php" class="btn-outline booking-again-btn">CONTINUE SHOPPING</a>
            </div>

        <?php elseif (empty($cartItems)): ?>
            <div class="cart-empty">
                <?php if ($cartMessage): ?>
                    <div class="auth-error booking-message"><?= htmlspecialchars($cartMessage, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>
                <i class="bi bi-handbag"></i>
                <h2>Your cart is empty.</h2>
                <p>Browse our curl care essentials and add something you'll love.</p>
                <a href="products.php" class="btn-main">SHOP PRODUCTS</a>
            </div>

        <?php else: ?>
            <?php if ($cartMessage): ?>
                <div class="<?= $cartStatus === 'error' ? 'auth-error' : 'auth-context' ?> booking-message"><?= htmlspecialchars($cartMessage, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <div class="cart-list">
                <?php foreach ($cartItems as $item): ?>
                    <div class="cart-item">

                        <div class="cart-item-img">
                            <img src="<?= htmlspecialchars($item['image'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                        </div>

                        <div class="cart-item-info">
                            <h3><?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                            <span class="cart-item-unit-price"><?= formatPrice($item['price']) ?> each</span>
                            <?php if ((int) $item['quantity'] >= (int) $item['stock_quantity']): ?>
                                <span class="cart-item-stock-note">Max available in stock</span>
                            <?php endif; ?>
                        </div>

                        <form method="post" action="forms/update-cart.php" class="cart-item-qty">
                            <input type="hidden" name="update_quantity" value="1">
                            <input type="hidden" name="product_id" value="<?= (int) $item['product_id'] ?>">
                            <button type="submit" name="quantity" value="<?= max(0, (int) $item['quantity'] - 1) ?>" class="qty-btn" aria-label="Decrease quantity">−</button>
                            <span class="qty-value"><?= (int) $item['quantity'] ?></span>
                            <button type="submit" name="quantity" value="<?= (int) $item['quantity'] + 1 ?>" class="qty-btn" aria-label="Increase quantity" <?= (int) $item['quantity'] >= (int) $item['stock_quantity'] ? 'disabled' : '' ?>>+</button>
                        </form>

                        <div class="cart-item-total"><?= formatPrice((float) $item['price'] * (int) $item['quantity']) ?></div>

                        <form method="post" action="forms/update-cart.php" class="cart-item-remove-form">
                            <button type="submit" name="remove_item" value="<?= (int) $item['product_id'] ?>" class="cart-item-remove" aria-label="Remove <?= htmlspecialchars($item['name'], ENT_QUOTES, 'UTF-8') ?>">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>

                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <div class="cart-summary-row">
                    <span>Subtotal</span>
                    <strong><?= formatPrice($cartTotal) ?></strong>
                </div>
                <p class="cart-summary-note">Taxes and any shipping are calculated at pickup / delivery.</p>
                <form method="post" action="forms/checkout.php" class="js-checkout-form">
                    <input type="hidden" name="place_order" value="1">
                    <button type="submit" class="btn-main cart-checkout-btn">PLACE ORDER <i class="bi bi-arrow-right"></i></button>
                </form>
            </div>
        <?php endif; ?>

    </section>
</main>

<!-- Checkout confirmation modal -->
<div class="modal fade" id="checkoutConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content confirm-modal-content">
            <div class="confirm-modal-icon"><i class="bi bi-bag-check"></i></div>
            <h2>Place this order?</h2>
            <p>Your total is <strong><?= formatPrice($cartTotal) ?></strong>. This will check out your cart.</p>
            <div class="confirm-modal-actions">
                <button type="button" class="btn-outline" data-bs-dismiss="modal">GO BACK</button>
                <button type="button" class="btn-main" id="checkoutConfirmBtn">YES, PLACE ORDER</button>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>

</body>
</html>
