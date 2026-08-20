-- =========================================================
-- PhiloBeauty — database schema & sample data
-- Import this whole file in phpMyAdmin, or run:
--   mysql -u root -p < philobeauty.sql
-- =========================================================

CREATE DATABASE IF NOT EXISTS philobeauty CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE philobeauty;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS deliveries;
DROP TABLE IF EXISTS payments;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS order_items;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS categories;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------
-- Users (customers + admins, distinguished by `role`)
-- ---------------------------------------------------------
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(20) DEFAULT NULL,
  address VARCHAR(255) DEFAULT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('customer','admin') NOT NULL DEFAULT 'customer',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Categories
-- ---------------------------------------------------------
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(80) NOT NULL UNIQUE,
  description VARCHAR(255) DEFAULT NULL,
  accent_color VARCHAR(7) NOT NULL DEFAULT '8A2846'
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Products
-- ---------------------------------------------------------
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(150) NOT NULL UNIQUE,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  image VARCHAR(500) DEFAULT NULL,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Orders
-- ---------------------------------------------------------
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(30) NOT NULL UNIQUE,
  tracking_code VARCHAR(30) NOT NULL UNIQUE,
  user_id INT NOT NULL,
  full_name VARCHAR(100) NOT NULL,
  phone VARCHAR(20) NOT NULL,
  email VARCHAR(150) NOT NULL,
  county VARCHAR(80) NOT NULL,
  address VARCHAR(255) NOT NULL,
  payment_method ENUM('Cash on Delivery','M-Pesa') NOT NULL DEFAULT 'Cash on Delivery',
  payment_status ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  delivery_fee DECIMAL(10,2) NOT NULL DEFAULT 300.00,
  notes TEXT DEFAULT NULL,
  status ENUM('pending','processing','shipped','delivered','cancelled') NOT NULL DEFAULT 'pending',
  total_amount DECIMAL(10,2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Order items
-- ---------------------------------------------------------
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT DEFAULT NULL,
  product_name VARCHAR(150) NOT NULL,
  price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL,
  subtotal DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Payments and delivery tracking
-- ---------------------------------------------------------
CREATE TABLE payments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  method ENUM('Cash on Delivery','M-Pesa') NOT NULL,
  amount DECIMAL(10,2) NOT NULL,
  transaction_reference VARCHAR(80) DEFAULT NULL,
  status ENUM('pending','paid','failed','refunded') NOT NULL DEFAULT 'pending',
  paid_at TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  INDEX idx_payments_status (status)
) ENGINE=InnoDB;

CREATE TABLE deliveries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL UNIQUE,
  rider_name VARCHAR(100) DEFAULT 'Not assigned',
  rider_phone VARCHAR(20) DEFAULT NULL,
  status ENUM('waiting','assigned','out_for_delivery','delivered') NOT NULL DEFAULT 'waiting',
  estimated_minutes INT NOT NULL DEFAULT 60,
  dispatched_at TIMESTAMP NULL DEFAULT NULL,
  delivered_at TIMESTAMP NULL DEFAULT NULL,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  INDEX idx_deliveries_status (status)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Contact messages
-- ---------------------------------------------------------
CREATE TABLE contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL,
  message TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- Sample data
-- (No admin account here — visit setup.php once after import
--  to create admin@philobeauty.com / Admin@123 securely.)
-- =========================================================

INSERT INTO categories (name, slug, description, accent_color) VALUES
('Skincare', 'skincare', 'Serums, cleansers and daily essentials.', '7C8B6F'),
('Makeup', 'makeup', 'Complexion, lips and eyes.', '8A2846'),
('Haircare', 'haircare', 'Oils, creams and wash-day staples.', 'C9A227'),
('Fragrance', 'fragrance', 'Eau de parfum and body mists.', 'D8A8A0'),
('Bath & Body', 'bath-body', 'Butters, scrubs and body care.', 'A8785E'),
('Tools & Brushes', 'tools-brushes', 'Application tools and accessories.', '2B1620');

INSERT INTO products (category_id, name, slug, description, price, stock, image, is_featured) VALUES
(1, 'Hydra Glow Vitamin C Serum', 'hydra-glow-vitamin-c-serum', 'A lightweight, fast-absorbing serum that brightens dull skin and softens the look of dark spots over time. Apply a few drops each morning before sunscreen.', 2400.00, 40, 'assets/images/kenyan-skincare.webp', 1),
(1, 'Rose Clay Purifying Mask', 'rose-clay-purifying-mask', 'A gentle clay mask that draws out impurities without stripping the skin. Leaves a soft, matte finish after 10 minutes.', 1200.00, 25, 'assets/images/kenyan-skincare.webp', 0),
(1, 'Gentle Foaming Cleanser', 'gentle-foaming-cleanser', 'A pH-balanced daily cleanser that removes makeup and sunscreen without leaving skin feeling tight.', 950.00, 60, 'assets/images/kenyan-skincare.webp', 1),
(1, 'Niacinamide 10% Blemish Serum', 'niacinamide-10-blemish-serum', 'Helps calm visible redness and refine the look of pores. A cult favourite for combination and oily skin.', 1800.00, 35, 'assets/images/kenyan-skincare.webp', 0),
(1, 'SPF 50 Daily Sunscreen', 'spf-50-daily-sunscreen', 'A lightweight, no-white-cast sunscreen built for daily wear under makeup.', 1600.00, 50, 'assets/images/kenyan-skincare.webp', 1),

(2, 'Velvet Matte Lipstick — Berry Wine', 'velvet-matte-lipstick-berry-wine', 'A rich, comfortable matte finish in a deep berry-wine shade that suits most skin tones.', 1100.00, 45, 'assets/images/kenyan-makeup.webp', 1),
(2, 'Second Skin Foundation', 'second-skin-foundation', 'Buildable, breathable coverage that looks like skin, not makeup. Available in a wide shade range.', 2200.00, 30, 'assets/images/kenyan-makeup.webp', 1),
(2, 'Featherlight Setting Powder', 'featherlight-setting-powder', 'A finely milled translucent powder that sets makeup without adding weight or flashback.', 1400.00, 28, 'assets/images/kenyan-makeup.webp', 0),
(2, 'Precision Brow Pencil', 'precision-brow-pencil', 'A fine-tip pencil with a built-in spoolie for natural, hair-like strokes.', 750.00, 55, 'assets/images/kenyan-makeup.webp', 0),
(2, 'Editorial Eyeshadow Palette', 'editorial-eyeshadow-palette', 'Twelve richly pigmented mattes and shimmers, from everyday neutrals to a statement berry shade.', 2900.00, 20, 'assets/images/kenyan-makeup.webp', 1),

(3, 'Argan Repair Hair Oil', 'argan-repair-hair-oil', 'A weightless oil that tames frizz and adds shine without residue. Works on wet or dry hair.', 1350.00, 40, 'assets/images/kenyan-hair-body.webp', 0),
(3, 'Curl Defining Cream', 'curl-defining-cream', 'Defines curls and coils while fighting frizz, without the crunch.', 1050.00, 33, 'assets/images/kenyan-hair-body.webp', 0),
(3, 'Scalp Renewal Shampoo', 'scalp-renewal-shampoo', 'A clarifying shampoo that soothes an itchy scalp while gently cleansing strands.', 1150.00, 38, 'assets/images/kenyan-hair-body.webp', 1),
(3, 'Silk Wrap Hair Bonnet', 'silk-wrap-hair-bonnet', 'Protects styles overnight and reduces friction breakage. One size, adjustable band.', 600.00, 70, 'assets/images/kenyan-hair-body.webp', 0),

(4, 'Amber Musk Eau de Parfum', 'amber-musk-eau-de-parfum', 'A warm, long-lasting scent built around amber, musk and a hint of vanilla. 50ml.', 3600.00, 18, 'assets/images/kenyan-beauty-hero.webp', 1),
(4, 'Citrus Bloom Body Mist', 'citrus-bloom-body-mist', 'A fresh, everyday mist with notes of bergamot and white flowers.', 950.00, 42, 'assets/images/kenyan-beauty-hero.webp', 0),

(5, 'Shea Whip Body Butter', 'shea-whip-body-butter', 'A whipped, fast-absorbing butter that leaves skin soft for hours without feeling greasy.', 1300.00, 36, 'assets/images/kenyan-hair-body.webp', 0),
(5, 'Coffee Grind Body Scrub', 'coffee-grind-body-scrub', 'An energising scrub that buffs away dry skin and leaves a subtle coffee scent in the shower.', 1050.00, 29, 'assets/images/kenyan-hair-body.webp', 0),

(6, '8-Piece Vegan Brush Set', '8-piece-vegan-brush-set', 'A complete set of soft, cruelty-free brushes for face and eyes, with a travel pouch.', 2100.00, 22, 'assets/images/kenyan-makeup.webp', 0),
(6, 'Silicone Beauty Blender Duo', 'silicone-beauty-blender-duo', 'Two reusable silicone applicators that use less product and clean in seconds.', 700.00, 48, 'assets/images/kenyan-makeup.webp', 0);

CREATE INDEX idx_products_featured ON products (is_featured);
CREATE INDEX idx_orders_status_created ON orders (status, created_at);
