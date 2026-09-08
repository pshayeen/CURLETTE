<?php
// Expects: $isLoggedIn, $username, $bookHref, $navActive (optional), $showBookButton (optional)
$isLoggedIn = $isLoggedIn ?? false;
$username = $username ?? '';
$bookHref = $bookHref ?? '#';
$navActive = $navActive ?? '';
$showBookButton = $showBookButton ?? true;
$cartCount = $isLoggedIn ? getCartItemCount($_SESSION['user_id'] ?? null) : 0;
function navClass(string $key, string $navActive): string
{
    return $key === $navActive ? 'active' : '';
}
?>
<header class="header">
    <nav class="nav" aria-label="Main navigation">
        <div class="nav-box">

            <a href="index.php#home" class="logo">
                <img src="assets/logo.png" alt="Curlette Hair Studio">
            </a>

            <ul class="nav-links">
                <li><a href="index.php#home" class="<?= navClass('home', $navActive) ?>">HOME</a></li>
                <li><a href="services.php" class="<?= navClass('services', $navActive) ?>">SERVICES</a></li>
                <li><a href="products.php" class="<?= navClass('products', $navActive) ?>">PRODUCTS</a></li>
                <li><a href="about.php" class="<?= navClass('about', $navActive) ?>">ABOUT US</a></li>
                <li><a href="contact.php" class="<?= navClass('contact', $navActive) ?>">CONTACT</a></li>
            </ul>

            <div class="nav-actions">
                <?php if ($showBookButton): ?>
                    <a href="<?= $bookHref ?>" class="btn-main js-book-trigger">BOOK AN APPOINTMENT</a>
                <?php endif; ?>

                <a href="<?= $isLoggedIn ? 'cart.php' : 'account.php?mode=login&redirect=cart' ?>" class="icon-btn cart-btn-nav" aria-label="Shopping cart<?= $cartCount > 0 ? ", $cartCount items" : '' ?>">
                    <i class="bi bi-handbag"></i>
                    <?php if ($cartCount > 0): ?>
                        <span class="cart-count-badge"><?= $cartCount > 9 ? '9+' : $cartCount ?></span>
                    <?php endif; ?>
                </a>

                <?php if ($isLoggedIn): ?>
                    <div class="account-menu">
                        <button type="button" class="icon-btn account-btn account-menu-toggle"
                                aria-label="Account menu" aria-expanded="false" aria-haspopup="true">
                            <span class="account-avatar"><?= htmlspecialchars(strtoupper(mb_substr($username !== '' ? $username : 'A', 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>
                            <i class="bi bi-chevron-down account-caret"></i>
                        </button>
                        <div class="account-dropdown" hidden>
                            <div class="account-dropdown-header">
                                <span class="account-dropdown-avatar"><?= htmlspecialchars(strtoupper(mb_substr($username !== '' ? $username : 'A', 0, 1)), ENT_QUOTES, 'UTF-8') ?></span>
                                <div>
                                    <span class="account-dropdown-label">LOGGED IN AS</span>
                                    <strong><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></strong>
                                </div>
                            </div>
                            <a href="account.php?view=account">
                                <i class="bi bi-person"></i>
                                <span>My Account</span>
                            </a>
                            <a href="account.php?view=appointments">
                                <i class="bi bi-calendar-check"></i>
                                <span>My Appointments</span>
                            </a>
                            <div class="account-dropdown-divider"></div>
                            <a href="forms/logout.php" class="logout-link">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Log Out</span>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="account.php" class="icon-btn account-btn js-account-trigger" aria-label="Log in or sign up">
                        <i class="bi bi-person"></i>
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </nav>
</header>
