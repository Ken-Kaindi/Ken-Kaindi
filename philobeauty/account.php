<?php
require_once 'includes/config.php';
requireLogin();
$pageTitle = 'My Account';

$ordersStmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC");
$ordersStmt->execute([$_SESSION['user_id']]);
$orders = $ordersStmt->fetchAll();

require_once 'includes/header.php';
?>

<section class="page-header"><div class="container"><h1>My account</h1></div></section>

<section class="account-page">
  <div class="container">
    <h3>Hello, <?= sanitize($_SESSION['user_name']) ?></h3>
    <h4>Order history</h4>
    <?php if (empty($orders)): ?>
      <p class="empty-state">You haven't placed any orders yet. <a href="shop.php">Start shopping &rarr;</a></p>
    <?php else: ?>
      <table class="orders-table">
        <thead><tr><th>Order</th><th>Date</th><th>Status</th><th>Payment</th><th>Total</th><th></th></tr></thead>
        <tbody>
          <?php foreach ($orders as $o): ?>
            <tr>
              <td><?= sanitize(displayOrderNumber($o)) ?></td>
              <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
              <td><span class="status-badge status-<?= sanitize($o['status']) ?>"><?= ucfirst($o['status']) ?></span></td>
              <td><span class="status-badge payment-<?= sanitize($o['payment_status']) ?>"><?= ucfirst($o['payment_status']) ?></span></td>
              <td><?= formatPrice($o['total_amount']) ?></td>
              <td><a class="table-link" href="track-order.php?code=<?= urlencode($o['tracking_code']) ?>">Track &rarr;</a></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    <?php endif; ?>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
