<?php
session_start();
require 'includes/helpers.php';
require 'data/account.php';

if (empty($_SESSION['user_id'])) {
    header('Location: account.php?mode=login');
    exit;
}

$isLoggedIn = true;
$username   = $_SESSION['username'] ?? 'Account';
$bookHref   = bookHref($isLoggedIn);
$userId     = (int) $_SESSION['user_id'];
$account    = getUserAccount($userId);

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account | Curlétte Hair Studio</title>
    <link rel="icon" href="assets/images/C-icon.png" type="image/png">
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
            <h1>My <em>account.</em></h1>
            <p>Keep your details up to date.</p>
        </div>

        <?php if ($status === 'success'): ?>
            <div class="auth-context booking-message">Account updated.</div>
        <?php elseif ($status === 'error' && $message): ?>
            <div class="auth-error booking-message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>

        <div class="booking-section">
            <form action="forms/login.php" method="post" class="booking-form" novalidate>
                <input type="hidden" name="update_account" value="1">

                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?= htmlspecialchars($account['username'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($account['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone" maxlength="11" pattern="09[0-9]{9}" placeholder="09XXXXXXXXX" title="11 digits, starting with 09" value="<?= htmlspecialchars($account['phone'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>

                <button type="submit" class="btn-main">SAVE CHANGES</button>
            </form>
        </div>

    </section>
</main>

<?php include 'partials/footer.php'; ?>
<script src="assets/js/script.js"></script>
</body>
</html>
