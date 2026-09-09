<?php
require 'guard.php';
require '../data/admin.php';

$adminActive = 'customers';
$search = trim($_GET['search'] ?? '');
$customers = getAllCustomers($search);
$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customers | Admin | Curlétte</title>
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
            <h1>Customers</h1>
            <p><?= count($customers) ?> customer account<?= count($customers) === 1 ? '' : 's' ?>.</p>
        </div>
    </div>

    <?php if ($status === 'success' && $message): ?>
        <div class="admin-notice success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php elseif ($status === 'error' && $message): ?>
        <div class="admin-notice error"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <form method="get" class="admin-search-form">
        <input type="text" name="search" placeholder="Search by username or email" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
        <button type="submit">Search</button>
        <?php if ($search !== ''): ?>
            <a href="customers.php" class="admin-link">Clear</a>
        <?php endif; ?>
    </form>

    <?php if (empty($customers)): ?>
        <div class="admin-table-wrap">
            <p class="admin-empty">No customer accounts found.</p>
        </div>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Joined</th>
                        <th>Orders</th>
                        <th>Appointments</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <?php $formId = 'edit-customer-' . (int) $customer['id']; ?>
                        <tr>
                            <td>
                                <form id="<?= $formId ?>" method="post" action="customer-actions.php">
                                    <input type="hidden" name="update_info" value="1">
                                </form>
                                <input type="hidden" name="customer_id" value="<?= (int) $customer['id'] ?>" form="<?= $formId ?>">
                                <input type="text" name="username" class="admin-inline-input" maxlength="20" required
                                       value="<?= htmlspecialchars($customer['username'], ENT_QUOTES, 'UTF-8') ?>" form="<?= $formId ?>">
                            </td>
                            <td>
                                <input type="email" name="email" class="admin-inline-input" required
                                       value="<?= htmlspecialchars($customer['email'], ENT_QUOTES, 'UTF-8') ?>" form="<?= $formId ?>">
                            </td>
                            <td><span class="muted"><?= date('M j, Y', strtotime($customer['created_at'])) ?></span></td>
                            <td><?= (int) $customer['order_count'] ?></td>
                            <td><?= (int) $customer['appointment_count'] ?></td>
                            <td>
                                <div class="admin-customer-actions">
                                    <button type="submit" form="<?= $formId ?>" class="admin-btn-outline">Save</button>
                                    <form method="post" action="customer-actions.php" onsubmit="return confirm('Delete this account? This also removes their orders and appointments. This can\'t be undone.');">
                                        <input type="hidden" name="delete_customer" value="1">
                                        <input type="hidden" name="customer_id" value="<?= (int) $customer['id'] ?>">
                                        <button type="submit" class="admin-btn-danger">Delete</button>
                                    </form>
                                </div>
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
