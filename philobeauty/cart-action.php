<?php
require_once 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    setFlash('error', 'Invalid request.');
    redirect('cart.php');
}

$action = $_POST['action'] ?? '';
$productId = (int) ($_POST['product_id'] ?? 0);

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

switch ($action) {
    case 'add':
        $qty = max(1, (int) ($_POST['quantity'] ?? 1));
        $_SESSION['cart'][$productId] = ($_SESSION['cart'][$productId] ?? 0) + $qty;
        setFlash('success', 'Added to your bag.');
        break;

    case 'update':
        $qty = (int) ($_POST['quantity'] ?? 1);
        if ($qty <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId] = $qty;
        }
        setFlash('success', 'Bag updated.');
        break;

    case 'remove':
        unset($_SESSION['cart'][$productId]);
        setFlash('success', 'Item removed.');
        break;
}

$redirectTo = safeReturnUrl($_POST['redirect'] ?? '', 'cart.php');
redirect($redirectTo);
