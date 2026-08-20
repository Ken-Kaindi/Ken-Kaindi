<?php
require_once '../includes/config.php';
requireAdmin();
$pageTitle = 'Orders';

$statusFilter = $_GET['status'] ?? '';
$validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

$sql = "SELECT * FROM orders";
$params = [];
if (in_array($statusFilter, $validStatuses, true)) {
    $sql .= " WHERE status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

require_once '../includes/admin-header.php';
?>

<div class="admin-header-row"><h1>Orders</h1></div>

<div class="admin-filter-tabs">
  <a href="orders.php" class="<?= $statusFilter === '' ? 'active' : '' ?>">All</a>
  <?php foreach ($validStatuses as $s): ?>
    <a href="orders.php?status=<?= $s ?>" class="<?= $statusFilter === $s ? 'active' : '' ?>"><?= ucfirst($s) ?></a>
  <?php endforeach; ?>
</div>

<div class="admin-panel">
  <?php if (empty($orders)): ?>
    <p class="empty-state">No orders found.</p>
  <?php else: ?>
    <div class="table-wrap"><table class="admin-table">
      <thead><tr><th>Order</th><th>Customer</th><th>Phone</th><th>Status</th><th>Payment</th><th>Total</th><th>Date</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($orders as $o): ?>
          <tr>
            <td><?= sanitize(displayOrderNumber($o)) ?></td>
            <td><?= sanitize($o['full_name']) ?></td>
            <td><?= sanitize($o['phone']) ?></td>
            <td><span class="status-badge status-<?= sanitize($o['status']) ?>"><?= ucfirst($o['status']) ?></span></td>
            <td><span class="status-badge payment-<?= sanitize($o['payment_status']) ?>"><?= ucfirst($o['payment_status']) ?></span></td>
            <td><?= formatPrice($o['total_amount']) ?></td>
            <td><?= date('d M Y', strtotime($o['created_at'])) ?></td>
            <td><a href="order-details.php?id=<?= (int) $o['id'] ?>">View &rarr;</a></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table></div>
  <?php endif; ?>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
