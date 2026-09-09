<?php
require 'guard.php';
require '../data/admin.php';

$customerId = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';

if (!$customerId) {
    header('Location: customers.php?status=error&message=' . urlencode('Invalid account.'));
    exit;
}

$result = updateCustomerAccount($customerId, $username, $email);

if (!$result['success']) {
    header('Location: customers.php?status=error&message=' . urlencode($result['errors'][0]));
    exit;
}

header('Location: customers.php?status=success&message=' . urlencode('Account updated.'));
exit;
