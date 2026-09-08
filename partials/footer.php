<?php
/**
 * Shared site footer. Expects $isLoggedIn and $bookHref already set by the
 * including page (same values used for the nav partial).
 */
?>
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

            <a href="index.php#home">HOME</a>
            <a href="services.php">SERVICES</a>
            <a href="products.php">PRODUCTS</a>
            <a href="index.php#about">ABOUT US</a>
            <a href="index.php#contact">CONTACT</a>

        </div>


        <div class="footer-col">

            <h3>CUSTOMER</h3>

            <a href="account.php">MY ACCOUNT</a>
            <a href="<?= $bookHref ?? 'bookAppointment.php' ?>">BOOK AN APPOINTMENT</a>

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
