<?php
/**
 * Shared helper functions used across the whole site.
 */

function sanitize($data) {
    return htmlspecialchars(trim($data ?? ''), ENT_QUOTES, 'UTF-8');
}

function formatPrice($amount) {
    return 'KES ' . number_format((float) $amount, 2);
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isLoggedIn() && ($_SESSION['role'] ?? '') === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        setFlash('error', 'Please log in to continue.');
        redirect('login.php');
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        redirect('login.php');
    }
}

function redirect($url) {
    header('Location: ' . $url);
    exit;
}

function safeReturnUrl($candidate, $fallback = 'index.php') {
    $candidate = trim((string) $candidate);
    if ($candidate === '' || preg_match('/[\r\n]/', $candidate)) {
        return $fallback;
    }
    $parts = parse_url($candidate);
    if ($parts === false || isset($parts['scheme']) || isset($parts['host']) || str_starts_with($candidate, '//')) {
        return $fallback;
    }
    return $candidate;
}

function setFlash($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function getCartCount() {
    if (empty($_SESSION['cart'])) {
        return 0;
    }
    return array_sum($_SESSION['cart']);
}

function getCategories($pdo) {
    return $pdo->query('SELECT * FROM categories ORDER BY name ASC')->fetchAll();
}

function assetUrl($path) {
    $path = ltrim((string) $path, '/');
    $localPath = dirname(__DIR__) . '/' . $path;
    return $path . (is_file($localPath) ? '?v=' . filemtime($localPath) : '');
}

function productImage($product, $adminContext = false) {
    $image = trim((string) ($product['image'] ?? ''));
    if ($image === '') {
        $categoryMap = [
            1 => 'kenyan-skincare.webp',
            2 => 'kenyan-makeup.webp',
            3 => 'kenyan-hair-body.webp',
            4 => 'kenyan-beauty-hero.webp',
            5 => 'kenyan-hair-body.webp',
            6 => 'kenyan-makeup.webp',
        ];
        $image = 'assets/images/' . ($categoryMap[(int) ($product['category_id'] ?? 0)] ?? 'kenyan-skincare.webp');
    }
    if (preg_match('#^https?://#i', $image) || str_starts_with($image, 'data:')) {
        return $image;
    }
    $image = ltrim($image, '/');
    return ($adminContext ? '../' : '') . $image;
}

function displayOrderNumber($order) {
    if (!empty($order['order_number'])) {
        return $order['order_number'];
    }
    return 'PB-' . str_pad((string) ($order['id'] ?? 0), 6, '0', STR_PAD_LEFT);
}

function generateOrderNumber() {
    return 'PB-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));
}

function generateTrackingCode() {
    return 'PHILO-' . strtoupper(bin2hex(random_bytes(3)));
}

function orderProgress($status) {
    $steps = ['pending', 'processing', 'shipped', 'delivered'];
    $index = array_search($status, $steps, true);
    return $status === 'cancelled' ? -1 : ($index === false ? 0 : $index);
}

function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token ?? '');
}

function slugify($text) {
    $text = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $text), '-'));
    return $text !== '' ? $text : 'item-' . time();
}
