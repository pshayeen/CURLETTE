<?php
session_start();
require 'helpers.php';
require 'data/products.php';

$isLoggedIn = !empty($_SESSION['user_id']);
$username   = $_SESSION['username'] ?? '';
$bookHref   = bookHref($isLoggedIn);
$products   = getProducts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Shop Products — Curlétte Hair Studio</title>
    <link rel="icon" href="assets/C-icon.png" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="style/style.css">
</head>

<body data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>">

<?php $navActive = 'products'; include 'partials/nav.php'; ?>

<main class="booking-page">

    <section class="booking-hero">

        <div class="booking-heading">
            <p class="home-label">CURLÉTTE PRODUCTS</p>
            <h1>Curl care <em>essentials.</em></h1>
            <p>Everything you need to cleanse, hydrate, define, and protect your curls — picked for every step of your routine.</p>
        </div>

        <div class="product-grid products-page-grid">

            <?php foreach ($products as $product): ?>
                <?php $detailed = true; $returnTo = 'products'; include 'partials/product-card.php'; ?>
            <?php endforeach; ?>

        </div>

    </section>

</main>

<?php include 'partials/footer.php'; ?>
<?php include 'partials/auth-modal.php'; ?>

<script src="script.js"></script>

</body>
</html>
