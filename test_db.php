<?php
require_once 'includes/db_connect.php';

try {
    // Test the connection
    echo "Database connection successful!<br>";
    
    // Test if the database exists and has tables
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    if (count($tables) > 0) {
        echo "Tables found in the database:<br>";
        foreach ($tables as $table) {
            echo "- " . $table . "<br>";
        }
    } else {
        echo "No tables found in the database.<br>";
    }
    
    // Test if we can fetch room data
    $stmt = $pdo->query("SELECT * FROM room_types LIMIT 5");
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($rooms) > 0) {
        echo "<br>Room types in the database:<br>";
        foreach ($rooms as $room) {
            echo "- " . $room['name'] . " ($" . $room['price'] . "/night)<br>";
        }
    } else {
        echo "<br>No room types found in the database.<br>";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>