<?php
require 'guard.php';
require '../data/admin.php';

$adminActive = 'appointments';
$appointments = getAllAppointments();
$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments | Admin | Curlétte</title>
    <link rel="icon" href="../assets/C-icon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500,600;1,500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'partials/nav.php'; ?>

<main class="admin-main">

    <div class="admin-page-header">
        <div>
            <h1>Appointments</h1>
            <p><?= count($appointments) ?> total booking<?= count($appointments) === 1 ? '' : 's' ?>.</p>
        </div>
    </div>

    <?php if ($status === 'success'): ?>
        <div class="admin-notice success">Appointment updated.</div>
    <?php elseif ($status === 'error' && $message): ?>
        <div class="admin-notice error"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <?php if (empty($appointments)): ?>
        <div class="admin-table-wrap">
            <p class="admin-empty">No appointments have been booked yet.</p>
        </div>
    <?php else: ?>
        <div class="admin-table-wrap">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Customer</th>
                        <th>Service</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th>Notes</th>
                        <th>Status</th>
                        <th>Update</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appointments as $appt): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($appt['username'], ENT_QUOTES, 'UTF-8') ?></strong>
                                <span class="muted"><?= htmlspecialchars($appt['email'], ENT_QUOTES, 'UTF-8') ?></span>
                            </td>
                            <td><?= htmlspecialchars($appt['service'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= date('M j, Y', strtotime($appt['appointment_date'])) ?></td>
                            <td><?= date('g:i A', strtotime($appt['appointment_time'])) ?></td>
                            <td><span class="muted"><?= $appt['notes'] ? htmlspecialchars($appt['notes'], ENT_QUOTES, 'UTF-8') : '—' ?></span></td>
                            <td><span class="admin-badge <?= htmlspecialchars($appt['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars($appt['status'], ENT_QUOTES, 'UTF-8') ?></span></td>
                            <td>
                                <form method="post" action="update-appointment-status.php" class="admin-status-form">
                                    <input type="hidden" name="appointment_id" value="<?= (int) $appt['id'] ?>">
                                    <select name="status">
                                        <option value="upcoming" <?= $appt['status'] === 'upcoming' ? 'selected' : '' ?>>Upcoming</option>
                                        <option value="completed" <?= $appt['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="cancelled" <?= $appt['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <button type="submit">Save</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

</main>

</body>
</html>
