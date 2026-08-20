<?php
require_once 'includes/config.php';
$pageTitle = 'Contact';
require_once 'includes/header.php';
?>

<section class="page-header"><div class="container"><h1>Get in touch</h1></div></section>

<section class="contact-page">
  <div class="container contact-grid">
    <div class="contact-info">
      <h3>We'd love to hear from you</h3>
      <p>Questions about an order, a shade, or a product recommendation &mdash; send us a note and we'll reply within a day.</p>
      <p><strong>Email</strong><br><a href="mailto:<?= sanitize(BUSINESS_EMAIL) ?>"><?= sanitize(BUSINESS_EMAIL) ?></a></p>
      <p><strong>Phone</strong><br><a href="tel:+254743432746"><?= sanitize(BUSINESS_PHONE) ?></a></p>
      <p><strong>Based in</strong><br><?= sanitize(BUSINESS_LOCATION) ?></p>
    </div>
    <form action="contact-submit.php" method="post" class="contact-form">
      <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
      <label>Name<input type="text" name="name" required></label>
      <label>Email<input type="email" name="email" required></label>
      <label>Message<textarea name="message" rows="5" required></textarea></label>
      <button type="submit" class="btn btn-primary">Send message</button>
    </form>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
