<?php
require_once 'includes/config.php';
$pageTitle = 'Track Order';

$order = null;
$delivery = null;
$lookupError = '';
$code = trim($_POST['code'] ?? $_GET['code'] ?? '');
$phone = trim($_POST['phone'] ?? '');

if ($code !== '') {
    if (isLoggedIn() && $_SERVER['REQUEST_METHOD'] !== 'POST') {
        if (isAdmin()) {
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE tracking_code = ? OR order_number = ? LIMIT 1");
            $stmt->execute([$code, $code]);
        } else {
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE (tracking_code = ? OR order_number = ?) AND user_id = ? LIMIT 1");
            $stmt->execute([$code, $code, $_SESSION['user_id']]);
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            $lookupError = 'Your session expired. Please try again.';
        } elseif ($phone === '') {
            $lookupError = 'Enter the phone number used for the order.';
        } else {
            $digits = preg_replace('/\D+/', '', $phone);
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE (tracking_code = ? OR order_number = ?) AND REPLACE(REPLACE(REPLACE(phone, ' ', ''), '+', ''), '-', '') LIKE ? LIMIT 1");
            $stmt->execute([$code, $code, '%' . substr($digits, -9)]);
        }
    }

    if (isset($stmt)) {
        $order = $stmt->fetch();
        if (!$order && $lookupError === '') {
            $lookupError = 'No matching order was found. Check the code and phone number.';
        }
    }
}

if ($order) {
    $deliveryStmt = $pdo->prepare('SELECT * FROM deliveries WHERE order_id = ?');
    $deliveryStmt->execute([$order['id']]);
    $delivery = $deliveryStmt->fetch();
}

require_once 'includes/header.php';
$steps = ['pending' => 'Confirmed', 'processing' => 'Processing', 'shipped' => 'On the way', 'delivered' => 'Delivered'];
$progress = $order ? orderProgress($order['status']) : 0;
?>

<section class="page-header track-page-header"><div class="container"><p class="eyebrow">Order visibility</p><h1>Track your PhiloBeauty order</h1><p>Use your tracking code or order number and the phone number used at checkout.</p></div></section>

<section class="track-page">
  <div class="container track-layout">
    <form method="post" class="track-form">
      <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
      <label>Tracking code or order number<input type="text" name="code" required value="<?= sanitize($code) ?>" placeholder="PHILO-XXXXXX or PB-XXXXXX"></label>
      <label>Order phone number<input type="tel" name="phone" required value="<?= sanitize($phone) ?>" placeholder="07XX XXX XXX"></label>
      <button class="btn btn-primary btn-block" type="submit">Find my order</button>
      <?php if ($lookupError): ?><p class="inline-error"><?= sanitize($lookupError) ?></p><?php endif; ?>
    </form>

    <div class="track-result <?= $order ? 'has-order' : '' ?>">
      <?php if ($order): ?>
        <div class="track-result-head"><div><p class="eyebrow">Order found</p><h2><?= sanitize(displayOrderNumber($order)) ?></h2></div><span class="status-badge status-<?= sanitize($order['status']) ?>"><?= ucfirst($order['status']) ?></span></div>
        <?php if ($order['status'] === 'cancelled'): ?>
          <div class="cancelled-note">This order was cancelled. Contact <?= sanitize(BUSINESS_PHONE) ?> if you need assistance.</div>
        <?php else: ?>
          <div class="tracking-progress">
            <?php foreach (array_values($steps) as $index => $label): ?>
              <div class="progress-step <?= $index <= $progress ? 'complete' : '' ?>"><span><?= $index < $progress ? '✓' : $index + 1 ?></span><strong><?= $label ?></strong></div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
        <div class="track-meta">
          <div><span>Placed</span><strong><?= date('d M Y, H:i', strtotime($order['created_at'])) ?></strong></div>
          <div><span>Destination</span><strong><?= sanitize($order['county']) ?></strong></div>
          <div><span>Payment</span><strong><?= sanitize($order['payment_method']) ?> · <?= ucfirst($order['payment_status']) ?></strong></div>
          <div><span>Delivery ETA</span><strong><?= $delivery ? (int) $delivery['estimated_minutes'] . ' minutes after dispatch' : 'Being confirmed' ?></strong></div>
        </div>
      <?php else: ?>
        <div class="track-placeholder"><span>◎</span><h2>Your delivery journey appears here</h2><p>We will show confirmation, processing, dispatch and delivery progress in one clear view.</p></div>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
