<?php
session_start();
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');
$isLoggedIn = !empty($_SESSION['user_id']);
$username   = $_SESSION['username'] ?? '';

$bookHref = $isLoggedIn ? 'bookAppointment.php' : 'account.php?mode=login&redirect=book-appointment';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Curlétte Hair Studio</title>
    <link rel="icon" href="assets/C-icon.png" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="style/style.css">
</head>

<body data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>">

<header class="header">
    <nav class="nav" aria-label="Main navigation">
        <div class="nav-box">

            <a href="#home" class="logo">
                <img src="assets/logo.png" alt="Curlette Hair Studio">
            </a>

            <ul class="nav-links">
                <li><a href="#home" class="active">HOME</a></li>
                <li><a href="#services">SERVICES</a></li>
                <li><a href="#products">PRODUCTS</a></li>
                <li><a href="#about">ABOUT US</a></li>
                <li><a href="#contact">CONTACT</a></li>
            </ul>

            <div class="nav-actions">
                <a href="<?= $bookHref ?>" class="btn-main js-book-trigger">BOOK AN APPOINTMENT</a>

                <button type="button" class="icon-btn cart-btn-nav" aria-label="Shopping bag">
                    <i class="bi bi-handbag"></i>
                </button>

                <?php if ($isLoggedIn): ?>
                    <div class="account-menu">
                        <button type="button" class="icon-btn account-btn account-menu-toggle"
                                aria-label="Account menu" aria-expanded="false" aria-haspopup="true">
                            <i class="bi bi-person-fill"></i>
                        </button>
                        <div class="account-dropdown" hidden>
                            <div class="account-dropdown-header">
                                <span>ACCOUNT</span>
                                <strong><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></strong>
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
                            <a href="logout.php" class="logout-link">
                                <i class="bi bi-box-arrow-right"></i>
                                <span>Log Out</span>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="account.php" class="icon-btn account-btn js-account-trigger" aria-label="Account">
                        <i class="bi bi-person"></i>
                    </a>
                <?php endif; ?>
            </div>

        </div>
    </nav>
</header>

<main>

    <!-- HOME -->
    <section id="home" class="home">

        <img src="assets/model.png" alt="" class="home-bg">

        <div class="home-overlay"></div>

        <div class="home-box">
            <div class="home-content">

                <p class="home-label">MADE FOR YOUR CURLS</p>

                <h1>
                    Healthy curls,<br>
                    <em>confident you.</em>
                </h1>

                <p class="home-text">
                    Your curls deserve the best care. 
                    We offer personalized services and products 
                    to help you bring out their natural beauty.
                </p>

                <div class="home-buttons">
                    <a href="<?= $bookHref ?>" class="btn-main js-book-trigger">
                        BOOK AN APPOINTMENT
                    </a>

                    <a href="#products" class="btn-outline">
                        SHOP PRODUCTS
                    </a>
                </div>

            </div>
        </div>

    </section>


    <!-- SERVICES -->
    <section id="services" class="services">

        <img src="assets/bg.png" alt="" class="section-bg">

        <div class="section-box">

            <header class="section-title">
                <h2>OUR SERVICES</h2>
                <p>Personalized treatments for every curl.</p>
            </header>

            <div class="service-grid">

                <article class="service-card">
                    <div class="card-img">
                        <img src="assets/service.jpg" alt="Curl consultation">
                    </div>

                    <div class="card-info">
                        <h3>Curl Consultation</h3>
                        <p>Personalized guidance for your natural curls.</p>
                        <strong>$123</strong>
                    </div>
                </article>

                <article class="service-card">
                    <div class="card-img">
                        <img src="assets/service1.jpg" alt="Curl styling">
                    </div>

                    <div class="card-info">
                        <h3>Curl Styling</h3>
                        <p>Professional styling designed for your curl pattern.</p>
                        <strong>$123</strong>
                    </div>
                </article>

                <article class="service-card">
                    <div class="card-img">
                        <img src="assets/service3.jpg" alt="Curly hair cut">
                    </div>

                    <div class="card-info">
                        <h3>Curl Hair Cut</h3>
                        <p>A cut shaped specifically for your natural texture.</p>
                        <strong>$123</strong>
                    </div>
                </article>

                <article class="service-card">
                    <div class="card-img">
                        <img src="assets/service2.jpg" alt="Curly hair color">
                    </div>

                    <div class="card-info">
                        <h3>Curl Hair Color</h3>
                        <p>Beautiful color while keeping your curls healthy.</p>
                        <strong>$123</strong>
                    </div>
                </article>

            </div>

            <div class="center-btn">
                <a href="#services" class="btn-outline btn-small">
                    VIEW ALL SERVICES
                </a>
            </div>

        </div>

    </section>


    <!-- PRODUCTS -->
    <section id="products" class="products">

        <div class="products-box">

            <div class="products-text">

                <span>CURLÉTTE PRODUCTS</span>

                <h2>
                    Curl care<br>
                    <em>essentials.</em>
                </h2>

                <p>
                    Curl-friendly essentials made to nourish,
                    define, and protect your natural texture.
                </p>

                <a href="#products" class="btn-main">
                    VIEW ALL PRODUCTS
                </a>

            </div>

            <div class="product-grid">

                <article class="product-card">

                    <div class="product-img">
                        <img src="assets/prod.png" alt="Curl cleansing conditioner">
                    </div>

                    <div class="product-info">
                        <h3>Curl Cleansing Conditioner</h3>
                        <strong>$20</strong>

                        <button class="cart-btn" type="button" aria-label="Add Curl Cleansing Conditioner to cart">
                            <i class="bi bi-handbag"></i>
                        </button>
                    </div>

                </article>


                <article class="product-card">

                    <div class="product-img">
                        <img src="assets/prod2.png" alt="Moisturising conditioner">
                    </div>

                    <div class="product-info">
                        <h3>Moisturising Conditioner</h3>
                        <strong>$20</strong>

                        <button class="cart-btn" type="button" aria-label="Add Moisturising Conditioner to cart">
                            <i class="bi bi-handbag"></i>
                        </button>
                    </div>

                </article>


                <article class="product-card">

                    <div class="product-img">
                        <img src="assets/prod4.png" alt="Curl moisturising treatment">
                    </div>

                    <div class="product-info">
                        <h3>Curl Moisturising Treatment</h3>
                        <strong>$25</strong>

                        <button class="cart-btn" type="button" aria-label="Add Curl Moisturising Treatment to cart">
                            <i class="bi bi-handbag"></i>
                        </button>
                    </div>

                </article>


                <article class="product-card">

                    <div class="product-img">
                        <img src="assets/prod5.png" alt="Curl protein treatment">
                    </div>

                    <div class="product-info">
                        <h3>Curl Protein Treatment</h3>
                        <strong>$25</strong>

                        <button class="cart-btn" type="button" aria-label="Add Curl Protein Treatment to cart">
                            <i class="bi bi-handbag"></i>
                        </button>
                    </div>

                </article>

            </div>

        </div>

    </section>


    <!-- ABOUT -->
    <section id="about" class="about">

        <div class="about-box">

            <div class="about-text">

                <span>ABOUT CURLÉTTE</span>

                <h2>
                    More than a salon.<br>
                    <em>A home for your curls.</em>
                </h2>

                <p>
                    At Curlétte Hair Studio, we believe that every curl is unique
                    and deserves the right care. We created Curlétte as a welcoming
                    space where people with curly hair can feel empowered,
                    understood, and confident in their natural texture.
                </p>

                <a href="#about" class="btn-outline btn-small">
                    LEARN MORE ABOUT US
                </a>

            </div>

            <div class="about-img">
                <img src="assets/salon.jpg" alt="Curlétte Hair Studio">
            </div>

        </div>

    </section>


    <!-- TESTIMONIALS -->
    <section class="testimonials">

        <img src="assets/pattern.png" alt="" class="pattern">
        <img src="assets/pattern.png" alt="" class="pattern2">

        <header class="testimonial-title">

            <h2>
                What our clients say about us?
            </h2>

        </header>


        <div class="testimonial-track">

            <article class="testimonial-card">

                <div class="testimonial-img">
                    <img src="assets/c5.jpg" alt="Curlétte client">
                </div>

                <div class="testimonial-info">

                    <div class="stars" aria-label="5 out of 5 stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>

                    <p>
                        "My curls have never looked this defined.
                        I also loved how they taught me to care for them."
                    </p>

                    <strong>— Yelena V.</strong>

                </div>

            </article>

            <article class="testimonial-card">

                <div class="testimonial-img">
                    <img src="assets/c1.jpg" alt="Curlétte client">
                </div>

                <div class="testimonial-info">

                    <div class="stars" aria-label="5 out of 5 stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>

                    <p>
                        "Everyone was so welcoming.
                        I loved that they didn't try to change my natural curls."
                    </p>

                    <strong>— Amara S.</strong>

                </div>

            </article>

            <article class="testimonial-card">

                <div class="testimonial-img">
                    <img src="assets/c2.jpg" alt="Curlétte client">
                </div>

                <div class="testimonial-info">

                    <div class="stars" aria-label="5 out of 5 stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>

                    <p>
                        "Finally found a salon that understands curly hair.
                        The result was amazing."
                    </p>

                    <strong>— Nicole M.</strong>

                </div>

            </article>

            <article class="testimonial-card">

                <div class="testimonial-img">
                    <img src="assets/c3.jpg" alt="Curlétte client">
                </div>

                <div class="testimonial-info">

                    <div class="stars" aria-label="5 out of 5 stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>

                    <p>
                        "The whole experience was relaxing,
                        and my curls felt healthier after my appointment."
                    </p>

                    <strong>— Sofia R.</strong>

                </div>

            </article>

            <article class="testimonial-card">

                <div class="testimonial-img">
                    <img src="assets/c4.jpg" alt="Curlette client">
                </div>

                <div class="testimonial-info">

                    <div class="stars" aria-label="5 out of 5 stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>

                    <p>
                        "I finally understand how to care for my natural curls.
                        I'll definitely be coming back."
                    </p>

                    <strong>— Clara M.</strong>

                </div>

            </article>

            <!-- DUPLICATES -->

                <article class="testimonial-card">

                <div class="testimonial-img">
                    <img src="assets/c5.jpg" alt="Curlétte client">
                </div>

                <div class="testimonial-info">

                    <div class="stars" aria-label="5 out of 5 stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>

                    <p>
                        "My curls have never looked this defined.
                        I also loved how they taught me to care for them."
                    </p>

                    <strong>— Yelena V.</strong>

                </div>

            </article>

            <article class="testimonial-card">

                <div class="testimonial-img">
                    <img src="assets/c1.jpg" alt="Curlétte client">
                </div>

                <div class="testimonial-info">

                    <div class="stars" aria-label="5 out of 5 stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>

                    <p>
                        "Everyone was so welcoming.
                        I loved that they didn't try to change my natural curls."
                    </p>

                    <strong>— Amara S.</strong>

                </div>

            </article>

            <article class="testimonial-card">

                <div class="testimonial-img">
                    <img src="assets/c2.jpg" alt="Curlétte client">
                </div>

                <div class="testimonial-info">

                    <div class="stars" aria-label="5 out of 5 stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>

                    <p>
                        "Finally found a salon that understands curly hair.
                        The result was amazing."
                    </p>

                    <strong>— Nicole M.</strong>

                </div>

            </article>

            <article class="testimonial-card">

                <div class="testimonial-img">
                    <img src="assets/c3.jpg" alt="Curlétte client">
                </div>

                <div class="testimonial-info">

                    <div class="stars" aria-label="5 out of 5 stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>

                    <p>
                        "The whole experience was relaxing,
                        and my curls felt healthier after my appointment."
                    </p>

                    <strong>— Sofia R.</strong>

                </div>

            </article>

            <article class="testimonial-card">

                <div class="testimonial-img">
                    <img src="assets/c4.jpg" alt="Curlette client">
                </div>

                <div class="testimonial-info">

                    <div class="stars" aria-label="5 out of 5 stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>

                    <p>
                        "I finally understand how to care for my natural curls.
                        I'll definitely be coming back."
                    </p>

                    <strong>— Clara M.</strong>

                </div>

            </article>

        </div>

    </section>


    <!-- APPOINTMENT -->
    <section id="appointment" class="cta">

        <img src="assets/cta-bg.png" alt="" class="cta-bg">

        <div class="cta-overlay"></div>
        <div class="cta-content">

            <h2>Ready to love your curls?</h2>
            <p>Book your appointment today!</p>

            <a href="<?= $bookHref ?>" class="btn-main js-book-trigger">
                BOOK AN APPOINTMENT
            </a>

        </div>

    </section>

</main>


<footer id="contact" class="footer">

    <div class="footer-box">

        <div class="footer-brand">

            <img src="assets/logo.png" alt="Curlette Hair Studio">

            <p>
                A curly hair studio dedicated to helping you care for,
                understand, and love your natural curls.
            </p>

        </div>


        <div class="footer-col">

            <h3>QUICK LINKS</h3>

            <a href="#home">HOME</a>
            <a href="#services">SERVICES</a>
            <a href="#products">PRODUCTS</a>
            <a href="#about">ABOUT US</a>
            <a href="#contact">CONTACT</a>

        </div>


        <div class="footer-col">

            <h3>CUSTOMER</h3>

            <a href="account.php">MY ACCOUNT</a>
            <a href="#">MY ORDERS</a>

        </div>


        <div class="footer-col">

            <h3>CONTACT US</h3>

            <p>
                <i class="bi bi-telephone"></i>
                0912 345 6789
            </p>

            <p>
                <i class="bi bi-envelope"></i>
                curlettehairstudio@email.com
            </p>

            <div class="social">

                <a href="#" aria-label="Facebook">
                    <i class="bi bi-facebook"></i>
                </a>

                <a href="#" aria-label="Instagram">
                    <i class="bi bi-instagram"></i>
                </a>

            </div>

        </div>

    </div>

</footer>

<!-- ACCOUNT MODAL -->
<div class="modal-overlay" id="authModal" aria-hidden="true">
    <div class="modal-box auth-card" role="dialog" aria-modal="true" aria-labelledby="authModalTitle">

        <button type="button" class="modal-close" id="authModalClose" aria-label="Close">
            <i class="bi bi-x-lg"></i>
        </button>

        <div class="auth-tabs" role="tablist">
            <button type="button" class="auth-tab active" data-tab="login" role="tab" aria-selected="true">LOG IN</button>
            <button type="button" class="auth-tab" data-tab="signup" role="tab" aria-selected="false">SIGN UP</button>
        </div>

        <p class="auth-context" id="authModalContext" hidden></p>
        <p class="auth-error" id="authModalError" hidden></p>

        <div class="auth-panel" data-panel="login">
            <h2 class="auth-title" id="authModalTitle">Welcome back.</h2>
            <p class="auth-sub">Log in to manage your appointments and orders.</p>

            <form action="account_function.php" method="post" class="auth-form" novalidate data-ajax-form>
                <input type="hidden" name="login" value="1">
                <input type="hidden" name="redirect" value="" class="js-redirect-field">

                <div class="form-group">
                    <label for="modal-login-email">Email</label>
                    <input type="email" id="modal-login-email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="modal-login-password">Password</label>
                    <input type="password" id="modal-login-password" name="password" autocomplete="new-password" required>
                </div>

                <button type="submit" class="btn-main auth-submit">LOG IN</button>
            </form>
        </div>

        <div class="auth-panel" data-panel="signup" hidden>
            <h2 class="auth-title">Join Curlétte.</h2>
            <p class="auth-sub">Create an account to book appointments and shop products.</p>

            <form action="account_function.php" method="post" class="auth-form" novalidate data-ajax-form>
                <input type="hidden" name="signup" value="1">
                <input type="hidden" name="redirect" value="" class="js-redirect-field">

                <div class="form-group">
                    <label for="modal-signup-username">Username</label>
                    <input type="text" id="modal-signup-username" name="username" required>
                </div>

                <div class="form-group">
                    <label for="modal-signup-email">Email</label>
                    <input type="email" id="modal-signup-email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="modal-signup-password">Password</label>
                    <input type="password" id="modal-signup-password" name="password" autocomplete="new-password" minlength="8" pattern="(?=.*[A-Za-z])(?=.*[0-9])(?=.*[^A-Za-z0-9]).{8,}" title="Password must be at least 8 characters and include a letter, a number, and a special character." required>
                    <small class="password-hint">At least 8 characters, with a letter, a number, and a special character.</small>
                </div>

                <div class="form-group">
                    <label for="modal-signup-confirm">Confirm Password</label>
                    <input type="password" id="modal-signup-confirm" name="confirm_password" autocomplete="new-password" required>
                </div>

                <button type="submit" class="btn-main auth-submit">CREATE ACCOUNT</button>
            </form>
        </div>

    </div>
</div>

<script src="script.js"></script>


</body>
</html>