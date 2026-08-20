<?php
require_once 'includes/config.php';

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    setFlash('error', 'That product could not be found.');
    redirect('shop.php');
}

$pageTitle = $product['name'];

$relStmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? ORDER BY RAND() LIMIT 4");
$relStmt->execute([$product['category_id'], $product['id']]);
$related = $relStmt->fetchAll();

require_once 'includes/header.php';

$imgSrc = productImage($product);
?>

<section class="product-detail">
  <div class="container product-detail-grid">
    <div class="product-gallery">
      <img src="<?= sanitize($imgSrc) ?>" alt="<?= sanitize($product['name']) ?>">
    </div>
    <div class="product-panel">
      <p class="eyebrow"><a href="shop.php?category=<?= urlencode($product['category_slug']) ?>"><?= sanitize($product['category_name']) ?></a></p>
      <h1><?= sanitize($product['name']) ?></h1>
      <p class="product-detail-price"><?= formatPrice($product['price']) ?></p>
      <p class="product-detail-desc"><?= nl2br(sanitize($product['description'])) ?></p>

      <div class="product-assurance">
        <span>✓ Authentic product</span><span>✓ Secure checkout</span><span>✓ Machakos support</span>
      </div>

      <?php if ((int) $product['stock'] > 0): ?>
        <p class="stock-status in-stock">In stock &mdash; <?= (int) $product['stock'] ?> left</p>
        <form action="cart-action.php" method="post" class="add-to-cart-form">
          <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
          <input type="hidden" name="action" value="add">
          <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
          <input type="hidden" name="redirect" value="cart.php">
          <div class="qty-control">
            <button type="button" class="qty-btn" data-action="decrease" aria-label="Decrease quantity">&minus;</button>
            <input type="number" name="quantity" value="1" min="1" max="<?= (int) $product['stock'] ?>" id="qtyInput">
            <button type="button" class="qty-btn" data-action="increase" aria-label="Increase quantity">+</button>
          </div>
          <button type="submit" class="btn btn-primary">Add to bag</button>
        </form>
      <?php else: ?>
        <p class="stock-status out-of-stock">Currently sold out</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php if ($related): ?>
<section class="related-products">
  <div class="container">
    <h2 class="section-title">You may also like</h2>
    <div class="product-grid">
      <?php foreach ($related as $p): ?>
        <?php include 'includes/product-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
