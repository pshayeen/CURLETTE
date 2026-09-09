<?php
require 'guard.php';
require '../data/admin.php';

if (isset($_POST['update_info'])) {
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
}

if (isset($_POST['delete_customer'])) {
    $customerId = filter_input(INPUT_POST, 'customer_id', FILTER_VALIDATE_INT);

    if (!$customerId) {
        header('Location: customers.php?status=error&message=' . urlencode('Invalid account.'));
        exit;
    }

    $ok = deleteCustomerAccount($customerId);

    header($ok
        ? 'Location: customers.php?status=success&message=' . urlencode('Account deleted.')
        : 'Location: customers.php?status=error&message=' . urlencode('Could not delete that account.'));
    exit;
}

header('Location: customers.php');
exit;
