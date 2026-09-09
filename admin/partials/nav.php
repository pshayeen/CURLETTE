<?php
$adminActive = $adminActive ?? '';
function adminNavClass(string $key, string $adminActive): string
{
    return $key === $adminActive ? 'active' : '';
}
?>
<header class="admin-header">
    <div class="admin-header-inner">

        <a href="dashboard.php" class="admin-logo">
            <img src="../assets/logo.png" alt="Curlétte">
            <span>ADMIN</span>
        </a>

        <nav class="admin-nav" aria-label="Admin navigation">
            <a href="dashboard.php" class="<?= adminNavClass('dashboard', $adminActive) ?>">Dashboard</a>
            <a href="orders.php" class="<?= adminNavClass('orders', $adminActive) ?>">Orders</a>
            <a href="appointments.php" class="<?= adminNavClass('appointments', $adminActive) ?>">Appointments</a>
            <a href="products.php" class="<?= adminNavClass('products', $adminActive) ?>">Products</a>
            <a href="messages.php" class="<?= adminNavClass('messages', $adminActive) ?>">Messages</a>
        </nav>

        <div class="admin-header-actions">
            <div class="admin-user">
                <span class="admin-avatar"><?= htmlspecialchars(strtoupper(mb_substr($adminUsername !== '' ? $adminUsername : 'A', 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>
                <span class="admin-username"><?= htmlspecialchars($adminUsername, ENT_QUOTES, 'UTF-8') ?></span>
            </div>
            <a href="../index.php" class="admin-link">View Site</a>
            <form method="post" action="../forms/logout.php" class="admin-logout-form">
                <button type="submit" class="admin-logout-btn">
                    <i class="bi bi-box-arrow-right"></i> Log Out
                </button>
            </form>
        </div>

    </div>
</header>
