<?php
session_start();
require 'includes/helpers.php';
require 'data/appointments.php';

if (empty($_SESSION['user_id'])) {
    header('Location: account.php?mode=login');
    exit;
}

$isLoggedIn = true;
$username   = $_SESSION['username'] ?? 'Account';
$bookHref   = bookHref($isLoggedIn);
$appointments = getUserAppointments((int) $_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Appointments — Curlétte Hair Studio</title>
    <link rel="icon" href="assets/C-icon.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;0,700;1,400;1,500;1,600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body data-logged-in="1">

<?php include 'partials/nav.php'; ?>

<main class="booking-page">
    <section class="booking-hero">

        <div class="booking-heading">
            <p class="home-label">YOUR CURLÉTTE ACCOUNT</p>
            <h1>My <em>appointments.</em></h1>
            <p>Every visit you've booked with us, past and upcoming.</p>
        </div>

        <?php if ($appointments): ?>
            <?php if (($_GET['status'] ?? null) === 'success'): ?>
                <div class="auth-context booking-message"><?= htmlspecialchars($_GET['message'] ?? 'Updated.', ENT_QUOTES, 'UTF-8') ?></div>
            <?php elseif (($_GET['status'] ?? null) === 'error'): ?>
                <div class="auth-error booking-message"><?= htmlspecialchars($_GET['message'] ?? 'Something went wrong.', ENT_QUOTES, 'UTF-8') ?></div>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (empty($appointments)): ?>
            <div class="cart-empty">
                <i class="bi bi-calendar-x"></i>
                <h2>No appointments yet.</h2>
                <p>When you book a visit, it'll show up here.</p>
                <a href="<?= $bookHref ?>" class="btn-main">BOOK AN APPOINTMENT</a>
            </div>
        <?php else: ?>
            <div class="my-list">
                <?php foreach ($appointments as $appt): ?>
                    <div class="my-list-item">
                        <div class="my-list-item-header">
                            <div>
                                <h3><?= htmlspecialchars($appt['service'], ENT_QUOTES, 'UTF-8') ?></h3>
                                <span class="muted">
                                    <?= date('M j, Y', strtotime($appt['appointment_date'])) ?>
                                    at <?= date('g:i A', strtotime($appt['appointment_time'])) ?>
                                </span>
                                <?php if ($appt['notes']): ?>
                                    <span class="muted"><?= htmlspecialchars($appt['notes'], ENT_QUOTES, 'UTF-8') ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="status-badge <?= htmlspecialchars($appt['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($appt['status'], ENT_QUOTES, 'UTF-8') ?></span>
                        </div>

                        <?php if ($appt['status'] === 'upcoming'): ?>
                            <div class="my-list-item-actions">
                                <details class="my-reschedule-toggle">
                                    <summary>Reschedule</summary>
                                    <form method="post" action="forms/reschedule-appointment.php" class="my-reschedule-form js-reschedule-form">
                                        <input type="hidden" name="appointment_id" value="<?= (int) $appt['id'] ?>">
                                        <div class="form-group">
                                            <label for="date-<?= (int) $appt['id'] ?>">New Date</label>
                                            <input type="date" id="date-<?= (int) $appt['id'] ?>" name="appointment_date" min="<?= date('Y-m-d') ?>" value="<?= htmlspecialchars($appt['appointment_date'], ENT_QUOTES, 'UTF-8') ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="time-<?= (int) $appt['id'] ?>">New Time</label>
                                            <input type="time" id="time-<?= (int) $appt['id'] ?>" name="appointment_time" value="<?= htmlspecialchars($appt['appointment_time'], ENT_QUOTES, 'UTF-8') ?>" required>
                                        </div>
                                        <button type="submit" class="btn-outline btn-small">SAVE NEW TIME</button>
                                    </form>
                                </details>

                                <button type="button" class="my-cancel-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#cancelConfirmModal"
                                        data-id="<?= (int) $appt['id'] ?>"
                                        data-label="Your <?= htmlspecialchars($appt['service'], ENT_QUOTES, 'UTF-8') ?> appointment on <?= date('M j, Y', strtotime($appt['appointment_date'])) ?> will be cancelled. This can't be undone.">
                                    Cancel Appointment
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    </section>
</main>

<!-- Shared cancel-confirmation modal (populated per row via data attributes) -->
<div class="modal fade" id="cancelConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="forms/cancel-appointment.php" id="cancelConfirmForm">
            <div class="modal-content confirm-modal-content danger">
                <div class="confirm-modal-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <h2>Cancel this appointment?</h2>
                <p id="cancelConfirmLabel">This can't be undone.</p>
                <input type="hidden" id="cancelConfirmIdField" name="appointment_id" value="">
                <div class="confirm-modal-actions">
                    <button type="button" class="btn-outline" data-bs-dismiss="modal">GO BACK</button>
                    <button type="submit" class="btn-main confirm-modal-danger">YES, CANCEL</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Reschedule confirmation modal -->
<div class="modal fade" id="rescheduleConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content confirm-modal-content">
            <div class="confirm-modal-icon"><i class="bi bi-calendar-event"></i></div>
            <h2>Confirm new time?</h2>
            <p>Move this appointment to <strong id="rescheduleConfirmSummary">—</strong>?</p>
            <div class="confirm-modal-actions">
                <button type="button" class="btn-outline" data-bs-dismiss="modal">GO BACK</button>
                <button type="button" class="btn-main" id="rescheduleConfirmBtn">YES, RESCHEDULE</button>
            </div>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/script.js"></script>
</body>
</html>
