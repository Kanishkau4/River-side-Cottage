<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root'); // Change this to your MySQL username
define('DB_PASS', '');     // Change this to your MySQL password
define('DB_NAME', 'luxevista_resort');

// Create connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // Log the error instead of dying
    error_log("Database Connection Failed: " . $e->getMessage());
    // For debugging, you can uncomment the line below:
    // die("Connection failed: " . $e->getMessage());
    
    // Set $pdo to null so we can check if connection failed
    $pdo = null;
}
?>