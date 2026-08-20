<?php
require_once '../includes/config.php';
requireAdmin();
$pageTitle = 'Payments';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $id = (int) ($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? 'pending';
    $reference = trim($_POST['transaction_reference'] ?? '');
    if (in_array($status, ['pending', 'paid', 'failed', 'refunded'], true)) {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("UPDATE payments SET status = ?, transaction_reference = ?, paid_at = CASE WHEN ? = 'paid' THEN COALESCE(paid_at, NOW()) ELSE paid_at END WHERE id = ?");
        $stmt->execute([$status, $reference, $status, $id]);
        $pdo->prepare("UPDATE orders o JOIN payments p ON p.order_id = o.id SET o.payment_status = p.status WHERE p.id = ?")->execute([$id]);
        $pdo->commit();
        setFlash('success', 'Payment record updated.');
        redirect('payments.php');
    }
}

$payments = $pdo->query("SELECT p.*, o.order_number, o.full_name, o.phone FROM payments p JOIN orders o ON o.id = p.order_id ORDER BY p.created_at DESC")->fetchAll();
require_once '../includes/admin-header.php';
?>
<div class="admin-header-row"><div><p class="eyebrow">Transaction control</p><h1>Payments</h1></div></div>
<div class="admin-panel table-panel"><div class="table-wrap"><table class="admin-table"><thead><tr><th>Order</th><th>Customer</th><th>Method</th><th>Amount</th><th>Status</th><th>Reference</th><th>Update</th></tr></thead><tbody>
<?php foreach ($payments as $payment): ?><tr><td><?= sanitize($payment['order_number']) ?></td><td><?= sanitize($payment['full_name']) ?><br><small><?= sanitize($payment['phone']) ?></small></td><td><?= sanitize($payment['method']) ?></td><td><?= formatPrice($payment['amount']) ?></td><td><span class="status-badge payment-<?= sanitize($payment['status']) ?>"><?= ucfirst($payment['status']) ?></span></td><td><?= sanitize($payment['transaction_reference'] ?: '—') ?></td><td><form method="post" class="table-form"><input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>"><input type="hidden" name="id" value="<?= (int) $payment['id'] ?>"><select name="status"><?php foreach (['pending','paid','failed','refunded'] as $status): ?><option value="<?= $status ?>" <?= $payment['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option><?php endforeach; ?></select><input type="text" name="transaction_reference" value="<?= sanitize($payment['transaction_reference']) ?>" placeholder="M-Pesa/reference"><button class="btn btn-primary btn-small" type="submit">Save</button></form></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php require_once '../includes/admin-footer.php'; ?>
