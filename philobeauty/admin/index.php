<?php
require_once '../includes/config.php';
requireAdmin();
$pageTitle = 'Dashboard';

$totalProducts = (int) $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalOrders = (int) $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalCustomers = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
$totalRevenue = (float) $pdo->query("SELECT COALESCE(SUM(amount), 0) FROM payments WHERE status = 'paid'")->fetchColumn();
$lowStock = (int) $pdo->query("SELECT COUNT(*) FROM products WHERE stock <= 5")->fetchColumn();
$pendingOrders = (int) $pdo->query("SELECT COUNT(*) FROM orders WHERE status IN ('pending','processing')")->fetchColumn();
$activeDeliveries = (int) $pdo->query("SELECT COUNT(*) FROM deliveries WHERE status != 'delivered'")->fetchColumn();

$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC LIMIT 6")->fetchAll();

require_once '../includes/admin-header.php';
?>

<div class="admin-header-row"><div><p class="eyebrow">Operations overview</p><h1>Dashboard</h1><p class="admin-subtitle">Welcome back, <?= sanitize($_SESSION['user_name'] ?? 'Administrator') ?>.</p></div><a href="product-form.php" class="btn btn-primary">+ Add product</a></div>

<div class="stat-grid">
  <div class="stat-card"><span class="stat-icon">◇</span><p class="stat-label">Products</p><p class="stat-value"><?= $totalProducts ?></p></div>
  <div class="stat-card"><span class="stat-icon">▤</span><p class="stat-label">All orders</p><p class="stat-value"><?= $totalOrders ?></p></div>
  <div class="stat-card"><span class="stat-icon">○</span><p class="stat-label">Customers</p><p class="stat-value"><?= $totalCustomers ?></p></div>
  <div class="stat-card"><span class="stat-icon">KSh</span><p class="stat-label">Paid revenue</p><p class="stat-value stat-value-money"><?= formatPrice($totalRevenue) ?></p></div>
  <div class="stat-card"><span class="stat-icon">↗</span><p class="stat-label">Active deliveries</p><p class="stat-value"><?= $activeDeliveries ?></p></div>
  <div class="stat-card stat-warning"><span class="stat-icon">!</span><p class="stat-label">Low stock</p><p class="stat-value"><?= $lowStock ?></p></div>
</div>

<div class="admin-quick-grid">
  <a href="orders.php?status=pending"><strong>Review new orders</strong><span><?= $pendingOrders ?> need attention &rarr;</span></a>
  <a href="deliveries.php"><strong>Manage deliveries</strong><span><?= $activeDeliveries ?> active records &rarr;</span></a>
  <a href="reports.php"><strong>View business reports</strong><span>Sales, products and status &rarr;</span></a>
</div>

<div class="admin-panel">
  <div class="panel-head"><h3>Recent orders</h3><a href="orders.php">View all &rarr;</a></div>
  <?php if (empty($recentOrders)): ?>
    <p class="empty-state">No orders yet.</p>
  <?php else: ?>
    <div class="table-wrap"><table class="admin-table">
      <thead><tr><th>Order</th><th>Customer</th><th>Status</th><th>Payment</th><th>Total</th><th>Date</th></tr></thead>
      <tbody>
        <?php foreach ($recentOrders as $o): ?>
          <tr>
            <td><a class="table-link" href="order-details.php?id=<?= (int) $o['id'] ?>"><?= sanitize(displayOrderNumber($o)) ?></a></td>
            <td><?= sanitize($o['full_name']) ?></td>
            <td><span class="status-badge status-<?= sanitize($o['status']) ?>"><?= ucfirst($o['status']) ?></span></td>
            <td><span class="status-badge payment-<?= sanitize($o['payment_status']) ?>"><?= ucfirst($o['payment_status']) ?></span></td>
            <td><?= formatPrice($o['total_amount']) ?></td>
            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endif; ?>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
