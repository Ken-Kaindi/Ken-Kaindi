<?php
require_once '../includes/config.php';

if (isAdmin()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
        redirect('login.php');
    }
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $admin['id'];
        $_SESSION['user_name'] = $admin['full_name'];
        $_SESSION['role'] = 'admin';
        redirect('index.php');
    }

    setFlash('error', 'Incorrect admin credentials.');
    redirect('login.php');
}

$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login &mdash; PhiloBeauty</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../<?= sanitize(assetUrl('assets/css/style.css')) ?>">
</head>
<body class="admin-login-body">
  <div class="admin-login-box">
    <a href="../index.php" class="login-logo"><img src="../<?= sanitize(assetUrl('assets/images/philobeauty-logo.webp')) ?>" alt="PhiloBeauty" width="250" height="167"></a>
    <p class="admin-tag">Admin console</p>
    <?php if ($flash): ?>
      <div class="flash flash-<?= sanitize($flash['type']) ?>" style="position:static; margin-bottom:16px;"><?= sanitize($flash['message']) ?></div>
    <?php endif; ?>
    <form method="post">
      <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
      <label>Email<input type="email" name="email" required autofocus></label>
      <label>Password<input type="password" name="password" required></label>
      <button type="submit" class="btn btn-primary btn-block">Log in</button>
    </form>
  </div>
</body>
</html>
