<?php
session_start();
require 'includes/helpers.php';

$isLoggedIn = !empty($_SESSION['user_id']);
$username   = $_SESSION['username'] ?? '';
$bookHref   = bookHref($isLoggedIn);

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contact Us — Curlétte Hair Studio</title>
    <link rel="icon" href="assets/C-icon.png" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>">

<?php $navActive = 'contact'; include 'partials/nav.php'; ?>

<main class="booking-page">

    <section class="booking-hero">

        <div class="booking-heading">
            <p class="home-label">GET IN TOUCH</p>
            <h1>Let's talk <em>curls.</em></h1>
            <p>Questions about a service, a product, or just want to say hi? Send us a message and we'll get back to you.</p>
        </div>

        <div class="contact-split">

            <div class="contact-form-col booking-section">

                <?php if ($status === 'success'): ?>
                    <div class="auth-context">Thanks! Your message has been sent — we'll get back to you soon.</div>
                <?php elseif ($status === 'error' && $message): ?>
                    <div class="auth-error"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <form action="forms/send-message.php" method="post" class="booking-form" novalidate>
                    <input type="hidden" name="send_message" value="1">

                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone (optional)</label>
                        <input type="tel" id="phone" name="phone">
                    </div>

                    <div class="form-group">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" rows="5" placeholder="Tell us what's on your mind..." required></textarea>
                    </div>

                    <button type="submit" class="btn-main">SEND MESSAGE</button>
                </form>

            </div>

            <div class="contact-info-col">

                <div class="about-visit-panel contact-info-panel">
                    <div class="about-visit-address">
                        <i class="bi bi-geo-alt"></i>
                        <p>123 Curl Street, Cebu City, 6000 Cebu, Philippines</p>
                    </div>

                    <div class="about-visit-contact contact-info-links">
                        <a href="tel:09123456789"><i class="bi bi-telephone"></i> 0912 345 6789</a>
                        <a href="mailto:curlettehairstudio@email.com"><i class="bi bi-envelope"></i> curlettehairstudio@email.com</a>
                    </div>

                    <p class="contact-response-note">We usually reply within 1 business day.</p>

                    <div class="contact-social">
                        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                    </div>
                </div>

            </div>

        </div>

    </section>

</main>

<?php include 'partials/footer.php'; ?>
<?php include 'partials/auth-modal.php'; ?>

<script src="assets/js/script.js"></script>

</body>
</html>
