<?php
require_once '../includes/config.php';
requireAdmin();
$pageTitle = 'Customers';

$customers = $pdo->query("SELECT u.id, u.full_name, u.email, u.phone, u.created_at, COUNT(o.id) AS order_count, COALESCE(SUM(CASE WHEN o.status != 'cancelled' THEN o.total_amount ELSE 0 END), 0) AS lifetime_value FROM users u LEFT JOIN orders o ON o.user_id = u.id WHERE u.role = 'customer' GROUP BY u.id ORDER BY u.created_at DESC")->fetchAll();
require_once '../includes/admin-header.php';
?>
<div class="admin-header-row"><div><p class="eyebrow">Customer management</p><h1>Customers</h1></div><span class="admin-count"><?= count($customers) ?> registered</span></div>
<div class="admin-panel table-panel"><div class="table-wrap"><table class="admin-table"><thead><tr><th>Customer</th><th>Contact</th><th>Orders</th><th>Lifetime value</th><th>Joined</th></tr></thead><tbody>
<?php foreach ($customers as $customer): ?><tr><td><strong><?= sanitize($customer['full_name']) ?></strong></td><td><?= sanitize($customer['email']) ?><br><small><?= sanitize($customer['phone'] ?: 'No phone') ?></small></td><td><?= (int) $customer['order_count'] ?></td><td><?= formatPrice($customer['lifetime_value']) ?></td><td><?= date('d M Y', strtotime($customer['created_at'])) ?></td></tr><?php endforeach; ?>
</tbody></table></div></div>
<?php require_once '../includes/admin-footer.php'; ?>
