</main>

<footer class="site-footer">
  <div class="container footer-inner">
    <div class="footer-brand">
      <a href="index.php" class="footer-logo-link"><img src="<?= sanitize(assetUrl('assets/images/philobeauty-logo.webp')) ?>" alt="PhiloBeauty cosmetics" width="230" height="153"></a>
      <p>Authentic, considered beauty from Machakos, delivered across Kenya.</p>
      <div class="swatch-row" aria-hidden="true">
        <span class="swatch" style="background:#8A2846"></span>
        <span class="swatch" style="background:#C9A227"></span>
        <span class="swatch" style="background:#D8A8A0"></span>
        <span class="swatch" style="background:#7C8B6F"></span>
        <span class="swatch" style="background:#A8785E"></span>
      </div>
    </div>
    <div class="footer-col">
      <h4>Shop</h4>
      <a href="shop.php">All products</a>
      <a href="track-order.php">Track an order</a>
      <a href="about.php">About us</a>
      <a href="contact.php">Contact</a>
    </div>
    <div class="footer-col">
      <h4>Account</h4>
      <?php if (isLoggedIn()): ?>
        <a href="account.php">My orders</a>
        <a href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
        <a href="register.php">Register</a>
      <?php endif; ?>
    </div>
    <div class="footer-col">
      <h4>Get in touch</h4>
      <p><?= sanitize(BUSINESS_LOCATION) ?></p>
      <p><a href="mailto:<?= sanitize(BUSINESS_EMAIL) ?>"><?= sanitize(BUSINESS_EMAIL) ?></a></p>
      <p><a href="tel:+254743432746"><?= sanitize(BUSINESS_PHONE) ?></a></p>
    </div>
  </div>
  <div class="footer-bottom">
    <div class="container">
      <p>&copy; <?= date('Y') ?> PhiloBeauty. All rights reserved.</p>
    </div>
  </div>
</footer>

<nav class="mobile-bottom-nav" aria-label="Mobile navigation">
  <a href="index.php" class="<?= $currentPage === 'index.php' ? 'active' : '' ?>"><span>⌂</span>Home</a>
  <a href="shop.php" class="<?= in_array($currentPage, ['shop.php','product.php'], true) ? 'active' : '' ?>"><span>◇</span>Shop</a>
  <a href="track-order.php" class="<?= $currentPage === 'track-order.php' ? 'active' : '' ?>"><span>◎</span>Track</a>
  <a href="cart.php" class="<?= in_array($currentPage, ['cart.php','checkout.php'], true) ? 'active' : '' ?>"><span>▢</span>Bag<small><?= (int) getCartCount() ?></small></a>
  <a href="<?= isLoggedIn() ? 'account.php' : 'login.php' ?>" class="<?= in_array($currentPage, ['account.php','login.php','register.php'], true) ? 'active' : '' ?>"><span>○</span>Account</a>
</nav>

<script src="<?= sanitize(assetUrl('assets/js/script.js')) ?>" defer></script>
</body>
</html>
