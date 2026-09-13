<?php
session_start();

require '../database/config.php';
require '../includes/validation.php';
require '../includes/redirects.php';
require '../includes/helpers.php';

function wantsJson(): bool
{
    $requestedWith = $_SERVER['HTTP_X_REQUESTED_WITH'] ?? '';
    $accept = $_SERVER['HTTP_ACCEPT'] ?? '';

    return strtolower($requestedWith) === 'xmlhttprequest'
        || str_contains($accept, 'application/json');
}

function redirectError(string $mode, string $message, string $redirectQuery = ''): never
{
    if (wantsJson()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }

    header('Location: ../account.php?mode=' . $mode . '&status=error&message=' . urlencode($message) . $redirectQuery);
    exit;
}

function redirectSuccess(string $target): never
{
    if (wantsJson()) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'redirect' => $target]);
        exit;
    }

    header('Location: ' . $target);
    exit;
}

$redirectKey = (string) ($_POST['redirect'] ?? '');
$redirect = isAllowedRedirect($redirectKey) ? $redirectKey : '';
$redirectQuery = $redirect !== '' ? '&redirect=' . urlencode($redirect) : '';

if (isset($_POST['login'])) {
    $result = validateLoginInput($_POST);

    if ($result['errors']) {
        redirectError('login', implode(' ', $result['errors']), $redirectQuery);
    }

    $pdo = getConnection();
    $stmt = $pdo->prepare('SELECT id, username, password_hash, is_admin FROM user_account WHERE email = ? LIMIT 1');
    $stmt->execute([$result['data']['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($result['data']['password'], $user['password_hash'])) {
        redirectError('login', 'Incorrect email or password.', $redirectQuery);
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['is_admin'] = (bool) $user['is_admin'];

    if ($_SESSION['is_admin']) {
        redirectSuccess('../admin/dashboard.php');
    }

    redirectSuccess(resolveRedirectTarget($redirect));
}

if (isset($_POST['signup'])) {
    $result = validateSignupInput($_POST);

    if ($result['errors']) {
        redirectError('signup', implode(' ', $result['errors']), $redirectQuery);
    }

    $fullName = $result['data']['full_name'];
    $email = $result['data']['email'];
    $phone = $result['data']['phone'];
    $address = $result['data']['address'];

    try {
        $pdo = getConnection();
        $check = $pdo->prepare('SELECT id FROM user_account WHERE email = ? LIMIT 1');
        $check->execute([$email]);

        if ($check->fetch(PDO::FETCH_ASSOC)) {
            redirectError('signup', 'That email is already registered.', $redirectQuery);
        }

        $username = generateUsernameFromName($pdo, $fullName);
        $passwordHash = password_hash($result['data']['password'], PASSWORD_DEFAULT);
        $stmt = $pdo->prepare(
            'INSERT INTO user_account (username, full_name, email, phone, address, password_hash)
             VALUES (:username, :full_name, :email, :phone, :address, :password_hash)'
        );
        $stmt->execute([
            ':username' => $username,
            ':full_name' => $fullName,
            ':email' => $email,
            ':phone' => $phone,
            ':address' => $address,
            ':password_hash' => $passwordHash
        ]);

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $pdo->lastInsertId();
        $_SESSION['username'] = $username;
        $_SESSION['is_admin'] = false;

        redirectSuccess(resolveRedirectTarget($redirect));
    } catch (PDOException $e) {
        redirectError('signup', 'Something went wrong. Please try again.', $redirectQuery);
    }
}

if (isset($_POST['update_account'])) {
    if (empty($_SESSION['user_id'])) {
        header('Location: ../account.php?mode=login');
        exit;
    }

    $username = trim($_POST['username'] ?? '');
    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $errors = array_filter([
        validateRequired($username, 'Username'),
        validateUsernameFormat($username),
        validateFullName($fullName),
        validateRequired($email, 'Email'),
        validateEmailFormat($email),
        validateRequired($phone, 'Phone number'),
        $phone !== '' ? validatePhoneFormat($phone) : null,
        validateAddress($address),
    ]);

    if ($errors) {
        header('Location: ../my-account.php?status=error&message=' . urlencode(implode(' ', $errors)));
        exit;
    }

    try {
        $pdo = getConnection();
        $userId = (int) $_SESSION['user_id'];
        $check = $pdo->prepare(
            'SELECT id FROM user_account
             WHERE (email = ? OR username = ?) AND id <> ?
             LIMIT 1'
        );
        $check->execute([$email, $username, $userId]);

        if ($check->fetch(PDO::FETCH_ASSOC)) {
            header('Location: ../my-account.php?status=error&message=' . urlencode('That username or email is already registered.'));
            exit;
        }

        $stmt = $pdo->prepare('UPDATE user_account SET username = ?, full_name = ?, email = ?, phone = ?, address = ? WHERE id = ?');
        $stmt->execute([$username, $fullName, $email, $phone, $address, $userId]);
        $_SESSION['username'] = $username;

        header('Location: ../my-account.php?status=success');
        exit;
    } catch (PDOException $e) {
        header('Location: ../my-account.php?status=error&message=' . urlencode('Something went wrong. Please try again.'));
        exit;
    }
}

header('Location: ../account.php');
exit;
