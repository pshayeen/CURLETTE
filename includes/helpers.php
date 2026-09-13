<?php

// booking page if logged in, else login page
function bookHref(bool $isLoggedIn): string
{
    return $isLoggedIn
        ? 'bookAppointment.php'
        : 'account.php?mode=login&redirect=book-appointment';
}

// booking page with the service picked, or login
function serviceHref(string $serviceName, bool $isLoggedIn): string
{
    return $isLoggedIn
        ? 'bookAppointment.php?service=' . urlencode($serviceName)
        : 'account.php?mode=login&redirect=book-appointment';
}

// formats a price as "PHP 1,234.00"
function formatPrice($price): string
{
    return 'PHP ' . number_format((float) $price, 2);
}

// Cart item count for the nav badge. 0 for guests, no DB hit.
function getCartItemCount(?int $userId): int
{
    if (!$userId) {
        return 0;
    }

    require_once __DIR__ . '/../database/config.php';
    $pdo = getConnection();
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart_item WHERE user_id = ?');
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}

// turns a payment_method value into a readable label
function paymentMethodLabel(string $method): string
{
    $labels = ['cash' => 'Cash on Delivery', 'gcash' => 'GCash', 'bank' => 'Bank Transfer'];
    return $labels[$method] ?? 'Cash on Delivery';
}

// flat deposit required to book any appointment
function getDepositAmount(): float
{
    return 100.00;
}

// generates a reference number for GCash/Bank Transfer payments
function generatePaymentReference(): string
{
    return strtoupper(bin2hex(random_bytes(5)));
}

// makes a username from the first part of someone's name, adding a
// number on the end if that name is already taken. pass $excludeUserId
// when re-generating for an existing account so it doesn't collide
// with that same account's current username.
function generateUsernameFromName(PDO $pdo, string $fullName, ?int $excludeUserId = null): string
{
    $firstName = explode(' ', trim($fullName))[0] ?? '';
    $base = preg_replace('/[^a-zA-Z0-9_]/', '', $firstName);
    if ($base === '') {
        $base = 'user';
    }
    $base = substr($base, 0, 15);

    $sql = 'SELECT id FROM user_account WHERE username = ?';
    if ($excludeUserId !== null) {
        $sql .= ' AND id != ?';
    }
    $sql .= ' LIMIT 1';
    $check = $pdo->prepare($sql);

    $username = $base;
    $suffix = 1;

    while (true) {
        $params = $excludeUserId !== null ? [$username, $excludeUserId] : [$username];
        $check->execute($params);
        if (!$check->fetch()) {
            return $username;
        }
        $suffix++;
        $username = $base . $suffix;
    }
}
