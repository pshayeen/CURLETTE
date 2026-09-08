<?php
session_start();
require 'helpers.php';

$isLoggedIn = !empty($_SESSION['user_id']);
$username   = $_SESSION['username'] ?? '';
$bookHref   = bookHref($isLoggedIn);
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

<?php $navActive = 'home'; include 'partials/nav.php'; ?>

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

                <a href="services.php" class="service-card">
                    <div class="card-img">
                        <img src="assets/service.jpg" alt="Curl consultation">
                    </div>

                    <div class="card-info">
                        <h3>Curl Consultation</h3>
                        <p>Personalized guidance for your natural curls.</p>
                        <strong>$123</strong>
                    </div>
                </a>

                <a href="services.php" class="service-card">
                    <div class="card-img">
                        <img src="assets/service1.jpg" alt="Curl styling">
                    </div>

                    <div class="card-info">
                        <h3>Curl Styling</h3>
                        <p>Professional styling designed for your curl pattern.</p>
                        <strong>$123</strong>
                    </div>
                </a>

                <a href="services.php" class="service-card">
                    <div class="card-img">
                        <img src="assets/service3.jpg" alt="Curly hair cut">
                    </div>

                    <div class="card-info">
                        <h3>Curl Hair Cut</h3>
                        <p>A cut shaped specifically for your natural texture.</p>
                        <strong>$123</strong>
                    </div>
                </a>

                <a href="services.php" class="service-card">
                    <div class="card-img">
                        <img src="assets/service2.jpg" alt="Curly hair color">
                    </div>

                    <div class="card-info">
                        <h3>Curl Hair Color</h3>
                        <p>Beautiful color while keeping your curls healthy.</p>
                        <strong>$123</strong>
                    </div>
                </a>

            </div>

            <div class="center-btn">
                <a href="services.php" class="btn-outline btn-small">
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

            <!-- Duplicate set below: required for the seamless infinite marquee (.testimonial-track animates to -50%, so the track needs two identical copies back-to-back) -->

            <article class="testimonial-card" aria-hidden="true">

                <div class="testimonial-img">
                    <img src="assets/c5.jpg" alt="" role="presentation">
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

            <article class="testimonial-card" aria-hidden="true">

                <div class="testimonial-img">
                    <img src="assets/c1.jpg" alt="" role="presentation">
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

            <article class="testimonial-card" aria-hidden="true">

                <div class="testimonial-img">
                    <img src="assets/c2.jpg" alt="" role="presentation">
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

            <article class="testimonial-card" aria-hidden="true">

                <div class="testimonial-img">
                    <img src="assets/c3.jpg" alt="" role="presentation">
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

            <article class="testimonial-card" aria-hidden="true">

                <div class="testimonial-img">
                    <img src="assets/c4.jpg" alt="" role="presentation">
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


    <?php include 'partials/cta-banner.php'; ?>

</main>


<?php include 'partials/footer.php'; ?>
<?php include 'partials/auth-modal.php'; ?>

<script src="script.js"></script>


</body>
</html>