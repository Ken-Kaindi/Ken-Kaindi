<?php
require_once 'includes/config.php';
$pageTitle = 'Register';

if (isLoggedIn()) {
    redirect('account.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request. Please try again.');
        redirect('register.php');
    }

    $fullName = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$fullName || !$email || !$password) {
        setFlash('error', 'Please fill in all required fields.');
        redirect('register.php');
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        setFlash('error', 'Enter a valid email address.');
        redirect('register.php');
    }
    if ($password !== $confirm) {
        setFlash('error', 'Passwords do not match.');
        redirect('register.php');
    }
    if (strlen($password) < 8) {
        setFlash('error', 'Password must be at least 8 characters.');
        redirect('register.php');
    }

    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        setFlash('error', 'An account with that email already exists.');
        redirect('register.php');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $insertStmt = $pdo->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
    $insertStmt->execute([$fullName, $email, $phone, $hash]);

    $_SESSION['user_id'] = $pdo->lastInsertId();
    $_SESSION['user_name'] = $fullName;
    $_SESSION['role'] = 'customer';
    setFlash('success', 'Welcome to PhiloBeauty, ' . $fullName . '!');
    redirect('account.php');
}

require_once 'includes/header.php';
?>

<section class="auth-page">
  <div class="container auth-box">
    <h1>Create your account</h1>
    <form method="post" class="auth-form">
      <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
      <label>Full name<input type="text" name="full_name" required autofocus></label>
      <label>Email<input type="email" name="email" required></label>
      <label>Phone<input type="tel" name="phone" placeholder="07XX XXX XXX"></label>
      <label>Password<input type="password" name="password" required minlength="8"></label>
      <label>Confirm password<input type="password" name="confirm_password" required minlength="8"></label>
      <button type="submit" class="btn btn-primary btn-block">Create account</button>
    </form>
    <p class="auth-switch">Already have an account? <a href="login.php">Log in</a></p>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
