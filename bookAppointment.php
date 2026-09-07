<?php
session_start();

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

if (empty($_SESSION['user_id'])) {
    header('Location: account.php?mode=login&redirect=book-appointment');
    exit;
}

require 'database/config.php';

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
$id      = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$today   = date('Y-m-d');

$booking = null;
if ($status === 'success' && $id) {
    $pdo  = getConnection();
    $sql  = "SELECT service, appointment_date, appointment_time, notes
             FROM appointment_booking
             WHERE id = :id AND user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Book an Appointment — Curlétte Hair Studio</title>
    <link rel="icon" href="assets/C-icon.png" type="image/png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">

    <link rel="stylesheet" href="style/style.css">
</head>

<body>

<header class="header">
    <nav class="nav" aria-label="Main navigation">
        <div class="nav-box">

            <a href="index.php#home" class="logo">
                <img src="assets/logo.png" alt="Curlette Hair Studio">
            </a>

            <ul class="nav-links">
                <li><a href="index.php#home">HOME</a></li>
                <li><a href="index.php#services">SERVICES</a></li>
                <li><a href="index.php#products">PRODUCTS</a></li>
                <li><a href="index.php#about">ABOUT US</a></li>
                <li><a href="index.php#contact">CONTACT</a></li>
            </ul>

            <div class="nav-actions">
                <a href="bookAppointment.php" class="btn-main">BOOK AN APPOINTMENT</a>

                <button type="button" class="icon-btn cart-btn-nav" aria-label="Shopping bag">
                    <i class="bi bi-handbag"></i>
                </button>

                <a href="account.php" class="icon-btn account-btn" aria-label="Account">
                    <i class="bi bi-person-fill"></i>
                </a>
            </div>

        </div>
    </nav>
</header>

<main>

    <section class="auth">

        <div class="auth-card">

            <h2 class="auth-title">Book your appointment.</h2>
            <p class="auth-sub">Pick a service, date, and time that works for you.</p>

            <?php if ($status === 'success' && $booking): ?>
                <p class="auth-success">Appointment booked! Confirmation #<?= htmlspecialchars($id) ?></p>

                <div class="booking-summary">
                    <div class="booking-row">
                        <span>Service</span>
                        <strong><?= htmlspecialchars($booking['service']) ?></strong>
                    </div>
                    <div class="booking-row">
                        <span>Date</span>
                        <strong><?= htmlspecialchars(date('F j, Y', strtotime($booking['appointment_date']))) ?></strong>
                    </div>
                    <div class="booking-row">
                        <span>Time</span>
                        <strong><?= htmlspecialchars(date('g:i A', strtotime($booking['appointment_time']))) ?></strong>
                    </div>
                    <?php if (!empty($booking['notes'])): ?>
                        <div class="booking-row">
                            <span>Notes</span>
                            <strong><?= htmlspecialchars($booking['notes']) ?></strong>
                        </div>
                    <?php endif; ?>
                </div>

                <p class="auth-sub book-again-note">Want to book another?</p>
            <?php elseif ($status === 'error' && $message): ?>
                <p class="auth-error"><?= htmlspecialchars($message) ?></p>
            <?php endif; ?>

            <form action="booking_function.php" method="post" class="auth-form" novalidate>
                <input type="hidden" name="book-appointment" value="1">

                <div class="form-group">
                    <label for="service">Service</label>
                    <select id="service" name="service" required>
                        <option value="" disabled selected>Choose a service</option>
                        <option value="Curl Consultation">Curl Consultation — $123</option>
                        <option value="Curl Styling">Curl Styling — $123</option>
                        <option value="Curl Hair Cut">Curl Hair Cut — $123</option>
                        <option value="Curl Hair Color">Curl Hair Color — $123</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="appointment_date">Date</label>
                    <input type="date" id="appointment_date" name="appointment_date" min="<?= htmlspecialchars($today) ?>" required>
                </div>

                <div class="form-group">
                    <label for="appointment_time">Time</label>
                    <input type="time" id="appointment_time" name="appointment_time" required>
                </div>

                <div class="form-group">
                    <label for="notes">Notes (optional)</label>
                    <input type="text" id="notes" name="notes" placeholder="Anything we should know?">
                </div>

                <button type="submit" class="btn-main auth-submit">CONFIRM BOOKING</button>
            </form>

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
            <a href="index.php#home">HOME</a>
            <a href="index.php#services">SERVICES</a>
            <a href="index.php#products">PRODUCTS</a>
            <a href="index.php#about">ABOUT US</a>
            <a href="index.php#contact">CONTACT</a>
        </div>

        <div class="footer-col">
            <h3>CUSTOMER</h3>
            <a href="account.php">MY ACCOUNT</a>
            <a href="#">MY ORDERS</a>
        </div>

        <div class="footer-col">
            <h3>CONTACT US</h3>
            <p><i class="bi bi-telephone"></i> 0912 345 6789</p>
            <p><i class="bi bi-envelope"></i> curlettehairstudio@email.com</p>

            <div class="social">
                <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
            </div>
        </div>

    </div>

</footer>

<script src="script.js"></script>

</body>
</html>