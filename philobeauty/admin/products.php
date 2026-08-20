<?php
require_once '../includes/config.php';
requireAdmin();
$pageTitle = 'Products';

$products = $pdo->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.created_at DESC")->fetchAll();

require_once '../includes/admin-header.php';
?>

<div class="admin-header-row">
  <h1>Products</h1>
  <a href="product-form.php" class="btn btn-primary">+ Add product</a>
</div>

<div class="admin-panel">
  <?php if (empty($products)): ?>
    <p class="empty-state">No products yet. Add your first one above.</p>
  <?php else: ?>
    <table class="admin-table">
      <thead><tr><th>Product</th><th>Category</th><th>Price</th><th>Stock</th><th>Featured</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($products as $p): ?>
          <tr>
            <td class="admin-product-cell">
              <img src="<?= sanitize(productImage($p, true)) ?>" alt="<?= sanitize($p['name']) ?>">
              <?= sanitize($p['name']) ?>
            </td>
            <td><?= sanitize($p['category_name']) ?></td>
            <td><?= formatPrice($p['price']) ?></td>
            <td><?= (int) $p['stock'] ?></td>
            <td><?= $p['is_featured'] ? 'Yes' : '&mdash;' ?></td>
            <td class="admin-actions">
              <a href="product-form.php?id=<?= (int) $p['id'] ?>">Edit</a>
              <form action="product-delete.php" method="post" onsubmit="return confirm('Delete this product?');">
                <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
                <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
                <button type="submit" class="link-danger">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
