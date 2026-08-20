# PhiloBeauty Cosmetics Management System

PhiloBeauty is a modern, responsive PHP/MySQL cosmetics store and management system for a beauty business based in Machakos Town, Kenya.

## Business details

- Business: PhiloBeauty
- Proprietor / student: Philomena Muithya
- Location: Machakos Town, Machakos County, Kenya
- Telephone: +254 743 432746
- Email: hello@philobeauty.co.ke

## Project objectives

The system is designed to:

1. Register and securely authenticate PhiloBeauty customers and administrators.
2. Categorise, display, search and maintain available cosmetics and stock.
3. Enable customers to add products to a shopping bag and place orders online.
4. Record payments and coordinate the processing and delivery of customer orders.
5. Allow customers to track an order using a secure tracking code and phone number.
6. Generate useful sales, product, payment, customer and delivery reports for management.

## Main features

### Customer storefront

- Responsive homepage with the approved PhiloBeauty logo and original Kenyan beauty photography.
- Product categories for skincare, makeup, haircare, fragrance, bath and body, and tools.
- Product search, category filtering, sorting, pagination and detailed product pages.
- Quick-add product cards, session shopping bag and stock-aware checkout.
- Customer registration, login, account page and order history.
- M-Pesa or cash-on-delivery order records.
- Public order tracking using the order/tracking code and checkout phone number.
- Business information and contact form for the Machakos store.

### Administration

- Responsive operations dashboard with products, customers, orders, paid revenue, fulfilment and low-stock statistics.
- Category, product and stock management.
- Order status management and item-level order details.
- Customer register with order count and lifetime value.
- Payment records with transaction references and payment statuses.
- Delivery assignment, rider details, ETA and fulfilment status.
- Contact-message management.
- Date-filtered business reports with print-friendly layout.

### Technical and security improvements

- PDO prepared statements for database queries.
- Password hashing and verification.
- CSRF protection for state-changing forms.
- Session ID regeneration after successful login.
- Database transactions for order, stock, payment and delivery creation.
- Stock updates are conditional to reduce overselling.
- Output escaping, email validation and safe internal redirects.
- Re-importable SQL schema with indexes and referential integrity.
- Local WebP photographs and SVG fallbacks for reliable, fast loading.
- Cache-busted CSS, JavaScript and logo assets.

## Requirements

- PHP 8.0 or later
- MySQL 8+ or MariaDB
- Apache through XAMPP/WAMP/MAMP, or another PHP web server

## Installation with XAMPP

1. Extract this archive and copy the `philobeauty` folder to `C:\xampp\htdocs\`.
2. Start Apache and MySQL in the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Import `database/philobeauty.sql`.
5. Visit `http://localhost/philobeauty/setup.php` once.
6. Delete `setup.php` after the administrator is created.
7. Open `http://localhost/philobeauty/`.

If MySQL uses different credentials, update `includes/db.php`.

## Default administrator

- Email: `admin@philobeauty.com`
- Password: `Admin@123`

Change the password before production use. The M-Pesa module is a coursework transaction record, not a connection to the live Safaricom Daraja API.

## Important routes

- Storefront: `/philobeauty/`
- Shop: `/philobeauty/shop.php`
- Track an order: `/philobeauty/track-order.php`
- Customer account: `/philobeauty/account.php`
- Administrator: `/philobeauty/admin/login.php`
- Reports: `/philobeauty/admin/reports.php`

## Project structure

```text
philobeauty/
├── admin/                  Administration modules
├── assets/
│   ├── css/style.css       Complete responsive visual system
│   ├── images/             Logo, Kenyan photographs and fallbacks
│   └── js/script.js        Navigation, controls and interactions
├── database/
│   └── philobeauty.sql     Schema, indexes and sample catalogue
├── includes/               Configuration, layouts and shared helpers
├── index.php               Modern public homepage
├── shop.php                Searchable cosmetics catalogue
├── cart.php                Customer shopping bag
├── checkout.php            Delivery and payment checkout
├── track-order.php         Customer order tracking
└── setup.php               One-time administrator creation
```

See `COMPARISON_AND_IMPROVEMENTS.md` for the comparison with the Reens Restaurant reference project.
