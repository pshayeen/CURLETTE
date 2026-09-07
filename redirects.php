<?php

function getRedirectMap(): array
{
    return [
        'home'             => 'index.php#home',
        'services'         => 'index.php#services',
        'products'         => 'index.php#products',
        'about'            => 'index.php#about',
        'contact'          => 'index.php#contact',
        'book-appointment' => 'bookAppointment.php',
    ];
}

function isAllowedRedirect(string $key): bool
{
    return array_key_exists($key, getRedirectMap());
}

function resolveRedirectTarget(string $key): string
{
    $map = getRedirectMap();
    return $map[$key] ?? ' index.php';
}