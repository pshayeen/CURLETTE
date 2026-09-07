<?php

session_start();

require 'database/config.php';
require 'validation.php';
require 'redirects.php';

/**
 * The modal on index.php submits this form over fetch() with an
 * X-Requested-With header, so it gets a JSON response instead of a
 * redirect. The plain <form> fallback on account.php (no JS) keeps
 * getting the original redirect-based behaviour.
 */
function wantsJson(): bool
{
    $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    $accept        = $_SERVER['HTTP_ACCEPT'] ?? '';

    return strtolower($requestedWith) === 'xmlhttprequest'
        || str_contains($accept, 'application/json');
}

function sendError(string $mode, string $message, string $redirectQuery): void
{
    if (wantsJson()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }

    header('Location: account.php?mode=' . $mode . '&status=error&message=' . urlencode($message) . $redirectQuery);
    exit;
}

function sendSuccess(string $redirectTarget): void
{
    if (wantsJson()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'redirect' => $redirectTarget]);
        exit;
    }

    header('Location: ' . $redirectTarget);
    exit;
}

$redirectKey = (string) ($_POST['redirect'] ?? '');
$redirect    = isAllowedRedirect($redirectKey) ? $redirectKey : '';
$redirectQuery = $redirect !== '' ? '&redirect=' . $redirect : '';

$authSuccess = false;

if (isset($_POST['login'])) {

    $result = validateLoginInput($_POST);
    $errors = $result['errors'];

    if (!empty($errors)) {
        sendError('login', implode(' ', $errors), $redirectQuery);
    }

    $email    = $result['data']['email'];
    $password = $result['data']['password'];

    $pdo  = getConnection();
    $stmt = $pdo->prepare(
        'SELECT id, username, password_hash FROM user_account WHERE email = ?'
    );
    $stmt->execute([$email]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password_hash'])) {
        sendError('login', 'Incorrect email or password.', $redirectQuery);
    }

    session_regenerate_id(true);
    $_SESSION['user_id']  = $user['id'];
    $_SESSION['username'] = $user['username'];
    $authSuccess = true;

} elseif (isset($_POST['signup'])) {

    $result = validateSignupInput($_POST);
    $errors = $result['errors'];

    if (!empty($errors)) {
        sendError('signup', implode(' ', $errors), $redirectQuery);
    }

    $username = $result['data']['username'];
    $email    = $result['data']['email'];

    try {
        $pdo = getConnection();

        $checkStmt = $pdo->prepare(
            'SELECT id FROM user_account WHERE email = ? OR username = ?'
        );
        $checkStmt->execute([$email, $username]);

        if ($checkStmt->fetch(PDO::FETCH_ASSOC)) {
            sendError('signup', 'That username or email is already registered.', $redirectQuery);
        }

        $passwordHash = password_hash($result['data']['password'], PASSWORD_DEFAULT);

        $sql = "INSERT INTO user_account (username, email, password_hash)
                VALUES (:username, :email, :password_hash)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password_hash', $passwordHash);
        $stmt->execute();

        session_regenerate_id(true);
        $_SESSION['user_id']  = $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        $authSuccess = true;
    } catch (PDOException $e) {
        sendError('signup', 'Something went wrong. Please try again.', $redirectQuery);
    }

} else {
    if (wantsJson()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        exit;
    }
    header('Location: account.php');
    exit;
}

if ($authSuccess) {
    sendSuccess(resolveRedirectTarget($redirect));
}