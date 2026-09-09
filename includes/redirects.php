<?php

// used after login/signup to send the user back
function getRedirectMap(): array
{
    return [
        'home'             => '../index.php#home',
        'services'         => '../services.php',
        'products'         => '../products.php',
        'about'            => '../about.php',
        'contact'          => '../contact.php',
        'book-appointment' => '../bookAppointment.php',
        'cart'             => '../cart.php',
    ];
}

function isAllowedRedirect(string $key): bool
{
    return array_key_exists($key, getRedirectMap());
}

function resolveRedirectTarget(string $key): string
{
    $map = getRedirectMap();
    return $map[$key] ?? '../index.php';
}
