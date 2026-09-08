<?php
session_start();
require 'helpers.php';
require 'data/services.php';

$isLoggedIn = !empty($_SESSION['user_id']);
$username   = $_SESSION['username'] ?? '';
$bookHref   = bookHref($isLoggedIn);
$services   = getServices();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Our Services — Curlétte Hair Studio</title>
    <link rel="icon" href="assets/C-icon.png" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="style/style.css">
</head>

<body data-logged-in="<?= $isLoggedIn ? '1' : '0' ?>">

<?php $navActive = 'services'; include 'partials/nav.php'; ?>

<main class="booking-page">

    <section class="booking-hero">

        <div class="booking-heading">
            <p class="home-label">CURLÉTTE SERVICES</p>
            <h1>Our <em>services.</em></h1>
            <p>Every treatment is tailored to your curl pattern. See what's included, then book whenever you're ready.</p>
        </div>

        <div class="service-grid services-page-grid">

            <?php foreach ($services as $index => $service): ?>
                <article class="service-card service-card--detailed">
                    <div class="card-img">
                        <img src="<?= htmlspecialchars($service['image'], ENT_QUOTES, 'UTF-8') ?>"
                             alt="<?= htmlspecialchars($service['name'], ENT_QUOTES, 'UTF-8') ?>">
                    </div>

                    <div class="card-info">
                        <h3><?= htmlspecialchars($service['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                        <p><?= htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8') ?></p>
                        <div class="card-bottom">
                            <strong><?= htmlspecialchars($service['price'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <span class="card-duration"><?= htmlspecialchars($service['duration'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>
                        <div class="card-actions">
                            <button type="button" class="btn-outline card-action-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#serviceModal<?= $index ?>">
                                VIEW DETAILS
                            </button>
                            <a href="<?= serviceHref($service['name'], $isLoggedIn) ?>" class="btn-main card-action-btn">
                                BOOK
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>

        </div>

    </section>

</main>

<?php foreach ($services as $index => $service): ?>
    <div class="modal fade detail-modal" id="serviceModal<?= $index ?>" tabindex="-1" aria-labelledby="serviceModalLabel<?= $index ?>" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content detail-modal-content">
                <button type="button" class="btn-close detail-modal-close" data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="detail-modal-img">
                    <img src="<?= htmlspecialchars($service['image'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                </div>
                <div class="detail-modal-body">
                    <p class="booking-eyebrow">CURLÉTTE SERVICE 0<?= $index + 1 ?></p>
                    <h2 id="serviceModalLabel<?= $index ?>"><?= htmlspecialchars($service['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <p class="detail-modal-description"><?= htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8') ?></p>
                    <div class="detail-modal-meta">
                        <div><span>PRICE</span><strong><?= htmlspecialchars($service['price'], ENT_QUOTES, 'UTF-8') ?></strong></div>
                        <div><span>DURATION</span><strong><?= htmlspecialchars($service['duration'], ENT_QUOTES, 'UTF-8') ?></strong></div>
                    </div>
                    <div class="service-includes">
                        <span>WHAT'S INCLUDED</span>
                        <p><?= htmlspecialchars($service['includes'], ENT_QUOTES, 'UTF-8') ?></p>
                    </div>
                    <a href="<?= serviceHref($service['name'], $isLoggedIn) ?>" class="btn-main detail-modal-cta">
                        BOOK THIS SERVICE <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<?php include 'partials/footer.php'; ?>
<?php include 'partials/auth-modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="script.js"></script>

</body>
</html>
