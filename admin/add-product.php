<?php
require 'guard.php';

$adminActive = 'products';
$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product — Admin — Curlétte</title>
    <link rel="icon" href="../assets/C-icon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500,600;1,500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

<?php include 'partials/nav.php'; ?>

<main class="admin-main">

    <div class="admin-page-header">
        <div>
            <h1>Add Product</h1>
            <p>Add a new item to the shop catalog.</p>
        </div>
        <a href="products.php" class="admin-link">← Back to Products</a>
    </div>

    <?php if ($status === 'error' && $message): ?>
        <div class="admin-notice error"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="admin-panel admin-form-panel">
        <form action="create-product.php" method="post" enctype="multipart/form-data" class="admin-form">

            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="price">Price ($)</label>
                <input type="number" id="price" name="price" min="0.01" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="stock_quantity">Starting Stock</label>
                <input type="number" id="stock_quantity" name="stock_quantity" min="0" value="0" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="4" placeholder="What is this product, what does it do..." required></textarea>
            </div>

            <div class="form-group">
                <label for="image">Product Photo</label>
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.webp,.gif" required>
                <small class="admin-form-hint">JPG, PNG, WEBP, or GIF. Max 5MB.</small>
            </div>

            <button type="submit" class="btn-main">ADD PRODUCT</button>
        </form>
    </div>

</main>

</body>
</html>
