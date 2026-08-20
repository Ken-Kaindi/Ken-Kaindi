<?php
require_once '../includes/config.php';
requireAdmin();

$id = (int) ($_GET['id'] ?? 0);
$product = null;
if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();
    if (!$product) {
        setFlash('error', 'Product not found.');
        redirect('products.php');
    }
}
$pageTitle = $product ? 'Edit Product' : 'Add Product';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrfToken($_POST['csrf_token'] ?? '')) {
        setFlash('error', 'Invalid request.');
        redirect('product-form.php' . ($id ? "?id=$id" : ''));
    }

    $name = sanitize($_POST['name'] ?? '');
    $categoryId = (int) ($_POST['category_id'] ?? 0);
    $price = (float) ($_POST['price'] ?? 0);
    $stock = (int) ($_POST['stock'] ?? 0);
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $slug = slugify($name);

    if (!$name || !$categoryId || $price <= 0) {
        setFlash('error', 'Please fill in name, category and a valid price.');
        redirect('product-form.php' . ($id ? "?id=$id" : ''));
    }

    if ($product) {
        $upd = $pdo->prepare("UPDATE products SET name=?, slug=?, category_id=?, price=?, stock=?, description=?, image=?, is_featured=? WHERE id=?");
        $upd->execute([$name, $slug, $categoryId, $price, $stock, $description, $image, $isFeatured, $id]);
        setFlash('success', 'Product updated.');
    } else {
        $ins = $pdo->prepare("INSERT INTO products (name, slug, category_id, price, stock, description, image, is_featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $ins->execute([$name, $slug, $categoryId, $price, $stock, $description, $image, $isFeatured]);
        setFlash('success', 'Product added.');
    }
    redirect('products.php');
}

$categories = getCategories($pdo);
require_once '../includes/admin-header.php';
?>

<div class="admin-header-row"><h1><?= $product ? 'Edit product' : 'Add product' ?></h1></div>

<div class="admin-panel admin-form-panel">
  <form method="post" class="admin-form">
    <input type="hidden" name="csrf_token" value="<?= generateCsrfToken() ?>">
    <label>Product name<input type="text" name="name" required value="<?= sanitize($product['name'] ?? '') ?>"></label>
    <label>Category
      <select name="category_id" required>
        <option value="">Select category</option>
        <?php foreach ($categories as $cat): ?>
          <option value="<?= (int) $cat['id'] ?>" <?= (int) ($product['category_id'] ?? 0) === (int) $cat['id'] ? 'selected' : '' ?>><?= sanitize($cat['name']) ?></option>
        <?php endforeach; ?>
      </select>
    </label>
    <div class="form-row">
      <label>Price (KES)<input type="number" step="0.01" min="0" name="price" required value="<?= sanitize($product['price'] ?? '') ?>"></label>
      <label>Stock<input type="number" min="0" name="stock" required value="<?= sanitize((string) ($product['stock'] ?? 0)) ?>"></label>
    </div>
    <label>Image path or URL<input type="text" name="image" placeholder="assets/images/product.svg or https://&hellip;" value="<?= sanitize($product['image'] ?? '') ?>"><small>Use a local project image for reliable offline loading, or a complete HTTPS URL.</small></label>
    <label>Description<textarea name="description" rows="5"><?= sanitize($product['description'] ?? '') ?></textarea></label>
    <label class="checkbox-option"><input type="checkbox" name="is_featured" <?= !empty($product['is_featured']) ? 'checked' : '' ?>> Feature on homepage</label>
    <div class="form-actions">
      <button type="submit" class="btn btn-primary"><?= $product ? 'Save changes' : 'Add product' ?></button>
      <a href="products.php" class="btn btn-ghost">Cancel</a>
    </div>
  </form>
</div>

<?php require_once '../includes/admin-footer.php'; ?>
