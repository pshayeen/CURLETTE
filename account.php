<?php
session_start();

require 'redirects.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$mode = ($_GET['mode'] ?? 'login') === 'signup' ? 'signup' : 'login';
$status = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
$redirectKey = (string) ($_GET['redirect'] ?? '');
$redirect = isAllowedRedirect($redirectKey) ? $redirectKey : '';

$contextMessage = $redirect === 'book-appointment'
    ? 'Log in or create an account to book your appointment.'
    : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Account — Curlétte Hair Studio</title>
    <link rel="icon" href="assets/C-icon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/style.css">
</head>
<body>
<header class="header">
    <nav class="nav" aria-label="Main navigation">
        <div class="nav-box">
            <a href="index.php#home" class="logo"><img src="assets/logo.png" alt="Curlétte Hair Studio"></a>
            <ul class="nav-links">
                <li><a href="index.php#home">HOME</a></li>
                <li><a href="index.php#services">SERVICES</a></li>
                <li><a href="index.php#products">PRODUCTS</a></li>
                <li><a href="index.php#about">ABOUT US</a></li>
                <li><a href="index.php#contact">CONTACT</a></li>
            </ul>
            <div class="nav-actions">
                <a href="bookAppointment.php" class="btn-main">BOOK AN APPOINTMENT</a>
                <button type="button" class="icon-btn cart-btn-nav" aria-label="Shopping bag"><i class="bi bi-handbag"></i></button>
                <a href="account.php" class="icon-btn account-btn" aria-label="Account"><i class="bi bi-person-fill"></i></a>
            </div>
        </div>
    </nav>
</header>

<main>
    <section class="auth">
        <div class="auth-card">
            <div class="auth-tabs" role="tablist">
                <a href="?mode=login&redirect=<?= urlencode($redirect) ?>" class="auth-tab <?= $mode === 'login' ? 'active' : '' ?>">LOG IN</a>
                <a href="?mode=signup&redirect=<?= urlencode($redirect) ?>" class="auth-tab <?= $mode === 'signup' ? 'active' : '' ?>">SIGN UP</a>
            </div>

            <?php if ($contextMessage): ?>
                <p class="auth-context"><?= htmlspecialchars($contextMessage, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <?php if ($status === 'error' && $message): ?>
                <p class="auth-error"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></p>
            <?php endif; ?>

            <?php if ($mode === 'login'): ?>
                <h2 class="auth-title">Welcome back.</h2>
                <p class="auth-sub">Log in to manage your appointments and orders.</p>
                <form action="account_function.php" method="post" class="auth-form" autocomplete="off" novalidate>
                    <input type="hidden" name="login" value="1">
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') ?>">
                    <div class="form-group">
                        <label for="login-email">Email</label>
                        <input type="email" id="login-email" name="email" autocomplete="username" required>
                    </div>
                    <div class="form-group">
                        <label for="login-password">Password</label>
                        <input type="password" id="login-password" name="password" autocomplete="new-password" required>
                    </div>
                    <button type="submit" class="btn-main auth-submit">LOG IN</button>
                </form>
            <?php else: ?>
                <h2 class="auth-title">Join Curlétte.</h2>
                <p class="auth-sub">Create an account to book appointments and shop products.</p>
                <form action="account_function.php" method="post" class="auth-form" autocomplete="off" novalidate>
                    <input type="hidden" name="signup" value="1">
                    <input type="hidden" name="redirect" value="<?= htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') ?>">
                    <div class="form-group">
                        <label for="signup-username">Username</label>
                        <input type="text" id="signup-username" name="username" autocomplete="username" required>
                    </div>
                    <div class="form-group">
                        <label for="signup-email">Email</label>
                        <input type="email" id="signup-email" name="email" autocomplete="email" required>
                    </div>
                    <div class="form-group">
                        <label for="signup-password">Password</label>
                        <input type="password" id="signup-password" name="password" autocomplete="new-password"
                               minlength="8" pattern="(?=.*[A-Za-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}"
                               title="Password must be at least 8 characters and include a letter, a number, and a special character." required>
                        <small class="password-hint">At least 8 characters, with a letter, a number, and a special character.</small>
                    </div>
                    <div class="form-group">
                        <label for="signup-confirm">Confirm Password</label>
                        <input type="password" id="signup-confirm" name="confirm_password" autocomplete="new-password" required>
                    </div>
                    <button type="submit" class="btn-main auth-submit">CREATE ACCOUNT</button>
                </form>
            <?php endif; ?>
        </div>
    </section>
</main>

<footer id="contact" class="footer">
    <div class="footer-box">
        <div class="footer-brand">
            <img src="assets/logo.png" alt="Curlétte Hair Studio">
            <p>A curly hair studio dedicated to helping you care for, understand, and love your natural curls.</p>
        </div>
        <div class="footer-col">
            <h3>QUICK LINKS</h3>
            <a href="index.php#home">HOME</a><a href="index.php#services">SERVICES</a>
            <a href="index.php#products">PRODUCTS</a><a href="index.php#about">ABOUT US</a><a href="index.php#contact">CONTACT</a>
        </div>
        <div class="footer-col">
            <h3>CUSTOMER</h3>
            <a href="account.php">MY ACCOUNT</a><a href="account.php?mode=login&redirect=book-appointment">BOOK AN APPOINTMENT</a>
        </div>
        <div class="footer-col">
            <h3>CONTACT US</h3>
            <p><i class="bi bi-telephone"></i> 0912 345 6789</p>
            <p><i class="bi bi-envelope"></i> curlettehairstudio@email.com</p>
            <div class="social"><a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a><a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a></div>
        </div>
    </div>
</footer>
<script src="script.js"></script>
</body>
</html>
