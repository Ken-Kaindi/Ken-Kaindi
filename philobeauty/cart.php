<?php
require_once 'includes/config.php';
$pageTitle = 'Your Bag';

$cartItems = [];
$subtotal = 0;

if (!empty($_SESSION['cart'])) {
    $ids = array_keys($_SESSION['cart']);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id IN ($placeholders)");
    $stmt->execute($ids);
    $rows = $stmt->fetchAll();
    foreach ($rows as $row) {
        $qty = $_SESSION['cart'][$row['id']];
        $lineTotal = $row['price'] * $qty;
        $subtotal += $lineTotal;
        $cartItems[] = ['product' => $row, 'qty' => $qty, 'lineTotal' => $lineTotal];
    }
}

require_once 'includes/header.php';
?>

<section class="page-header"><div class="container"><h1>Your bag</h1></div></section>

<section class="cart-page">
  <div class="container">
    <?php if (empty($cartItems)): ?>
      <p class="empty-state">Your bag is empty. <a href="shop.php">Continue shopping &rarr;</a></p>
    <?php else: ?>
      <div class="cart-layout">
        <table class="cart-table">
          <thead>
            <tr><th>Product</th><th>Price</th><th>Qty</th><th>Total</th><th></th></tr>
          </thead>
          <tbody>
            <?php foreach ($cartItems as $item): $p = $item['product']; ?>
              <tr>
                <td class="cart-product">
                  <img src="<?= sanitize(productImage($p)) ?>" alt="<?= sanitize($p['name']) ?>">
                  <div>
                    <a href="product.php?id=<?= (int) $p['id'] ?>"><?= sanitize($p['name']) ?></a>
                    <p class="cart-category"><?= sanitize($p['category_name']) ?></p>
                  </div>
                </td>
                <td><?= formatPrice($p['price']) ?></td>
                <td>
                  <form action="cart-action.php" method="post" class="cart-qty-form">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="action" value="update">
                    <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                    <input type="hidden" name="redirect" value="cart.php">
                    <input type="number" name="quantity" value="<?= (int) $item['qty'] ?>" min="1" max="<?= (int) $p['stock'] ?>" onchange="this.form.submit()">
                  </form>
                </td>
                <td><?= formatPrice($item['lineTotal']) ?></td>
                <td>
                  <form action="cart-action.php" method="post">
                    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                    <input type="hidden" name="action" value="remove">
                    <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
                    <input type="hidden" name="redirect" value="cart.php">
                    <button type="submit" class="remove-btn" aria-label="Remove item">&times;</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
        <aside class="cart-summary">
          <h3>Order summary</h3>
          <div class="summary-row"><span>Subtotal</span><span><?= formatPrice($subtotal) ?></span></div>
          <div class="summary-row"><span>Delivery</span><span>Calculated at checkout</span></div>
          <a href="checkout.php" class="btn btn-primary btn-block">Proceed to checkout</a>
        </aside>
      </div>
    <?php endif; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
