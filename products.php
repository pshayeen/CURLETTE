<?php
session_start();
require 'includes/helpers.php';
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

    <title>Shop Products | Curlétte Hair Studio</title>
    <link rel="icon" href="assets/C-icon.png" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>">

<?php $navActive = 'products'; include 'partials/nav.php'; ?>

<main class="booking-page">

    <section class="booking-hero products-hero">

        <img src="assets/bg.png" alt="" class="section-bg">

        <div class="booking-heading">
            <p class="home-label">CURLÉTTE PRODUCTS</p>
            <h1>Curl care <em>essentials.</em></h1>
            <p>Everything you need to cleanse, hydrate, define, and protect your curls — picked for every step of your routine.</p>
        </div>

        <div class="products-panel">
            <div class="product-grid">

                <?php foreach ($products as $product): ?>
                    <?php $returnTo = 'products'; $showDetailsTrigger = true; include 'partials/product-card.php'; ?>
                <?php endforeach; ?>

            </div>
        </div>

    </section>

</main>

<?php foreach ($products as $product): ?>
    <div class="modal fade detail-modal" id="productModal<?= (int) $product['id'] ?>" tabindex="-1" aria-labelledby="productModalLabel<?= (int) $product['id'] ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content detail-modal-content">
                <button type="button" class="btn-close detail-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="detail-modal-img detail-modal-img--contain">
                    <img src="<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                </div>
                <div class="detail-modal-body">
                    <p class="booking-eyebrow">CURLÉTTE PRODUCT</p>
                    <h2 id="productModalLabel<?= (int) $product['id'] ?>"><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <p class="detail-modal-description"><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="detail-modal-meta">
                        <div>
                            <span>PRICE</span>
                            <strong><?= formatPrice($product['price']) ?></strong>
                        </div>
                        <div>
                            <span>AVAILABILITY</span>
                            <strong><?= (int) $product['stock_quantity'] > 0 ? 'In stock' : 'Out of stock' ?></strong>
                        </div>
                    </div>
                    <?php if ((int) $product['stock_quantity'] > 0): ?>
                        <form method="post" action="forms/update-cart.php">
                            <input type="hidden" name="add_to_cart" value="<?= (int) $product['id'] ?>">
                            <input type="hidden" name="return_to" value="products">
                            <button type="submit" class="btn-main detail-modal-cta">
                                ADD TO CART
                            </button>
                        </form>
                    <?php else: ?>
                        <button type="button" class="btn-outline detail-modal-cta" disabled>OUT OF STOCK</button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include 'partials/footer.php'; ?>
<?php include 'partials/auth-modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>

</body>
</html>
