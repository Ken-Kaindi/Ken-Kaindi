<?php
require_once 'includes/config.php';
$pageTitle = 'Login';

if (isLoggedIn()) {
    redirect('account.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request. Please try again.');
        redirect('login.php');
    }

    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        setFlash('success', 'Welcome back, ' . $user['full_name'] . '.');
        redirect($user['role'] === 'admin' ? 'admin/index.php' : 'account.php');
    }

    setFlash('error', 'Incorrect email or password.');
    redirect('login.php');
}

require_once 'includes/header.php';
?>

<section class="auth-page">
  <div class="container auth-box">
    <h1>Welcome back</h1>
    <p>Log in to track orders and check out faster.</p>
    <form method="post" class="auth-form">
      <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
      <label>Email<input type="email" name="email" required autofocus></label>
      <label>Password<input type="password" name="password" required></label>
      <button type="submit" class="btn btn-primary btn-block">Log in</button>
    </form>
    <p class="auth-switch">New to PhiloBeauty? <a href="register.php">Create an account</a></p>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
