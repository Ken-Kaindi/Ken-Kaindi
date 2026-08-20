<?php
require_once '../includes/config.php';
requireAdmin();
$pageTitle = 'Reports';

$from = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['from'] ?? '') ? $_GET['from'] : date('Y-m-01');
$to = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_GET['to'] ?? '') ? $_GET['to'] : date('Y-m-d');
$range = [$from . ' 00:00:00', $to . ' 23:59:59'];

$summaryStmt = $pdo->prepare("SELECT COUNT(*) AS orders, COALESCE(SUM(CASE WHEN status!='cancelled' THEN total_amount ELSE 0 END),0) AS order_value, COALESCE(AVG(CASE WHEN status!='cancelled' THEN total_amount END),0) AS average_order, SUM(status='delivered') AS delivered FROM orders WHERE created_at BETWEEN ? AND ?");
$summaryStmt->execute($range); $summary = $summaryStmt->fetch();
$paidStmt = $pdo->prepare("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='paid' AND created_at BETWEEN ? AND ?");
$paidStmt->execute($range); $paidRevenue = (float) $paidStmt->fetchColumn();
$productStmt = $pdo->prepare("SELECT oi.product_name, SUM(oi.quantity) AS units, SUM(oi.subtotal) AS sales FROM order_items oi JOIN orders o ON o.id=oi.order_id WHERE o.status!='cancelled' AND o.created_at BETWEEN ? AND ? GROUP BY oi.product_name ORDER BY units DESC LIMIT 8");
$productStmt->execute($range); $topProducts = $productStmt->fetchAll();
$statusStmt = $pdo->prepare("SELECT status, COUNT(*) AS total FROM orders WHERE created_at BETWEEN ? AND ? GROUP BY status ORDER BY total DESC");
$statusStmt->execute($range); $statusBreakdown = $statusStmt->fetchAll();
$paymentStmt = $pdo->prepare("SELECT method, COUNT(*) AS total, COALESCE(SUM(amount),0) AS value FROM payments WHERE created_at BETWEEN ? AND ? GROUP BY method");
$paymentStmt->execute($range); $paymentBreakdown = $paymentStmt->fetchAll();

require_once '../includes/admin-header.php';
?>
<div class="admin-header-row no-print"><div><p class="eyebrow">Business intelligence</p><h1>Reports</h1></div><button class="btn btn-ghost" onclick="window.print()">Print report</button></div>
<form class="report-filter no-print" method="get"><label>From<input type="date" name="from" value="<?= sanitize($from) ?>"></label><label>To<input type="date" name="to" value="<?= sanitize($to) ?>"></label><button class="btn btn-primary" type="submit">Apply dates</button></form>
<div class="report-heading"><img src="../assets/images/philobeauty-logo.webp" alt="PhiloBeauty"><div><h2>Sales and operations report</h2><p><?= date('d M Y', strtotime($from)) ?> – <?= date('d M Y', strtotime($to)) ?></p></div></div>
<div class="stat-grid report-stats"><div class="stat-card"><p class="stat-label">Orders</p><p class="stat-value"><?= (int) $summary['orders'] ?></p></div><div class="stat-card"><p class="stat-label">Order value</p><p class="stat-value stat-value-money"><?= formatPrice($summary['order_value']) ?></p></div><div class="stat-card"><p class="stat-label">Paid revenue</p><p class="stat-value stat-value-money"><?= formatPrice($paidRevenue) ?></p></div><div class="stat-card"><p class="stat-label">Average order</p><p class="stat-value stat-value-money"><?= formatPrice($summary['average_order']) ?></p></div><div class="stat-card"><p class="stat-label">Delivered</p><p class="stat-value"><?= (int) $summary['delivered'] ?></p></div></div>
<div class="report-grid"><section class="admin-panel"><h3>Top products</h3><div class="table-wrap"><table class="admin-table"><thead><tr><th>Product</th><th>Units</th><th>Sales</th></tr></thead><tbody><?php foreach ($topProducts as $row): ?><tr><td><?= sanitize($row['product_name']) ?></td><td><?= (int) $row['units'] ?></td><td><?= formatPrice($row['sales']) ?></td></tr><?php endforeach; ?></tbody></table></div></section><section class="admin-panel"><h3>Order status</h3><?php foreach ($statusBreakdown as $row): ?><div class="report-line"><span><?= ucfirst($row['status']) ?></span><strong><?= (int) $row['total'] ?></strong></div><?php endforeach; ?><h3 class="report-subtitle">Payment methods</h3><?php foreach ($paymentBreakdown as $row): ?><div class="report-line"><span><?= sanitize($row['method']) ?></span><strong><?= (int) $row['total'] ?> · <?= formatPrice($row['value']) ?></strong></div><?php endforeach; ?></section></div>
<?php require_once '../includes/admin-footer.php'; ?>
