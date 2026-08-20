<?php
/**
 * Database connection (PDO)
 * Update these four values to match your local MySQL setup (e.g. XAMPP/WAMP defaults
 * are usually host=localhost, username=root, password='').
 */
$host     = 'localhost';
$dbname   = 'philobeauty';
$dbuser   = 'root';
$dbpass   = '';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $dbuser,
        $dbpass,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]
    );
} catch (PDOException $e) {
    die('Database connection failed. Make sure MySQL is running and the "philobeauty" database has been imported. (' . $e->getMessage() . ')');
}
