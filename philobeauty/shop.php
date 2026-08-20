<?php
require_once 'includes/config.php';
$pageTitle = 'Shop';

$where = [];
$params = [];

if (!empty($_GET['category'])) {
    $where[] = 'c.slug = :slug';
    $params[':slug'] = $_GET['category'];
}
if (!empty($_GET['search'])) {
    $where[] = 'p.name LIKE :search';
    $params[':search'] = '%' . $_GET['search'] . '%';
}
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$sort = $_GET['sort'] ?? 'newest';
switch ($sort) {
    case 'price_asc':  $orderSql = 'p.price ASC';   break;
    case 'price_desc': $orderSql = 'p.price DESC';  break;
    case 'name':       $orderSql = 'p.name ASC';    break;
    default:           $orderSql = 'p.created_at DESC';
}

$perPage = 12;
$page = max(1, (int) ($_GET['page'] ?? 1));
$offset = ($page - 1) * $perPage;

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM products p JOIN categories c ON p.category_id = c.id $whereSql");
$countStmt->execute($params);
$totalProducts = (int) $countStmt->fetchColumn();
$totalPages = max(1, (int) ceil($totalProducts / $perPage));

$sql = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id $whereSql ORDER BY $orderSql LIMIT :limit OFFSET :offset";
$stmt = $pdo->prepare($sql);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$products = $stmt->fetchAll();

$categories = getCategories($pdo);
$activeCategory = $_GET['category'] ?? '';

require_once 'includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <h1><?= !empty($_GET['search']) ? 'Results for &ldquo;' . sanitize($_GET['search']) . '&rdquo;' : 'Shop all products' ?></h1>
    <p><?= $totalProducts ?> product<?= $totalProducts === 1 ? '' : 's' ?></p>
  </div>
</section>

<section class="shop-layout">
  <div class="container shop-grid">
    <aside class="shop-filters">
      <h4>Categories</h4>
      <ul class="filter-list">
        <li><a href="shop.php" class="<?= $activeCategory === '' ? 'active' : '' ?>">All</a></li>
        <?php foreach ($categories as $cat): ?>
          <li><a href="shop.php?category=<?= urlencode($cat['slug']) ?>" class="<?= $activeCategory === $cat['slug'] ? 'active' : '' ?>"><?= sanitize($cat['name']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </aside>
    <div class="shop-main">
      <div class="shop-toolbar">
        <form method="get" class="sort-form">
          <?php if ($activeCategory): ?><input type="hidden" name="category" value="<?= sanitize($activeCategory) ?>"><?php endif; ?>
          <?php if (!empty($_GET['search'])): ?><input type="hidden" name="search" value="<?= sanitize($_GET['search']) ?>"><?php endif; ?>
          <label for="sort">Sort by</label>
          <select name="sort" id="sort" onchange="this.form.submit()">
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
            <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
            <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
            <option value="name" <?= $sort === 'name' ? 'selected' : '' ?>>Name A&ndash;Z</option>
          </select>
        </form>
      </div>

      <?php if (empty($products)): ?>
        <p class="empty-state">No products match your search. Try a different category or keyword.</p>
      <?php else: ?>
        <div class="product-grid">
          <?php foreach ($products as $p): ?>
            <?php include 'includes/product-card.php'; ?>
          <?php endforeach; ?>
        </div>
        <?php if ($totalPages > 1): ?>
          <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <a href="?<?= sanitize(http_build_query(array_merge($_GET, ['page' => $i]))) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>
          </div>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
