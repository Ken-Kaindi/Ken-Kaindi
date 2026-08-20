<?php
require_once 'includes/config.php';
requireLogin();
$pageTitle = 'Order Confirmed';

$orderId = (int) ($_GET['order_id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $_SESSION['user_id']]);
$order = $stmt->fetch();

if (!$order) {
    redirect('account.php');
}

$itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemStmt->execute([$orderId]);
$items = $itemStmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="confirmation-page">
  <div class="container">
    <div class="confirmation-box">
      <span class="confirm-icon">&check;</span>
      <h1>Thank you, <?= sanitize($order['full_name']) ?>.</h1>
      <p>Your order <strong><?= sanitize(displayOrderNumber($order)) ?></strong> has been received.</p>
      <div class="tracking-code-box"><span>Tracking code</span><strong><?= sanitize($order['tracking_code']) ?></strong><small>Use this code with <?= sanitize($order['phone']) ?> to follow your delivery.</small></div>
      <table class="order-items-table">
        <?php foreach ($items as $item): ?>
          <tr><td><?= sanitize($item['product_name']) ?> &times; <?= (int) $item['quantity'] ?></td><td><?= formatPrice($item['subtotal']) ?></td></tr>
        <?php endforeach; ?>
        <tr class="order-total-row"><td>Total</td><td><?= formatPrice($order['total_amount']) ?></td></tr>
      </table>
      <p>We'll deliver to <?= sanitize($order['address']) ?>, <?= sanitize($order['county']) ?> &mdash; payment via <?= sanitize($order['payment_method']) ?>.</p>
      <div class="confirmation-actions"><a href="track-order.php?code=<?= urlencode($order['tracking_code']) ?>" class="btn btn-primary">Track order</a><a href="shop.php" class="btn btn-ghost">Continue shopping</a></div>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
