<?php

require_once __DIR__ . '/../database/config.php';

// A user's own appointments, most recent first.
function getUserAppointments(int $userId): array
{
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        'SELECT id, service, appointment_date, appointment_time, notes, status, deposit_amount, payment_method, payment_reference, created_at
         FROM appointment_booking
         WHERE user_id = ?
         ORDER BY appointment_date DESC, appointment_time DESC'
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// one appointment with customer info, for the receipt page.
// pass $userId for owner-only, or null for admin access
function getAppointmentById(int $appointmentId, ?int $userId = null): ?array
{
    $pdo = getConnection();
    $sql = 'SELECT a.*, u.full_name, u.email
            FROM appointment_booking a
            INNER JOIN user_account u ON u.id = a.user_id
            WHERE a.id = ?';
    $params = [$appointmentId];

    if ($userId !== null) {
        $sql .= ' AND a.user_id = ?';
        $params[] = $userId;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $appt = $stmt->fetch(PDO::FETCH_ASSOC);
    return $appt ?: null;
}
