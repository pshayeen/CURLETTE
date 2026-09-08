<?php
require 'guard.php';
require '../database/config.php';

$messageId = filter_input(INPUT_POST, 'message_id', FILTER_VALIDATE_INT);

if ($messageId) {
    $pdo = getConnection();
    $stmt = $pdo->prepare('UPDATE contact_message SET is_read = 1 WHERE id = ?');
    $stmt->execute([$messageId]);
}

header('Location: messages.php');
exit;
