<?php
// needs: $product (optional: $returnTo, $showDetailsTrigger)
$returnTo = $returnTo ?? 'products';
$product  = $product ?? [];
$showDetailsTrigger = $showDetailsTrigger ?? false;
$inStock  = (int) ($product['stock_quantity'] ?? 0) > 0;
?>
<article class="product-card">

    <?php if ($showDetailsTrigger): ?>

        <button type="button" class="product-card-trigger"
                data-bs-toggle="modal"
                data-bs-target="#productModal<?= (int) $product['id'] ?>"
                aria-label="View details for <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
            <div class="product-img">
                <img src="<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
            </div>

            <div class="product-info-preview">
                <h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                <strong><?= formatPrice($product['price']) ?></strong>
            </div>
        </button>

        <?php if ($inStock): ?>
            <form method="post" action="forms/update-cart.php" class="cart-btn-form">
                <input type="hidden" name="add_to_cart" value="<?= (int) $product['id'] ?>">
                <input type="hidden" name="return_to" value="<?= htmlspecialchars($returnTo, ENT_QUOTES, 'UTF-8') ?>">
                <button class="cart-btn" type="submit" aria-label="Add <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?> to cart">
                    <i class="bi bi-handbag"></i>
                </button>
            </form>
        <?php else: ?>
            <span class="stock-status">OUT OF STOCK</span>
        <?php endif; ?>

    <?php else: ?>

        <div class="product-img">
            <img src="<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
        </div>

        <div class="product-info">
            <h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
            <strong><?= formatPrice($product['price']) ?></strong>

            <?php if ($inStock): ?>
                <form method="post" action="forms/update-cart.php" class="cart-btn-form">
                    <input type="hidden" name="add_to_cart" value="<?= (int) $product['id'] ?>">
                    <input type="hidden" name="return_to" value="<?= htmlspecialchars($returnTo, ENT_QUOTES, 'UTF-8') ?>">
                    <button class="cart-btn" type="submit" aria-label="Add <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?> to cart">
                        <i class="bi bi-handbag"></i>
                    </button>
                </form>
            <?php else: ?>
                <span class="stock-status">OUT OF STOCK</span>
            <?php endif; ?>
        </div>

    <?php endif; ?>

</article>
