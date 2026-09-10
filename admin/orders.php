<?php
require 'guard.php';
require '../data/admin.php';
require '../includes/helpers.php';

$adminActive = 'orders';
$orders = getAllOrders();
$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders | Admin | Curlétte</title>
    <link rel="icon" href="../assets/images/C-icon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500,600;1,500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'partials/nav.php'; ?>

<main class="admin-main">

    <div class="admin-page-header">
        <div>
            <h1>Orders</h1>
            <p><?= count($orders) ?> total order<?= count($orders) === 1 ? '' : 's' ?>.</p>
        </div>
    </div>

    <?php if ($status === 'success'): ?>
        <div class="admin-notice success">Order updated.</div>
    <?php elseif ($status === 'error' && $message): ?>
        <div class="admin-notice error"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (empty($orders)): ?>
        <div class="admin-table-wrap">
            <p class="admin-empty">No orders have been placed yet.</p>
        </div>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Customer</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <?php $items = getOrderItems((int) $order['id']); ?>
                        <tr>
                            <td><strong>#<?= (int) $order['id'] ?></strong></td>
                            <td>
                                <strong><?= htmlspecialchars($order['username'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <span class="muted"><?= htmlspecialchars($order['email'], ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td>
                                <?php foreach ($items as $item): ?>
                                    <div><?= htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8') ?> × <?= (int) $item['quantity'] ?></div>
                                <?php endforeach; ?>
                            </td>
                            <td><strong><?= formatPrice($order['total']) ?></strong></td>
                            <td>
                                <strong><?= paymentMethodLabel($order['payment_method']) ?></strong>
                            </td>
                            <td><span class="muted"><?= date('M j, Y', strtotime($order['created_at'])) ?></span></td>
                            <td><span class="admin-badge <?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($order['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td>
                                <form method="post" action="update-order-status.php" class="admin-status-form">
                                    <input type="hidden" name="order_id" value="<?= (int) $order['id'] ?>">
                                    <select name="status">
                                        <option value="placed" <?= $order['status'] === 'placed' ? 'selected' : '' ?>>Placed</option>
                                        <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="fulfilled" <?= $order['status'] === 'fulfilled' ? 'selected' : '' ?>>Fulfilled</option>
                                        <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <button type="submit">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</main>

</body>
</html>
