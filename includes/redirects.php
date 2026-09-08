<?php

// Targets are relative to actions/, the only place resolveRedirectTarget() is called from.
function getRedirectMap(): array
{
    return [
        'home'             => '../index.php#home',
        'services'         => '../services.php',
        'products'         => '../products.php',
        'about'            => '../index.php#about',
        'contact'          => '../index.php#contact',
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
