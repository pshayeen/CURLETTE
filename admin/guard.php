<?php
session_start();

if (empty($_SESSION['user_id']) || empty($_SESSION['is_admin'])) {
    header('Location: ../account.php?mode=login');
    exit;
}

$adminUsername = $_SESSION['username'] ?? 'Admin';
