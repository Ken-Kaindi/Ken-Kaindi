<?php
require_once 'includes/config.php';
requireLogin();
$pageTitle = 'Checkout';

if (empty($_SESSION['cart'])) {
    setFlash('error', 'Your bag is empty.');
    redirect('shop.php');
}

$ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$rows = $stmt->fetchAll();

$cartItems = [];
$subtotal = 0;
foreach ($rows as $row) {
    $qty = $_SESSION['cart'][$row['id']];
    $lineTotal = $row['price'] * $qty;
    $subtotal += $lineTotal;
    $cartItems[] = ['product' => $row, 'qty' => $qty, 'lineTotal' => $lineTotal];
}
$deliveryFee = DELIVERY_FEE;
$total = $subtotal + $deliveryFee;

$userStmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$userStmt->execute([$_SESSION['user_id']]);
$user = $userStmt->fetch();

$counties = ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Uasin Gishu', 'Kiambu', 'Machakos', 'Kajiado', 'Kilifi', 'Other'];

require_once 'includes/header.php';
?>

<section class="page-header"><div class="container"><h1>Checkout</h1></div></section>

<section class="checkout-page">
  <div class="container checkout-grid">
    <form action="place-order.php" method="post" class="checkout-form">
      <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
      <h3>Delivery details</h3>
      <label>Full name<input type="text" name="full_name" required value="<?= sanitize($user['full_name'] ?? '') ?>"></label>
      <label>Phone number<input type="tel" name="phone" required value="<?= sanitize($user['phone'] ?? '') ?>"></label>
      <label>Email<input type="email" name="email" required value="<?= sanitize($user['email'] ?? '') ?>"></label>
      <label>County
        <select name="county" required>
          <?php foreach ($counties as $county): ?>
            <option value="<?= sanitize($county) ?>"><?= sanitize($county) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <label>Delivery address<textarea name="address" rows="3" required placeholder="Street, building, house/unit number"></textarea></label>
      <label>Order notes <span class="optional-label">Optional</span><textarea name="notes" rows="2" placeholder="Delivery directions, preferred call time or product notes"></textarea></label>

      <h3>Payment method</h3>
      <label class="radio-option"><input type="radio" name="payment_method" value="Cash on Delivery" checked> Cash on Delivery</label>
      <label class="radio-option"><input type="radio" name="payment_method" value="M-Pesa"> M-Pesa <small>Payment details are confirmed after placing the order.</small></label>

      <button type="submit" class="btn btn-primary btn-block">Place order</button>
    </form>

    <aside class="cart-summary">
      <h3>Order summary</h3>
      <?php foreach ($cartItems as $item): ?>
        <div class="summary-row"><span><?= sanitize($item['product']['name']) ?> &times; <?= (int) $item['qty'] ?></span><span><?= formatPrice($item['lineTotal']) ?></span></div>
      <?php endforeach; ?>
      <div class="summary-row"><span>Subtotal</span><span><?= formatPrice($subtotal) ?></span></div>
      <div class="summary-row"><span>Delivery</span><span><?= formatPrice($deliveryFee) ?></span></div>
      <div class="summary-row summary-total"><span>Total</span><span><?= formatPrice($total) ?></span></div>
      <div class="checkout-assurance"><span>✓ Secure account checkout</span><span>✓ Stock confirmed before order</span><span>✓ Trackable delivery</span></div>
    </aside>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
