<?php
require 'guard.php';
require '../data/admin.php';

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
