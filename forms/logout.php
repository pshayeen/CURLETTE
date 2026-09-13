<?php
session_start();

// ?check=1 reports login status as JSON (used for bfcache detection)
if (isset($_GET['check'])) {
    header('Content-Type: application/json');
    echo json_encode(['loggedIn' => !empty($_SESSION['user_id'])]);
    exit;
}

$_SESSION = [];

if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}

session_destroy();

header('Location: ../index.php');
exit;
