<?php


function bookHref(bool $isLoggedIn): string
{
    return $isLoggedIn
        ? 'bookAppointment.php'
        : 'account.php?mode=login&redirect=book-appointment';
}


function serviceHref(string $serviceName, bool $isLoggedIn): string
{
    return $isLoggedIn
        ? 'bookAppointment.php?service=' . urlencode($serviceName)
        : 'account.php?mode=login&redirect=book-appointment';
}


function formatPrice($price): string
{
    return '$' . number_format((float) $price, 2);
}


function getCartItemCount(?int $userId): int
{
    if (!$userId) {
        return 0;
    }

    require_once __DIR__ . '/database/config.php';
    $pdo = getConnection();
    $stmt = $pdo->prepare('SELECT COALESCE(SUM(quantity), 0) FROM cart_item WHERE user_id = ?');
    $stmt->execute([$userId]);
    return (int) $stmt->fetchColumn();
}
