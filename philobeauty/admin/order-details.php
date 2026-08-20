<?php
require_once '../includes/config.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    setFlash('error', 'Order not found.');
    redirect('orders.php');
}
$pageTitle = 'Order ' . displayOrderNumber($order);

$validStatuses = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $newStatus = $_POST['status'] ?? '';
    if (in_array($newStatus, $validStatuses, true)) {
        $upd = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $upd->execute([$newStatus, $id]);
        $deliveryStatus = match ($newStatus) { 'processing' => 'assigned', 'shipped' => 'out_for_delivery', 'delivered' => 'delivered', default => null };
        if ($deliveryStatus) {
            $pdo->prepare("UPDATE deliveries SET status=?, dispatched_at=CASE WHEN ?='out_for_delivery' THEN COALESCE(dispatched_at,NOW()) ELSE dispatched_at END, delivered_at=CASE WHEN ?='delivered' THEN COALESCE(delivered_at,NOW()) ELSE delivered_at END WHERE order_id=?")->execute([$deliveryStatus, $deliveryStatus, $deliveryStatus, $id]);
        }
        setFlash('success', 'Order status updated.');
        redirect('order-details.php?id=' . $id);
    }
}

$itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemStmt->execute([$id]);
$items = $itemStmt->fetchAll();

require_once '../includes/admin-header.php';
?>

<div class="admin-header-row"><div><p class="eyebrow">Order management</p><h1><?= sanitize(displayOrderNumber($order)) ?></h1><p class="admin-subtitle">Tracking: <?= sanitize($order['tracking_code']) ?></p></div><a href="../track-order.php?code=<?= urlencode($order['tracking_code']) ?>" class="btn btn-ghost" target="_blank">Customer view</a></div>

<div class="order-details-grid">
  <div class="admin-panel">
    <h3>Items</h3>
    <table class="admin-table">
      <thead><tr><th>Product</th><th>Price</th><th>Qty</th><th>Subtotal</th></tr></thead>
      <tbody>
        <?php foreach ($items as $item): ?>
          <tr>
            <td><?= sanitize($item['product_name']) ?></td>
            <td><?= formatPrice($item['price']) ?></td>
            <td><?= (int) $item['quantity'] ?></td>
            <td><?= formatPrice($item['subtotal']) ?></td>
          </tr>
        <?php endforeach; ?>
        <tr class="order-total-row"><td colspan="3">Total</td><td><?= formatPrice($order['total_amount']) ?></td></tr>
      </tbody>
    </table>
  </div>
  <div class="admin-panel">
    <h3>Customer &amp; delivery</h3>
    <p><strong><?= sanitize($order['full_name']) ?></strong></p>
    <p><?= sanitize($order['phone']) ?> &middot; <?= sanitize($order['email']) ?></p>
    <p><?= sanitize($order['address']) ?>, <?= sanitize($order['county']) ?></p>
    <p>Payment: <?= sanitize($order['payment_method']) ?></p>
    <p>Payment status: <span class="status-badge payment-<?= sanitize($order['payment_status']) ?>"><?= ucfirst($order['payment_status']) ?></span></p>
    <p>Placed: <?= date('d M Y, H:i', strtotime($order['created_at'])) ?></p>
    <?php if (!empty($order['notes'])): ?><p><strong>Notes:</strong> <?= nl2br(sanitize($order['notes'])) ?></p><?php endif; ?>

    <h3>Update status</h3>
    <form method="post" class="admin-form admin-form-inline">
      <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
      <select name="status">
        <?php foreach ($validStatuses as $s): ?>
          <option value="<?= $s ?>" <?= $order['status'] === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
        <?php endforeach; ?>
      </select>
      <button type="submit" class="btn btn-primary">Update</button>
    </form>
  </div>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
