<?php
require_once '../includes/config.php';
requireAdmin();
$pageTitle = 'Categories';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrfToken($_POST['csrf_token'] ?? '')) {
    $name = sanitize($_POST['name'] ?? '');
    $accent = preg_replace('/[^A-Fa-f0-9]/', '', $_POST['accent_color'] ?? '');
    $accent = $accent !== '' ? substr($accent, 0, 6) : '8A2846';
    $description = trim($_POST['description'] ?? '');
    $slug = slugify($name);
    if ($name) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, accent_color) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $description, $accent]);
        setFlash('success', 'Category added.');
    }
    redirect('categories.php');
}

$categories = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count FROM categories c ORDER BY c.name ASC")->fetchAll();

require_once '../includes/admin-header.php';
?>

<div class="admin-header-row"><h1>Categories</h1></div>

<div class="admin-panel admin-form-panel">
  <form method="post" class="admin-form admin-form-inline">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    <label>Name<input type="text" name="name" required></label>
    <label>Accent color (hex)<input type="text" name="accent_color" placeholder="8A2846" maxlength="6"></label>
    <label>Description<input type="text" name="description"></label>
    <button type="submit" class="btn btn-primary">Add category</button>
  </form>
</div>

<div class="admin-panel">
  <table class="admin-table">
    <thead><tr><th>Name</th><th>Products</th><th>Accent</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($categories as $cat): ?>
        <tr>
          <td><?= sanitize($cat['name']) ?></td>
          <td><?= (int) $cat['product_count'] ?></td>
          <td><span class="color-dot" style="background:#<?= sanitize($cat['accent_color']) ?>"></span></td>
          <td class="admin-actions">
            <form action="category-delete.php" method="post" onsubmit="return confirm('Delete this category? Products inside it will also be deleted.');">
              <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
              <input type="hidden" name="id" value="<?= (int) $cat['id'] ?>">
              <button type="submit" class="link-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
