<?php
require_once 'includes/config.php';
$pageTitle = 'Home';

$featured = $pdo->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_featured = 1 ORDER BY p.created_at DESC LIMIT 8")->fetchAll();
$categories = getCategories($pdo);
$categoryVisuals = [
    'skincare' => 'kenyan-skincare.webp',
    'makeup' => 'kenyan-makeup.webp',
    'haircare' => 'kenyan-hair-body.webp',
    'fragrance' => 'kenyan-beauty-hero.webp',
    'bath-body' => 'kenyan-hair-body.webp',
    'tools-brushes' => 'kenyan-makeup.webp',
];

require_once 'includes/header.php';
?>

<section class="hero">
  <div class="container hero-inner">
    <div class="hero-text">
      <p class="eyebrow">Machakos beauty, thoughtfully chosen</p>
      <h1>Your everyday beauty, <em>beautifully simplified.</em></h1>
      <p class="hero-lede">Discover authentic skincare, makeup, haircare and fragrance selected for Kenyan routines, budgets and skin tones—backed by personal support from PhiloBeauty.</p>
      <div class="hero-actions">
        <a href="shop.php" class="btn btn-primary">Shop beauty essentials</a>
        <a href="track-order.php" class="btn btn-ghost">Track your order</a>
      </div>
      <div class="hero-trust" aria-label="PhiloBeauty service promises">
        <span><strong>100%</strong> authentic</span>
        <span><strong>24–72h</strong> delivery</span>
        <span><strong>M-Pesa</strong> supported</span>
      </div>
    </div>
    <div class="hero-visual">
      <div class="hero-logo-card">
        <img src="<?= sanitize(assetUrl('assets/images/kenyan-beauty-hero.webp')) ?>" alt="Kenyan woman applying burgundy lipstick at a modern beauty table" width="1600" height="800" fetchpriority="high">
      </div>
      <div class="hero-note"><span>✦</span><strong>Curated in Machakos</strong><small>For beauty that feels like you</small></div>
    </div>
  </div>
</section>

<section class="categories-strip">
  <div class="container">
    <div class="section-head">
      <div><p class="eyebrow">Find your routine</p><h2 class="section-title">Shop by category</h2></div>
      <a href="shop.php" class="section-link">Explore all products &rarr;</a>
    </div>
    <div class="category-grid">
      <?php foreach ($categories as $cat): ?>
        <a href="shop.php?category=<?= urlencode($cat['slug']) ?>" class="category-card" style="--accent:#<?= sanitize($cat['accent_color']) ?>">
          <img src="assets/images/<?= sanitize($categoryVisuals[$cat['slug']] ?? 'kenyan-skincare.webp') ?>" alt="<?= sanitize($cat['name']) ?> collection" loading="lazy">
          <span class="category-copy"><span class="category-name"><?= sanitize($cat['name']) ?></span><small><?= sanitize($cat['description']) ?></small></span>
          <span class="category-arrow">&rarr;</span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="featured">
  <div class="container">
    <div class="section-head">
      <h2 class="section-title">Best sellers</h2>
      <a href="shop.php" class="section-link">View all &rarr;</a>
    </div>
    <?php if (empty($featured)): ?>
      <p class="empty-state">No featured products yet &mdash; add some from the admin panel.</p>
    <?php else: ?>
      <div class="product-grid">
        <?php foreach ($featured as $p): ?>
          <?php include 'includes/product-card.php'; ?>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<section class="promise">
  <div class="container experience-panel">
    <div class="experience-copy">
      <p class="eyebrow">Beauty without the guesswork</p>
      <h2>From browsing to delivery, every step feels effortless.</h2>
      <p>PhiloBeauty combines a carefully organised catalogue with clear product details, secure account checkout, live order tracking and responsive support from Machakos.</p>
      <a href="about.php" class="section-link">Meet PhiloBeauty &rarr;</a>
    </div>
    <div class="promise-grid">
      <div class="promise-item"><span class="promise-icon">01</span><p class="eyebrow">Authentic sourcing</p><p>Genuine products selected from trusted brands and authorised distributors.</p></div>
      <div class="promise-item"><span class="promise-icon">02</span><p class="eyebrow">Flexible payment</p><p>Choose M-Pesa or cash on delivery through a clear, secure checkout.</p></div>
      <div class="promise-item"><span class="promise-icon">03</span><p class="eyebrow">Order visibility</p><p>Use your tracking code and phone number to follow every delivery stage.</p></div>
      <div class="promise-item"><span class="promise-icon">04</span><p class="eyebrow">Human support</p><p>Get product and order assistance directly from our Machakos team.</p></div>
    </div>
  </div>
</section>

<section class="testimonials-section">
  <div class="container">
    <div class="section-head"><div><p class="eyebrow">Community favourites</p><h2>Beauty customers come back for</h2></div></div>
    <div class="testimonial-grid">
      <article class="testimonial-card"><div class="stars">★★★★★</div><p>“My order arrived neatly packed and the shade guidance was exactly right.”</p><strong>— Mercy, Machakos</strong></article>
      <article class="testimonial-card"><div class="stars">★★★★★</div><p>“The checkout was simple, and I could track the order without calling.”</p><strong>— Amina, Nairobi</strong></article>
      <article class="testimonial-card"><div class="stars">★★★★★</div><p>“I finally found a skincare routine that fits both my skin and my budget.”</p><strong>— Faith, Makueni</strong></article>
    </div>
  </div>
</section>

<?php require_once 'includes/footer.php'; ?>
