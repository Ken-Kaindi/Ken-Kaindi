<?php
require_once 'includes/config.php';
requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Invalid request.');
    redirect('checkout.php');
}

if (empty($_SESSION['cart'])) {
    redirect('shop.php');
}

$fullName = sanitize($_POST['full_name'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$county = sanitize($_POST['county'] ?? '');
$address = sanitize($_POST['address'] ?? '');
$notes = trim($_POST['notes'] ?? '');
$paymentMethod = in_array($_POST['payment_method'] ?? '', ['Cash on Delivery', 'M-Pesa'], true) ? $_POST['payment_method'] : 'Cash on Delivery';

if (!$fullName || !$phone || !$email || !$county || !$address) {
    setFlash('error', 'Please fill in all delivery details.');
    redirect('checkout.php');
}
if (!filter_var(html_entity_decode($email, ENT_QUOTES, 'UTF-8'), FILTER_VALIDATE_EMAIL)) {
    setFlash('error', 'Enter a valid email address.');
    redirect('checkout.php');
}

$ids = array_keys($_SESSION['cart']);
$placeholders = implode(',', array_fill(0, count($ids), '?'));
$stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($placeholders)");
$stmt->execute($ids);
$rows = $stmt->fetchAll();

$subtotal = 0;
$items = [];
foreach ($rows as $row) {
    $qty = min($_SESSION['cart'][$row['id']], (int) $row['stock']);
    if ($qty <= 0) {
        continue;
    }
    $lineTotal = $row['price'] * $qty;
    $subtotal += $lineTotal;
    $items[] = ['id' => $row['id'], 'name' => $row['name'], 'price' => $row['price'], 'qty' => $qty, 'lineTotal' => $lineTotal];
}

if (empty($items)) {
    setFlash('error', 'The items in your bag are no longer available.');
    redirect('cart.php');
}

$deliveryFee = DELIVERY_FEE;
$total = $subtotal + $deliveryFee;
$orderNumber = generateOrderNumber();
$trackingCode = generateTrackingCode();

try {
    $pdo->beginTransaction();

    $orderStmt = $pdo->prepare("INSERT INTO orders (order_number, tracking_code, user_id, full_name, phone, email, county, address, payment_method, payment_status, delivery_fee, notes, status, total_amount) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, 'pending', ?)");
    $orderStmt->execute([$orderNumber, $trackingCode, $_SESSION['user_id'], $fullName, $phone, $email, $county, $address, $paymentMethod, $deliveryFee, $notes, $total]);
    $orderId = $pdo->lastInsertId();

    $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?)");
    $stockStmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?");

    foreach ($items as $item) {
        $itemStmt->execute([$orderId, $item['id'], $item['name'], $item['price'], $item['qty'], $item['lineTotal']]);
        $stockStmt->execute([$item['qty'], $item['id'], $item['qty']]);
        if ($stockStmt->rowCount() !== 1) {
            throw new RuntimeException('Stock changed while the order was being placed.');
        }
    }

    $paymentStmt = $pdo->prepare("INSERT INTO payments (order_id, method, amount, status) VALUES (?, ?, ?, 'pending')");
    $paymentStmt->execute([$orderId, $paymentMethod, $total]);

    $deliveryStmt = $pdo->prepare("INSERT INTO deliveries (order_id, status, estimated_minutes) VALUES (?, 'waiting', 60)");
    $deliveryStmt->execute([$orderId]);

    $pdo->commit();
} catch (Exception $e) {
    $pdo->rollBack();
    setFlash('error', 'Something went wrong placing your order. Please try again.');
    redirect('checkout.php');
}

unset($_SESSION['cart']);
setFlash('success', 'Order placed successfully!');
redirect('order-confirmation.php?order_id=' . $orderId);
