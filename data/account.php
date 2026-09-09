<?php

require_once __DIR__ . '/../database/config.php';

// a user's own account info
function getUserAccount(int $userId): ?array
{
    $pdo = getConnection();
    $stmt = $pdo->prepare('SELECT username, email, phone FROM user_account WHERE id = ?');
    $stmt->execute([$userId]);
    $account = $stmt->fetch(PDO::FETCH_ASSOC);
    return $account ?: null;
}
