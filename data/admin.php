<?php

require_once __DIR__ . '/../database/config.php';

function getDashboardStats(): array
{
    $pdo = getConnection();

    return [
        'pending_orders' => (int) $pdo->query("SELECT COUNT(*) FROM shop_order WHERE status = 'placed'")->fetchColumn(),
        'total_orders' => (int) $pdo->query('SELECT COUNT(*) FROM shop_order')->fetchColumn(),
        'upcoming_appointments' => (int) $pdo->query("SELECT COUNT(*) FROM appointment_booking WHERE status = 'upcoming' AND appointment_date >= CURDATE()")->fetchColumn(),
        'low_stock_count' => (int) $pdo->query('SELECT COUNT(*) FROM product WHERE stock_quantity <= 5')->fetchColumn(),
        'unread_messages' => (int) $pdo->query('SELECT COUNT(*) FROM contact_message WHERE is_read = 0')->fetchColumn(),
        'total_revenue' => (float) $pdo->query("SELECT COALESCE(SUM(total), 0) FROM shop_order WHERE status != 'cancelled'")->fetchColumn(),
    ];
}

function getRecentOrders(int $limit = 5): array
{
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        'SELECT o.id, o.status, o.total, o.created_at, u.username
         FROM shop_order o
         INNER JOIN user_account u ON u.id = o.user_id
         ORDER BY o.created_at DESC
         LIMIT ?'
    );
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUpcomingAppointmentsPreview(int $limit = 5): array
{
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        "SELECT a.id, a.service, a.appointment_date, a.appointment_time, u.username
         FROM appointment_booking a
         INNER JOIN user_account u ON u.id = a.user_id
         WHERE a.status = 'upcoming' AND a.appointment_date >= CURDATE()
         ORDER BY a.appointment_date ASC, a.appointment_time ASC
         LIMIT ?"
    );
    $stmt->bindValue(1, $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllOrders(): array
{
    $pdo = getConnection();
    $stmt = $pdo->query(
        'SELECT o.id, o.status, o.total, o.created_at, u.username, u.email
         FROM shop_order o
         INNER JOIN user_account u ON u.id = o.user_id
         ORDER BY o.created_at DESC'
    );
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getOrderItems(int $orderId): array
{
    $pdo = getConnection();
    $stmt = $pdo->prepare('SELECT product_name, unit_price, quantity FROM shop_order_item WHERE order_id = ?');
    $stmt->execute([$orderId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllAppointments(): array
{
    $pdo = getConnection();
    $stmt = $pdo->query(
        'SELECT a.id, a.service, a.appointment_date, a.appointment_time, a.notes, a.status, u.username, u.email
         FROM appointment_booking a
         INNER JOIN user_account u ON u.id = a.user_id
         ORDER BY a.appointment_date DESC, a.appointment_time DESC'
    );
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllMessages(): array
{
    $pdo = getConnection();
    $stmt = $pdo->query('SELECT * FROM contact_message ORDER BY created_at DESC');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
