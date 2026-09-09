<?php
// needs: $bookHref (optional: $ctaHeading, $ctaText)
$bookHref   = $bookHref ?? '#';
$ctaHeading = $ctaHeading ?? 'Ready to love your curls?';
$ctaText    = $ctaText ?? 'Book your appointment today!';
?>
<!-- APPOINTMENT -->
<section id="appointment" class="cta">

    <img src="assets/images/cta-bg.png" alt="" class="cta-bg">

    <div class="cta-overlay"></div>
    <div class="cta-content">

        <h2><?= htmlspecialchars($ctaHeading, ENT_QUOTES, 'UTF-8') ?></h2>
        <p><?= htmlspecialchars($ctaText, ENT_QUOTES, 'UTF-8') ?></p>

        <a href="<?= $bookHref ?>" class="btn-main js-book-trigger">
            BOOK AN APPOINTMENT
        </a>

    </div>

</section>
