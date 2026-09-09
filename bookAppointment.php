<?php
session_start();

if (empty($_SESSION['user_id'])) {
    header('Location: account.php?mode=login&redirect=book-appointment');
    exit;
}

require 'database/config.php';
require 'data/services.php';
require 'includes/helpers.php';

$isLoggedIn = true;
$bookHref = bookHref($isLoggedIn);

$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
$id      = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$today   = date('Y-m-d');
$username = $_SESSION['username'] ?? 'Account';

$services = getServices();

$selectedService = trim($_GET['service'] ?? '');
$serviceNames = array_column($services, 'name');
if (!in_array($selectedService, $serviceNames, true)) {
    $selectedService = '';
}

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
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-logged-in="1">

<?php $showBookButton = false; include 'partials/nav.php'; ?>

<main class="booking-page">
    <section class="booking-hero">
        <div class="booking-heading">
            <p class="home-label">YOUR CURLS, YOUR CHOICE</p>
            <h1>Book your <em>appointment.</em></h1>
            <p>Choose the service that fits your curls, then select your preferred date and time.</p>
        </div>

        <?php if ($status === 'success' && $booking): ?>
            <div class="booking-success-card">

                <div class="booking-success-icon">
                    <i class="bi bi-check2"></i>
                </div>

                <div>
                    <p class="booking-eyebrow">BOOKING CONFIRMED</p>
                    <h2>Your appointment is booked.</h2>
                    <p>Confirmation #<?= htmlspecialchars((string) $id, ENT_QUOTES, 'UTF-8') ?></p>
                </div>

                <div class="booking-summary booking-summary-grid">

                    <div>
                        <span>Service</span>
                        <strong><?= htmlspecialchars($booking['service'], ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>

                    <div>
                        <span>Date</span>
                        <strong><?= htmlspecialchars(date('F j, Y', strtotime($booking['appointment_date'])), ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>

                    <div>
                        <span>Time</span>
                        <strong><?= htmlspecialchars(date('g:i A', strtotime($booking['appointment_time'])), ENT_QUOTES, 'UTF-8') ?></strong>
                    </div>

                    <?php if (!empty($booking['notes'])): ?>
                        <div>
                            <span>Notes</span>
                            <strong><?= htmlspecialchars($booking['notes'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </div>
                    <?php endif; ?>

                </div>

                <a href="bookAppointment.php" class="btn-outline booking-again-btn">BOOK ANOTHER</a>

            </div>
        <?php else: ?>
            <?php if ($status === 'error' && $message): ?>
                <div class="auth-error booking-message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>

            <form action="forms/save-appointment.php" method="post" class="booking-form" novalidate>
                <input type="hidden" name="book-appointment" value="1">

                <div class="booking-section">
                    <div class="booking-section-heading">
                        <span>01</span>
                        <div>
                            <h2>Choose a service</h2>
                            <p>Click a service to select it. Need full details first? <a href="services.php">See all services</a>.</p>
                        </div>
                    </div>

                    <div class="booking-service-grid">
                        <?php foreach ($services as $index => $service): ?>
                            <article class="booking-service-card <?= $selectedService === $service['name'] ? 'selected' : '' ?>"
                                     data-service-card="<?= htmlspecialchars($service['name'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="button" class="service-card-main" data-service-select="<?= htmlspecialchars($service['name'], ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="service-card-img">
                                        <img src="<?= htmlspecialchars($service['image'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                                    </div>
                                    <span class="service-card-number">0<?= $index + 1 ?></span>
                                    <span class="service-card-title"><?= htmlspecialchars($service['name'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="service-card-description"><?= htmlspecialchars($service['description'], ENT_QUOTES, 'UTF-8') ?></span>
                                    <span class="service-card-bottom">
                                        <strong><?= htmlspecialchars($service['price'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        <span><?= htmlspecialchars($service['duration'], ENT_QUOTES, 'UTF-8') ?></span>
                                    </span>
                                </button>
                            </article>
                        <?php endforeach; ?>
                    </div>

                    <input type="hidden" id="service" name="service" value="<?= htmlspecialchars($selectedService, ENT_QUOTES, 'UTF-8') ?>" required>
                    <p class="selected-service-message" id="selectedServiceMessage" <?= $selectedService === '' ? 'hidden' : '' ?>>
                        <i class="bi bi-check-circle-fill"></i>
                        Selected: <strong id="selectedServiceName"><?= htmlspecialchars($selectedService, ENT_QUOTES, 'UTF-8') ?></strong>
                    </p>
                </div>

                <div class="booking-section booking-details-section">
                    <div class="booking-section-heading">
                        <span>02</span>
                        <div>
                            <h2>Choose your schedule</h2>
                            <p>Pick a date and time that works best for you.</p>
                        </div>
                    </div>

                    <div class="booking-details-grid">
                        <div class="form-group">
                            <label for="appointment_date">APPOINTMENT DATE</label>
                            <input type="date" id="appointment_date" name="appointment_date" min="<?= htmlspecialchars($today, ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="appointment_time">APPOINTMENT TIME</label>
                            <select id="appointment_time" name="appointment_time" required>
                                <option value="" selected disabled>Choose a time</option>
                                <?php for ($hour = 9; $hour <= 17; $hour++): ?>
                                    <?php foreach ([0, 30] as $minute): ?>
                                        <?php if ($hour === 17 && $minute > 0) continue; ?>
                                        <?php $timeValue = sprintf('%02d:%02d', $hour, $minute); ?>
                                        <option value="<?= $timeValue ?>"><?= date('g:i A', strtotime($timeValue)) ?></option>
                                    <?php endforeach; ?>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="booking-section">
                    <div class="booking-section-heading">
                        <span>03</span>
                        <div>
                            <h2>Anything we should know?</h2>
                            <p>Optional — tell us about your hair goals or special requests.</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="notes">NOTES <span>(OPTIONAL)</span></label>
                        <textarea id="notes" name="notes" maxlength="500" rows="4" placeholder="Tell us anything you'd like us to know..."></textarea>
                    </div>
                </div>

                <div class="booking-submit-area">
                    <p><i class="bi bi-info-circle"></i> Your appointment will be saved to your account after confirmation.</p>
                    <button type="submit" class="btn-main booking-submit">CONFIRM BOOKING <i class="bi bi-arrow-right"></i></button>
                </div>
            </form>
        <?php endif; ?>
    </section>
</main>

<!-- Booking confirmation modal -->
<div class="modal fade" id="bookingConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content confirm-modal-content">
        <div class="confirm-modal-icon"><i class="bi bi-calendar-check"></i></div>
        <h2>Confirm your booking?</h2>
            <p><strong id="bookingConfirmSummary">—</strong></p>
            <div class="confirm-modal-actions">
                <button type="button" class="btn-outline" data-bs-dismiss="modal">GO BACK</button>
                <button type="button" class="btn-main" id="bookingConfirmBtn">YES, BOOK IT</button>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>