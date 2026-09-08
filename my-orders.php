<?php
session_start();
require 'includes/helpers.php';
require 'data/orders.php';

if (empty($_SESSION['user_id'])) {
    header('Location: account.php?mode=login');
    exit;
}

$isLoggedIn = true;
$username   = $_SESSION['username'] ?? 'Account';
$bookHref   = bookHref($isLoggedIn);
$userId     = (int) $_SESSION['user_id'];
$orders     = getUserOrders($userId);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders — Curlétte Hair Studio</title>
    <link rel="icon" href="assets/C-icon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-logged-in="1">

<?php include 'partials/nav.php'; ?>

<main class="booking-page">
    <section class="booking-hero">

        <div class="booking-heading">
            <p class="home-label">YOUR CURLÉTTE ACCOUNT</p>
            <h1>My <em>orders.</em></h1>
            <p>Everything you've ordered from the shop, past and pending.</p>
        </div>

        <?php if (empty($orders)): ?>
            <div class="cart-empty">
                <i class="bi bi-bag-x"></i>
                <h2>No orders yet.</h2>
                <p>Browse our curl care essentials and place your first order.</p>
                <a href="products.php" class="btn-main">SHOP PRODUCTS</a>
            </div>
        <?php else: ?>
            <div class="my-list">
                <?php foreach ($orders as $order): ?>
                    <?php $items = getUserOrderItems((int) $order['id'], $userId); ?>
                    <div class="my-list-item">
                        <div class="my-list-item-header">
                            <div>
                                <h3>Order #<?= (int) $order['id'] ?></h3>
                                <span class="muted">Placed on <?= date('M j, Y', strtotime($order['created_at'])) ?></span>
                            </div>
                            <span class="status-badge <?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>

                        <div class="cart-order-summary">
                            <?php foreach ($items as $item): ?>
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
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </section>
</main>

<?php include 'partials/footer.php'; ?>
<script src="assets/js/script.js"></script>
</body>
</html>
