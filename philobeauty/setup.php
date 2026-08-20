<?php
/**
 * One-time setup script — creates the administrator if it does not exist.
 * Visit this file once in your browser, then DELETE it.
 */
require_once 'includes/config.php';

$defaultPassword = 'Admin@123';
$existing = $pdo->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$existing->execute(['admin@philobeauty.com']);
$created = !$existing->fetch();
if ($created) {
    $hash = password_hash($defaultPassword, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, 'admin')");
    $stmt->execute(['PhiloBeauty Administrator', 'admin@philobeauty.com', BUSINESS_PHONE, $hash]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Setup &mdash; PhiloBeauty</title>
<link rel="stylesheet" href="<?= sanitize(assetUrl('assets/css/style.css')) ?>">
</head>
<body class="admin-login-body">
  <div class="admin-login-box">
    <a href="index.php" class="login-logo"><img src="<?= sanitize(assetUrl('assets/images/philobeauty-logo.webp')) ?>" alt="PhiloBeauty" width="250" height="167"></a>
    <h2 style="margin-top:16px;"><?= $created ? 'Setup complete' : 'Admin already configured' ?></h2>
    <p><?= $created ? 'Your admin account is ready:' : 'The existing administrator password was not changed.' ?></p>
    <?php if ($created): ?><p><strong>Email:</strong> admin@philobeauty.com<br><strong>Password:</strong> Admin@123</p><?php else: ?><p><strong>Email:</strong> admin@philobeauty.com</p><?php endif; ?>
    <div class="flash flash-error" style="position:static; margin:16px 0;">
      <strong>Important:</strong> delete this file (setup.php) from your server after setup.
    </div>
    <a href="admin/login.php" class="btn btn-primary btn-block">Go to admin login &rarr;</a>
  </div>
</body>
</html>
