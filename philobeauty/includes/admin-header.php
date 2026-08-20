<?php
$flash = getFlash();
$current = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? sanitize($pageTitle) . ' — PhiloBeauty Admin' : 'PhiloBeauty Admin' ?></title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<meta name="theme-color" content="#7B0F34">
<link rel="stylesheet" href="../<?= sanitize(assetUrl('assets/css/style.css')) ?>">
</head>
<body class="admin-body">
<div class="admin-shell" id="adminShell">
  <aside class="admin-sidebar" id="adminSidebar">
    <a href="index.php" class="admin-brand-logo"><img src="../<?= sanitize(assetUrl('assets/images/philobeauty-logo.webp')) ?>" alt="PhiloBeauty" width="180" height="120"></a>
    <p class="admin-tag">Admin console</p>
    <nav class="admin-nav">
      <a href="index.php" class="<?= $current === 'index.php' ? 'active' : '' ?>">Dashboard</a>
      <a href="products.php" class="<?= in_array($current, ['products.php', 'product-form.php']) ? 'active' : '' ?>">Products</a>
      <a href="categories.php" class="<?= $current === 'categories.php' ? 'active' : '' ?>">Categories</a>
      <a href="orders.php" class="<?= in_array($current, ['orders.php', 'order-details.php']) ? 'active' : '' ?>">Orders</a>
      <a href="customers.php" class="<?= $current === 'customers.php' ? 'active' : '' ?>">Customers</a>
      <a href="payments.php" class="<?= $current === 'payments.php' ? 'active' : '' ?>">Payments</a>
      <a href="deliveries.php" class="<?= $current === 'deliveries.php' ? 'active' : '' ?>">Deliveries</a>
      <a href="reports.php" class="<?= $current === 'reports.php' ? 'active' : '' ?>">Reports</a>
      <a href="messages.php" class="<?= $current === 'messages.php' ? 'active' : '' ?>">Messages</a>
    </nav>
    <div class="admin-sidebar-footer">
      <a href="../index.php">&larr; Back to store</a>
      <a href="logout.php">Logout</a>
    </div>
  </aside>
  <main class="admin-main">
    <button type="button" class="admin-menu-toggle" id="adminMenuToggle" aria-label="Toggle admin navigation" aria-expanded="false">☰ Menu</button>
    <?php if ($flash): ?>
      <div class="flash flash-<?= sanitize($flash['type']) ?>"><?= sanitize($flash['message']) ?></div>
    <?php endif; ?>
