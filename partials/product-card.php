<?php

$detailed = $detailed ?? false;
$returnTo = $returnTo ?? 'products';
$inStock  = (int) $product['stock_quantity'] > 0;
?>
<article class="product-card<?= $detailed ? ' product-card--detailed' : '' ?>">

    <div class="product-img">
        <img src="<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
    </div>

    <div class="product-info">
        <h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
        <?php if ($detailed): ?>
            <p><?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?></p>
        <?php endif; ?>
        <strong><?= formatPrice($product['price']) ?></strong>

        <?php if ($inStock): ?>
            <form method="post" action="cart_function.php" class="cart-btn-form">
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

</article>
