<?php

require_once __DIR__ . '/../data/services.php';

function validateEmailFormat(string $value): ?string
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : "Enter a valid email address.";
}

function validateRequired(string $value, string $label): ?string
{
    return trim($value) === '' ? "$label is required." : null;
}

/* ---------- Account (login / signup) ---------- */

function validateUsernameFormat(string $value): ?string
{
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $value)
        ? null
        : "Username must be 3-20 characters (letters, numbers, underscores only).";
}

// PH mobile format: 11 digits, starts with 09
function validatePhoneFormat(string $value): ?string
{
    return preg_match('/^09[0-9]{9}$/', $value)
        ? null
        : "Phone must be 11 digits and start with 09.";
}

function validatePasswordStrength(string $value): ?string
{
    if (strlen($value) < 8) {
        return "Password must be at least 8 characters.";
    }

    if (!preg_match('/[A-Za-z]/', $value)) {
        return "Password must include at least one letter.";
    }

    if (!preg_match('/[0-9]/', $value)) {
        return "Password must include at least one number.";
    }

    if (!preg_match('/[^A-Za-z0-9]/', $value)) {
        return "Password must include at least one special character.";
    }

    return null;
}

function validateSignupInput(array $post): array
{
    $username = trim($post['username'] ?? '');
    $email    = trim($post['email'] ?? '');
    $phone    = trim($post['phone'] ?? '');
    $password = $post['password'] ?? '';
    $confirm  = $post['confirm_password'] ?? '';

    $errors = array_filter([
        validateRequired($username, 'Username'),
        validateUsernameFormat($username),
        validateRequired($email, 'Email'),
        validateEmailFormat($email),
        validateRequired($phone, 'Phone number'),
        $phone !== '' ? validatePhoneFormat($phone) : null,
        validateRequired($password, 'Password'),
        validatePasswordStrength($password),
        $password !== '' && $password !== $confirm ? "Passwords do not match." : null,
    ]);
    $errors = array_values($errors);

    if (empty($errors)) {
        $username = htmlspecialchars($username, ENT_QUOTES, 'UTF-8');
    }

    return [
        'errors' => $errors,
        'data'   => ['username' => $username, 'email' => $email, 'phone' => $phone, 'password' => $password],
    ];
}

function validateLoginInput(array $post): array
{
    $email    = trim($post['email'] ?? '');
    $password = $post['password'] ?? '';

    $errors = array_filter([
        validateRequired($email, 'Email'),
        validateEmailFormat($email),
        validateRequired($password, 'Password'),
    ]);
    $errors = array_values($errors);

    return [
        'errors' => $errors,
        'data'   => ['email' => $email, 'password' => $password],
    ];
}

/* ---------- Appointment booking ---------- */

function validateDateNotPast(string $value, string $label): ?string
{
    $date = DateTime::createFromFormat('Y-m-d', $value);
    if (!$date || $date->format('Y-m-d') !== $value) {
        return "$label must be a valid date.";
    }

    $today = new DateTime('today');
    return $date >= $today ? null : "$label cannot be in the past.";
}

function validateAppointmentInput(array $post): array
{
    $service = trim($post['service'] ?? '');
    $date    = trim($post['appointment_date'] ?? '');
    $time    = trim($post['appointment_time'] ?? '');
    $notes   = trim($post['notes'] ?? '');

    $validServices = array_column(getServices(), 'name');

    $validTimes = [];
    for ($hour = 9; $hour <= 17; $hour++) {
        foreach ([0, 30] as $minute) {
            if ($hour === 17 && $minute > 0) {
                continue;
            }
            $validTimes[] = sprintf('%02d:%02d', $hour, $minute);
        }
    }

    $errors = array_filter([
        validateRequired($service, 'Service'),
        ($service !== '' && !in_array($service, $validServices, true)) ? "Select a valid service." : null,
        validateRequired($date, 'Date'),
        $date !== '' ? validateDateNotPast($date, 'Date') : null,
        validateRequired($time, 'Time'),
        ($time !== '' && !in_array($time, $validTimes, true)) ? "Select a valid appointment time." : null,
        strlen($notes) > 500 ? "Notes must be 500 characters or less." : null,
    ]);
    $errors = array_values($errors);

    if (empty($errors)) {
        $notes = htmlspecialchars($notes, ENT_QUOTES, 'UTF-8');
    }

    return [
        'errors' => $errors,
        'data'   => [
            'service'          => $service,
            'appointment_date' => $date,
            'appointment_time' => $time,
            'notes'            => $notes,
        ],
    ];
}