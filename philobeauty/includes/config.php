<?php
/**
 * Bootstrap file — include this at the very top of every entry-point page.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('BUSINESS_NAME', 'PhiloBeauty');
define('BUSINESS_OWNER', 'Philomena Muithya');
define('BUSINESS_PHONE', '+254 743 432746');
define('BUSINESS_EMAIL', 'hello@philobeauty.co.ke');
define('BUSINESS_LOCATION', 'Machakos Town, Machakos County, Kenya');
define('DELIVERY_FEE', 300.00);

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';
