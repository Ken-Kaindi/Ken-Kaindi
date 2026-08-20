<?php
$categories = getCategories($pdo);
$flash = getFlash();
$cartCount = getCartCount();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($pageTitle) ? sanitize($pageTitle) . ' — PhiloBeauty' : 'PhiloBeauty — Beauty, Considered' ?></title>
<meta name="description" content="PhiloBeauty Machakos — genuine skincare, makeup, haircare, fragrance and beauty essentials delivered across Kenya.">
<meta name="theme-color" content="#7B0F34">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,600;0,9..144,700;1,9..144,500&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= sanitize(assetUrl('assets/css/style.css')) ?>">
</head>
<body>
<div class="announcement-bar">
  <div class="container announcement-inner">
    <span>Authentic beauty essentials in Machakos</span>
    <span class="announcement-contact"><a href="tel:+254743432746"><?= sanitize(BUSINESS_PHONE) ?></a> · Delivery across Kenya</span>
  </div>
</div>
<header class="site-header">
  <div class="container header-inner">
    <a href="index.php" class="brand-logo" aria-label="PhiloBeauty home">
      <img src="<?= sanitize(assetUrl('assets/images/philobeauty-logo.webp')) ?>" alt="PhiloBeauty cosmetics logo" width="162" height="108">
      <span class="brand-wordmark">Philo<span>Beauty</span></span>
    </a>

    <nav class="main-nav" id="mainNav">
      <a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>">Home</a>
      <a href="shop.php" class="<?= in_array($currentPage, ['shop.php', 'product.php'], true) ? 'active' : '' ?>">Shop</a>
      <div class="nav-dropdown">
        <span class="nav-dropdown-trigger">Categories</span>
        <div class="dropdown-menu">
          <?php foreach ($categories as $cat): ?>
            <a href="shop.php?category=<?= urlencode($cat['slug']) ?>"><?= sanitize($cat['name']) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <a href="track-order.php" class="<?= $currentPage === 'track-order.php' ? 'active' : '' ?>">Track order</a>
      <a href="about.php" class="<?= $currentPage === 'about.php' ? 'active' : '' ?>">About</a>
      <a href="contact.php" class="<?= $currentPage === 'contact.php' ? 'active' : '' ?>">Contact</a>

      <div class="mobile-account-links">
        <?php if (isLoggedIn()): ?>
          <a href="account.php">My account</a>
          <a href="logout.php">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
          <a href="register.php">Register</a>
        <?php endif; ?>
      </div>
    </nav>

    <div class="header-actions">
      <form class="search-form" action="shop.php" method="get">
        <input type="text" name="search" placeholder="Search products&hellip;" value="<?= sanitize($_GET['search'] ?? '') ?>" aria-label="Search products">
        <button type="submit" aria-label="Search">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
        </button>
      </form>
      <a href="cart.php" class="cart-link <?= $currentPage === 'cart.php' ? 'active' : '' ?>" aria-label="View bag">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4"/><path d="M3 6h18"/><path d="M16 10a4 4 0 01-8 0"/></svg>
        <span class="cart-count"><?= (int) $cartCount ?></span>
      </a>
      <div class="account-links">
        <?php if (isLoggedIn()): ?>
          <a href="account.php">My account</a>
          <a href="logout.php">Logout</a>
        <?php else: ?>
          <a href="login.php">Login</a>
        <?php endif; ?>
      </div>
    </div>

    <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>

<?php if ($flash): ?>
  <div class="flash flash-<?= sanitize($flash['type']) ?>">
    <div class="container"><?= sanitize($flash['message']) ?></div>
  </div>
<?php endif; ?>

<main>
