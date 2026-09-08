<?php

/**
 * Where the general "BOOK AN APPOINTMENT" button should go: straight to the
 * booking page if logged in, otherwise to login with a redirect back to it.
 */
function bookHref(bool $isLoggedIn): string
{
    return $isLoggedIn
        ? 'bookAppointment.php'
        : 'account.php?mode=login&redirect=book-appointment';
}

/**
 * Where a specific service card should go: straight to the booking page with
 * that service pre-selected if logged in, otherwise to login first.
 */
function serviceHref(string $serviceName, bool $isLoggedIn): string
{
    return $isLoggedIn
        ? 'bookAppointment.php?service=' . urlencode($serviceName)
        : 'account.php?mode=login&redirect=book-appointment';
}
