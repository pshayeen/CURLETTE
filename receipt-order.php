<?php
session_start();
require 'includes/helpers.php';
require 'data/orders.php';

if (empty($_SESSION['user_id'])) {
    header('Location: account.php?mode=login');
    exit;
}

$orderId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$isAdmin = !empty($_SESSION['is_admin']);

if (!$orderId) {
    header('Location: my-orders.php');
    exit;
}

$order = getOrderById($orderId, $isAdmin ? null : (int) $_SESSION['user_id']);

if (!$order) {
    header('Location: my-orders.php');
    exit;
}

$items = getUserOrderItems($orderId, (int) $order['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt — Order #<?= (int) $order['id'] ?></title>
    <link rel="icon" href="assets/images/C-icon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-logged-in="1">

<div class="receipt-page">
    <div class="receipt">

        <div class="receipt-header">
            <img src="assets/images/logo.png" alt="Curlétte Hair Studio">
            <p>123 Curl Street, Cebu City, 6000 Cebu, Philippines</p>
            <p>0912 345 6789 &middot; curlettehairstudio@email.com</p>
        </div>

        <h1>Order Receipt</h1>

        <div class="receipt-meta">
            <div><span>Order</span><strong>#<?= (int) $order['id'] ?></strong></div>
            <div><span>Date</span><strong><?= date('F j, Y', strtotime($order['created_at'])) ?></strong></div>
            <div><span>Customer</span><strong><?= htmlspecialchars($order['full_name'], ENT_QUOTES, 'UTF-8') ?></strong></div>
            <div><span>Status</span><strong><?= htmlspecialchars(ucfirst($order['status']), ENT_QUOTES, 'UTF-8') ?></strong></div>
        </div>

        <table class="receipt-items">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td><?= (int) $item['quantity'] ?></td>
                        <td><?= formatPrice($item['unit_price']) ?></td>
                        <td><?= formatPrice((float) $item['unit_price'] * (int) $item['quantity']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="receipt-total">
            <span>Total</span>
            <strong><?= formatPrice($order['total']) ?></strong>
        </div>

        <p class="receipt-payment">
            Paid via <?= paymentMethodLabel($order['payment_method']) ?>
            <?php if ($order['payment_reference']): ?> &mdash; Ref# <?= htmlspecialchars($order['payment_reference'], ENT_QUOTES, 'UTF-8') ?><?php endif; ?>
        </p>

        <p class="receipt-footer-note">Thank you for shopping with Curlétte Hair Studio.</p>

        <div class="receipt-actions">
            <button type="button" onclick="window.print()" class="btn-main">PRINT RECEIPT</button>
            <a href="my-orders.php" class="btn-outline">BACK TO MY ORDERS</a>
        </div>

    </div>
</div>

</body>
</html>
