<?php

// checks and saves an uploaded product photo
// returns ['success' => bool, 'path' => string|null, 'error' => string|null]
// error is 'no_file' when nothing was picked, so callers can decide if that's ok
function handleProductImageUpload(array $file): array
{
    $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
    $maxSizeBytes = 5 * 1024 * 1024;

    if (!isset($file['error']) || is_array($file['error'])) {
        return ['success' => false, 'path' => null, 'error' => 'Invalid upload.'];
    }

    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'path' => null, 'error' => 'no_file'];
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'path' => null, 'error' => 'Upload failed. Please try again.'];
    }

    if ($file['size'] > $maxSizeBytes) {
        return ['success' => false, 'path' => null, 'error' => 'Image must be smaller than 5MB.'];
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedExtensions, true)) {
        return ['success' => false, 'path' => null, 'error' => 'Image must be a JPG, PNG, WEBP, or GIF file.'];
    }

    // make sure it's a real image, not just a renamed file
    if (@getimagesize($file['tmp_name']) === false) {
        return ['success' => false, 'path' => null, 'error' => "That file doesn't look like a valid image."];
    }

    $uploadDir = __DIR__ . '/../assets/products/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid('product_', true) . '.' . $extension;

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        return ['success' => false, 'path' => null, 'error' => 'Could not save the uploaded image.'];
    }

    return ['success' => true, 'path' => 'assets/products/' . $filename, 'error' => null];
}
