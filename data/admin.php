<?php

require_once __DIR__ . '/../database/config.php';

// excludes admin accounts from every count and list below

function getDashboardStats(): array
{
    $pdo = getConnection();

    return [
        'pending_orders' => (int) $pdo->query(
            "SELECT COUNT(*) FROM shop_order o
             INNER JOIN user_account u ON u.id = o.user_id
             WHERE o.status IN ('placed', 'processing') AND u.is_admin = 0"
        )->fetchColumn(),

        'total_orders' => (int) $pdo->query(
            'SELECT COUNT(*) FROM shop_order o
             INNER JOIN user_account u ON u.id = o.user_id
             WHERE u.is_admin = 0'
        )->fetchColumn(),

        'upcoming_appointments' => (int) $pdo->query(
            "SELECT COUNT(*) FROM appointment_booking a
             INNER JOIN user_account u ON u.id = a.user_id
             WHERE a.status = 'upcoming' AND a.appointment_date >= CURDATE() AND u.is_admin = 0"
        )->fetchColumn(),

        'low_stock_count' => (int) $pdo->query(
            'SELECT COUNT(*) FROM product WHERE stock_quantity <= 5'
        )->fetchColumn(),

        'unread_messages' => (int) $pdo->query(
            'SELECT COUNT(*) FROM contact_message WHERE is_read = 0'
        )->fetchColumn(),

        'total_revenue' => (float) $pdo->query(
            "SELECT COALESCE(SUM(o.total), 0) FROM shop_order o
             INNER JOIN user_account u ON u.id = o.user_id
             WHERE o.status != 'cancelled' AND u.is_admin = 0"
        )->fetchColumn(),
    ];
}

function getRecentOrders(int $limit = 5): array
{
    $pdo = getConnection();
    $stmt = $pdo->prepare(
        'SELECT o.id, o.status, o.total, o.created_at, u.username
         FROM shop_order o
         INNER JOIN user_account u ON u.id = o.user_id
         WHERE u.is_admin = 0
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
         WHERE a.status = 'upcoming' AND a.appointment_date >= CURDATE() AND u.is_admin = 0
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
        'SELECT o.id, o.status, o.total, o.payment_method, o.created_at, u.username, u.email
         FROM shop_order o
         INNER JOIN user_account u ON u.id = o.user_id
         WHERE u.is_admin = 0
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
        'SELECT a.id, a.service, a.appointment_date, a.appointment_time, a.notes, a.status, a.deposit_amount, a.payment_method, u.username, u.email
         FROM appointment_booking a
         INNER JOIN user_account u ON u.id = a.user_id
         WHERE u.is_admin = 0
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

// Customer accounts only (excludes admin/staff logins), with a quick
// order/appointment count for each so the admin panel shows activity
// without extra clicks.
function getAllCustomers(string $search = ''): array
{
    $pdo = getConnection();

    $sql = "SELECT u.id, u.username, u.email, u.created_at,
                   (SELECT COUNT(*) FROM shop_order o WHERE o.user_id = u.id) AS order_count,
                   (SELECT COUNT(*) FROM appointment_booking a WHERE a.user_id = u.id) AS appointment_count
            FROM user_account u
            WHERE u.is_admin = 0";

    $params = [];
    if ($search !== '') {
        $sql .= ' AND (u.username LIKE ? OR u.email LIKE ?)';
        $params[] = '%' . $search . '%';
        $params[] = '%' . $search . '%';
    }

    $sql .= ' ORDER BY u.created_at DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Updates a customer's username/email. Reuses the same format rules as
// signup. Returns ['success' => bool, 'errors' => string[]].
function updateCustomerAccount(int $id, string $username, string $email): array
{
    require_once __DIR__ . '/../includes/validation.php';

    $username = trim($username);
    $email = trim($email);

    $errors = array_filter([
        validateRequired($username, 'Username'),
        validateUsernameFormat($username),
        validateRequired($email, 'Email'),
        validateEmailFormat($email),
    ]);
    $errors = array_values($errors);

    if (!empty($errors)) {
        return ['success' => false, 'errors' => $errors];
    }

    $pdo = getConnection();
    try {
        $stmt = $pdo->prepare('UPDATE user_account SET username = ?, email = ? WHERE id = ? AND is_admin = 0');
        $stmt->execute([$username, $email, $id]);
        return ['success' => true, 'errors' => []];
    } catch (PDOException $e) {
        if ($e->getCode() === '23000') {
            return ['success' => false, 'errors' => ['That username or email is already taken.']];
        }
        throw $e;
    }
}

// deletes a customer (never an admin); related rows cascade via FKs
function deleteCustomerAccount(int $id): bool
{
    $pdo = getConnection();
    $stmt = $pdo->prepare('DELETE FROM user_account WHERE id = ? AND is_admin = 0');
    $stmt->execute([$id]);
    return $stmt->rowCount() > 0;
}
