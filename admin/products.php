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
    <link rel="icon" href="../assets/images/C-icon.png" type="image/png">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500,600;1,500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
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
                    <th></th>
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

                        <td>
                            <form method="post" action="product-actions.php" class="admin-price-form">
                                <input type="hidden" name="update_price" value="1">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                <span>PHP</span>
                                <input type="number" name="price" min="0.01" step="0.01" value="<?= htmlspecialchars((string) $product['price'], ENT_QUOTES, 'UTF-8') ?>">
                                <button type="submit" class="admin-btn-outline">Save</button>
                            </form>
                        </td>

                        <td>
                            <span class="<?= (int) $product['stock_quantity'] <= 5 ? 'admin-stock-low' : '' ?>">
                                <?= (int) $product['stock_quantity'] ?>
                                <?= (int) $product['stock_quantity'] <= 5 ? ' (low)' : '' ?>
                            </span>
                        </td>

                        <td>
                            <form method="post" action="product-actions.php" class="admin-stock-form">
                                <input type="hidden" name="update_stock" value="1">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                <input type="number" name="stock_quantity" min="0" value="<?= (int) $product['stock_quantity'] ?>">
                                <button type="submit" class="admin-btn-outline">Save</button>
                            </form>
                        </td>

                        <td>
                            <form method="post" action="product-actions.php" enctype="multipart/form-data" class="admin-photo-form">
                                <input type="hidden" name="update_image" value="1">
                                <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp,.gif">
                                <button type="submit" class="admin-btn-outline">Upload</button>
                            </form>
                        </td>

                        <td>
                            <button type="button" class="admin-btn-danger"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteProductModal"
                                    data-id="<?= (int) $product['id'] ?>"
                                    data-label="Remove &ldquo;<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>&rdquo; from the catalog? This can't be undone.">
                                Remove
                            </button>
                        </td>

                    </tr>
                <?php endforeach; ?>
            </tbody>

        </table>
    </div>

</main>

<!-- delete product confirmation modal -->
<div class="modal fade" id="deleteProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="post" action="product-actions.php" id="deleteProductForm">
            <input type="hidden" name="delete_product" value="1">
            <div class="modal-content confirm-modal-content danger">
                <div class="confirm-modal-icon"><i class="bi bi-exclamation-triangle"></i></div>
                <h2>Remove this product?</h2>
                <p id="deleteProductLabel">This can't be undone.</p>
                <input type="hidden" id="deleteProductIdField" name="product_id" value="">
                <div class="confirm-modal-actions">
                    <button type="button" class="btn-outline" data-bs-dismiss="modal">GO BACK</button>
                    <button type="submit" class="btn-main confirm-modal-danger">YES, REMOVE</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="../assets/js/admin.js"></script>

</body>
</html>
