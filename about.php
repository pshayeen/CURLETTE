<?php
session_start();
require 'includes/helpers.php';

$isLoggedIn = !empty($_SESSION['user_id']);
$username   = $_SESSION['username'] ?? '';
$bookHref   = bookHref($isLoggedIn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>About Us | Curlétte Hair Studio</title>
    <link rel="icon" href="assets/images/C-icon.png" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>">

<?php $navActive = 'about'; include 'partials/nav.php'; ?>

<main class="booking-page">

    <section class="booking-hero about-hero">

        <img src="assets/images/salon.jpg" alt="" class="section-bg">
        <div class="about-hero-overlay"></div>

        <div class="booking-heading">
            <p class="home-label">ABOUT CURLÉTTE</p>
            <h1>More than a salon.<br><em>A home for your curls.</em></h1>
            <p>We're on a mission to help every curl pattern feel seen, understood, and beautifully cared for — one appointment at a time.</p>
        </div>

    </section>

    <!-- OUR STORY -->
    <section class="about-story">
        <div class="about-box about-box--auto">

            <div class="about-text">
                <span>OUR STORY</span>

                <h2>How Curlétte<br><em>began.</em></h2>

                <p>
                    Curlétte started with a simple frustration: too many curly-haired
                    clients left salons feeling like an afterthought — cut dry, styled
                    straight, and sent home with products that were never made for
                    their texture. We wanted something different.
                </p>

                <p>
                    So we built a studio where curls are the standard, not the
                    exception. Every stylist here trains specifically in curl-cutting
                    and curl-styling techniques, and every product on our shelves
                    earns its place because it actually works for textured hair.
                </p>

                <p>
                    Today, Curlétte is a space where you can sit down, take a breath,
                    and let your natural texture do the talking.
                </p>

                <a href="<?= $bookHref ?>" class="btn-main js-book-trigger">
                    BOOK YOUR VISIT
                </a>
            </div>

            <div class="about-img about-img--right">
                <img src="assets/images/model.png" alt="Curlétte client with healthy curls">
            </div>

        </div>
    </section>

    <!-- TEAM -->
    <section class="about-team">

        <div class="section-box">
            <header class="section-title">
                <h2>THE HANDS BEHIND THE CHAIR</h2>
                <p>Meet the people who'll actually be cutting your hair.</p>
            </header>
        </div>

        <div class="about-team-rows">

            <div class="about-box about-box--auto about-box--reverse">
                <div class="about-img">
                    <img src="assets/images/sample-pic3.jpg" alt="Shy Alonso">
                </div>
                <div class="about-text about-text--person">
                    <span>FOUNDER &amp; MASTER STYLIST</span>
                    <h2>Shy Alonso</h2>
                    <p>Started Curlétte after years of cutting curls dry in her own kitchen for friends who'd given up on salons. Still does the first cut for every new client.</p>
                </div>
            </div>

            <div class="about-box about-box--auto">
                <div class="about-text about-text--person">
                    <span>CURL SPECIALIST</span>
                    <h2>Ren Cruz</h2>
                    <p>Trained in curl-by-curl cutting and lives for a good curly bob transformation. If you're nervous about cutting your curls, ask for Ren.</p>
                </div>
                <div class="about-img">
                    <img src="assets/images/sample-pic2.jpg" alt="Ren Cruz">
                </div>
            </div>

            <div class="about-box about-box--auto about-box--reverse">
                <div class="about-img">
                    <img src="assets/images/sample-pic1.jpg" alt="Leah Santos">
                </div>
                <div class="about-text about-text--person">
                    <span>COLORIST</span>
                    <h2>Leah Santos</h2>
                    <p>Specializes in color that grows out gracefully — balayage and money-piece highlights using curl-safe formulas that won't loosen your pattern.</p>
                </div>
            </div>

            <div class="about-box about-box--auto">
                <div class="about-text about-text--person">
                    <span>CURL SPECIALIST &amp; EDUCATOR</span>
                    <h2>Bea Rodriguez</h2>
                    <p>Runs our styling workshops and will absolutely rewrite how you think about the "plopping" technique.</p>
                </div>
                <div class="about-img">
                    <img src="assets/images/sample-pic.jpg" alt="Bea Rodriguez">
                </div>
            </div>

        </div>

    </section>

    <!-- VISIT US -->
    <section class="about-visit">

        <img src="assets/images/bg.png" alt="" class="section-bg">

        <div class="about-visit-content">
            <div class="about-visit-panel">
                <header class="section-title">
                    <h2>VISIT US</h2>
                    <p>Come say hello.</p>
                </header>

                <div class="about-visit-address">
                    <i class="bi bi-geo-alt"></i>
                    <p>123 Curl Street, Cebu City, 6000 Cebu, Philippines</p>
                </div>

                <div class="about-visit-contact">
                    <a href="tel:09123456789"><i class="bi bi-telephone"></i> 0912 345 6789</a>
                    <a href="mailto:curlettehairstudio@email.com"><i class="bi bi-envelope"></i> curlettehairstudio@email.com</a>
                </div>
            </div>
        </div>

    </section>

</main>

<?php include 'partials/cta-banner.php'; ?>
<?php include 'partials/footer.php'; ?>
<?php include 'partials/auth-modal.php'; ?>

<script src="assets/js/script.js"></script>

</body>
</html>
