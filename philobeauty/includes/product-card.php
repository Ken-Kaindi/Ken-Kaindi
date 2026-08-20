<?php
/**
 * Expects $p — a product row (ideally joined with category_name).
 */
$imgSrc  = productImage($p);
$inStock = (int) $p['stock'] > 0;
?>
<article class="product-card">
  <a href="product.php?id=<?= (int) $p['id'] ?>" class="product-thumb">
    <img src="<?= sanitize($imgSrc) ?>" alt="<?= sanitize($p['name']) ?>" loading="lazy">
    <?php if (!$inStock): ?><span class="stock-flag">Sold out</span><?php endif; ?>
  </a>
  <div class="product-info">
    <p class="product-category"><?= sanitize($p['category_name'] ?? '') ?></p>
    <h3 class="product-name"><a href="product.php?id=<?= (int) $p['id'] ?>"><?= sanitize($p['name']) ?></a></h3>
    <div class="product-card-footer">
      <p class="product-price"><?= formatPrice($p['price']) ?></p>
      <?php if ($inStock): ?>
        <form action="cart-action.php" method="post">
          <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
          <input type="hidden" name="quantity" value="1">
          <input type="hidden" name="redirect" value="<?= sanitize($_SERVER['REQUEST_URI'] ?? 'shop.php') ?>">
          <button type="submit" class="quick-add" aria-label="Add <?= sanitize($p['name']) ?> to bag">Add +</button>
        </form>
      <?php endif; ?>
    </div>
  </div>
</article>
