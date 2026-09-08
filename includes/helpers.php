<?php

// Where "BOOK AN APPOINTMENT" goes: straight to booking if logged in, else to login first.
function bookHref(bool $isLoggedIn): string
{
    return $isLoggedIn
        ? 'bookAppointment.php'
        : 'account.php?mode=login&redirect=book-appointment';
}

// Where a specific service card goes: booking with that service pre-selected, or login first.
function serviceHref(string $serviceName, bool $isLoggedIn): string
{
    return $isLoggedIn
        ? 'bookAppointment.php?service=' . urlencode($serviceName)
        : 'account.php?mode=login&redirect=book-appointment';
}

// Formats a DB decimal or float/int price as "$12.00".
function formatPrice($price): string
{
    return '$' . number_format((float) $price, 2);
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
