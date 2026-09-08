<?php

require_once __DIR__ . '/../database/config.php';

// A user's own appointments, most recent first.
function getUserAppointments(int $userId): array
{
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        'SELECT id, service, appointment_date, appointment_time, notes, status, created_at
         FROM appointment_booking
         WHERE user_id = ?
         ORDER BY appointment_date DESC, appointment_time DESC'
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
