<?php
require_once '../includes/config.php';
requireAdmin();
$pageTitle = 'Deliveries';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'waiting';
    $rider = trim($_POST['rider_name'] ?? 'Not assigned');
    $riderPhone = trim($_POST['rider_phone'] ?? '');
    $eta = max(0, (int) ($_POST['estimated_minutes'] ?? 60));
    if (in_array($status, ['waiting', 'assigned', 'out_for_delivery', 'delivered'], true)) {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE deliveries SET rider_name=?, rider_phone=?, status=?, estimated_minutes=?, dispatched_at=CASE WHEN ?='out_for_delivery' THEN COALESCE(dispatched_at,NOW()) ELSE dispatched_at END, delivered_at=CASE WHEN ?='delivered' THEN COALESCE(delivered_at,NOW()) ELSE delivered_at END WHERE id=?");
        $stmt->execute([$rider, $riderPhone, $status, $eta, $status, $status, $id]);
        $orderStatus = match ($status) { 'out_for_delivery' => 'shipped', 'delivered' => 'delivered', 'assigned' => 'processing', default => 'pending' };
        $pdo->prepare("UPDATE orders o JOIN deliveries d ON d.order_id=o.id SET o.status=? WHERE d.id=? AND o.status!='cancelled'")->execute([$orderStatus, $id]);
        $pdo->commit();
        setFlash('success', 'Delivery status updated.');
        redirect('deliveries.php');
    }
}

$deliveries = $pdo->query("SELECT d.*, o.order_number, o.full_name, o.phone, o.address, o.county FROM deliveries d JOIN orders o ON o.id=d.order_id ORDER BY d.updated_at DESC")->fetchAll();
require_once '../includes/admin-header.php';
?>
<div class="admin-header-row"><div><p class="eyebrow">Fulfilment</p><h1>Deliveries</h1></div></div>
<div class="admin-panel table-panel"><div class="table-wrap"><table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>Destination</th><th>Status</th><th>Rider &amp; ETA</th><th>Update</th></tr></thead><tbody>
<?php foreach ($deliveries as $delivery): ?><tr><td><?= sanitize($delivery['order_number']) ?></td><td><?= sanitize($delivery['full_name']) ?><br><small><?= sanitize($delivery['phone']) ?></small></td><td><?= sanitize($delivery['address']) ?><br><small><?= sanitize($delivery['county']) ?></small></td><td><span class="status-badge delivery-<?= sanitize($delivery['status']) ?>"><?= ucwords(str_replace('_',' ',$delivery['status'])) ?></span></td><td><?= sanitize($delivery['rider_name']) ?><br><small><?= (int) $delivery['estimated_minutes'] ?> minutes</small></td><td><form method="post" class="table-form"><input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>"><input type="hidden" name="id" value="<?= (int) $delivery['id'] ?>"><input name="rider_name" value="<?= sanitize($delivery['rider_name']) ?>" placeholder="Rider name"><input name="rider_phone" value="<?= sanitize($delivery['rider_phone']) ?>" placeholder="Rider phone"><select name="status"><?php foreach (['waiting','assigned','out_for_delivery','delivered'] as $status): ?><option value="<?= $status ?>" <?= $delivery['status'] === $status ? 'selected' : '' ?>><?= ucwords(str_replace('_',' ',$status)) ?></option><?php endforeach; ?></select><input type="number" name="estimated_minutes" min="0" value="<?= (int) $delivery['estimated_minutes'] ?>"><button class="btn btn-primary btn-small" type="submit">Save</button></form></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php require_once '../includes/admin-footer.php'; ?>
