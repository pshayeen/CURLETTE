<?php

function validateEmailFormat(string $value): ?string
{
    return filter_var($value, FILTER_VALIDATE_EMAIL) ? null : "Enter a valid email address.";
}

function validateRequired(string $value, string $label): ?string
{
    return trim($value) === '' ? "$label is required." : null;
}

function validateIntRange(string $value, string $label, int $min, int $max): ?string
{
    $ok = filter_var($value, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => $min, 'max_range' => $max],
    ]);
    return $ok !== false ? null : "$label must be a whole number between $min and $max.";
}

function validateStudentInput(array $post): array
{
    $username = trim($post['username'] ?? '');
    $email    = trim($post['email'] ?? '');
    $age      = trim($post['age'] ?? '');

    $errors = array_filter([
        validateRequired($username, 'Username'),
        validateEmailFormat($email),
        validateIntRange($age, 'Age', 1, 120),
    ]);
    $errors = array_values($errors);

    if (empty($errors)) {
        $username = htmlspecialchars($username);
        $age      = (int) $age;
    }

    return [
        'errors' => $errors,
        'data'   => ['username' => $username, 'email' => $email, 'age' => $age],
    ];
}

/* ---------- Account (login / signup) ---------- */

function validateUsernameFormat(string $value): ?string
{
    return preg_match('/^[a-zA-Z0-9_]{3,20}$/', $value)
        ? null
        : "Username must be 3-20 characters (letters, numbers, underscores only).";
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
    $password = $post['password'] ?? '';
    $confirm  = $post['confirm_password'] ?? '';

    $errors = array_filter([
        validateRequired($username, 'Username'),
        validateUsernameFormat($username),
        validateRequired($email, 'Email'),
        validateEmailFormat($email),
        validateRequired($password, 'Password'),
        validatePasswordStrength($password),
        $password !== '' && $password !== $confirm ? "Passwords do not match." : null,
    ]);
    $errors = array_values($errors);

    if (empty($errors)) {
        $username = htmlspecialchars($username);
    }

    return [
        'errors' => $errors,
        'data'   => ['username' => $username, 'email' => $email, 'password' => $password],
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
    if (!$date) {
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

    $validServices = ['Curl Consultation', 'Curl Styling', 'Curl Hair Cut', 'Curl Hair Color'];

    $errors = array_filter([
        validateRequired($service, 'Service'),
        ($service !== '' && !in_array($service, $validServices, true)) ? "Select a valid service." : null,
        validateRequired($date, 'Date'),
        $date !== '' ? validateDateNotPast($date, 'Date') : null,
        validateRequired($time, 'Time'),
    ]);
    $errors = array_values($errors);

    if (empty($errors)) {
        $notes = htmlspecialchars($notes);
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