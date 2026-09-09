<?php
require 'guard.php';
require '../data/products.php';
require '../includes/helpers.php';

$adminActive = 'products';
$products = getProducts();
$status  = $_GET['status'] ?? null;
$message = $_GET['message'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Admin | Curlétte</title>
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
            <h1>Products</h1>
            <p><?= count($products) ?> product<?= count($products) === 1 ? '' : 's' ?> in the catalog.</p>
        </div>
        <a href="add-product.php" class="btn-main">+ ADD PRODUCT</a>
    </div>

    <?php if ($status === 'success'): ?>
        <div class="admin-notice success"><?= htmlspecialchars($message ?: 'Stock updated.', ENT_QUOTES, 'UTF-8') ?></div>
    <?php elseif ($status === 'error' && $message): ?>
        <div class="admin-notice error"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>

    <div class="admin-table-wrap">
        <table class="admin-table">

            <thead>
                <tr>
                    <th></th>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Update Stock</th>
                    <th>Change Photo</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>

                        <td>
                            <div class="admin-table-img">
                                <img src="../<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" alt="">
                            </div>
                        </td>

                        <td>
                            <strong><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                        </td>

                        <td><?= formatPrice($product['price']) ?></td>

                        <td>
                            <span class="<?= (int) $product['stock_quantity'] <= 5 ? 'admin-stock-low' : '' ?>">
                                <?= (int) $product['stock_quantity'] ?>
                                <?= (int) $product['stock_quantity'] <= 5 ? ' (low)' : '' ?>
                            </span>
                        </td>

                        <td>
                            <form method="post" action="update-stock.php" class="admin-stock-form">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                <input type="number" name="stock_quantity" min="0" value="<?= (int) $product['stock_quantity'] ?>">
                                <button type="submit">Save</button>
                            </form>
                        </td>

                        <td>
                            <form method="post" action="update-product-image.php" enctype="multipart/form-data" class="admin-photo-form">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif">
                                <button type="submit">Upload</button>
                            </form>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

</main>

</body>
</html>
