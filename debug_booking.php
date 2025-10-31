<?php
// Debug script to test the booking system

echo "<h2>Booking System Debug</h2>";

// Include the database connection
require_once 'includes/db_connect.php';

// Check if database connection exists
if ($pdo === null) {
    echo "<p style='color: red;'>ERROR: Database connection failed!</p>";
    echo "<p>Please check your database configuration in includes/db_connect.php</p>";
    exit;
} else {
    echo "<p style='color: green;'>SUCCESS: Database connection established</p>";
}

// Test if required tables exist
$tables_to_check = ['room_types', 'rooms', 'users', 'bookings'];

foreach ($tables_to_check as $table) {
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?");
        $stmt->execute([DB_NAME, $table]);
        $exists = $stmt->fetchColumn();
        
        if ($exists) {
            echo "<p style='color: green;'>SUCCESS: Table '$table' exists</p>";
        } else {
            echo "<p style='color: red;'>ERROR: Table '$table' does not exist</p>";
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>ERROR checking table '$table': " . $e->getMessage() . "</p>";
    }
}

// Test if we can fetch room data
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM room_types");
    $count = $stmt->fetchColumn();
    echo "<p>Room types in database: $count</p>";
    
    if ($count > 0) {
        $stmt = $pdo->query("SELECT * FROM room_types LIMIT 5");
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>Sample room types:</h3>";
        echo "<ul>";
        foreach ($rooms as $room) {
            echo "<li>" . htmlspecialchars($room['name']) . " - $" . $room['price'] . "/night</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR fetching room data: " . $e->getMessage() . "</p>";
}

// Test if we can fetch room data
try {
    $stmt = $pdo->query("SELECT COUNT(*) FROM rooms");
    $count = $stmt->fetchColumn();
    echo "<p>Rooms in database: $count</p>";
    
    if ($count > 0) {
        $stmt = $pdo->query("SELECT * FROM rooms LIMIT 5");
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<h3>Sample rooms:</h3>";
        echo "<ul>";
        foreach ($rooms as $room) {
            echo "<li>Room #" . htmlspecialchars($room['room_number']) . " (Status: " . $room['status'] . ")</li>";
        }
        echo "</ul>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>ERROR fetching room data: " . $e->getMessage() . "</p>";
}

echo "<h3>Database Configuration:</h3>";
echo "<ul>";
echo "<li>Host: " . DB_HOST . "</li>";
echo "<li>Database: " . DB_NAME . "</li>";
echo "<li>User: " . DB_USER . "</li>";
echo "<li>Password: " . (DB_PASS ? "**** (set)" : "(empty)") . "</li>";
echo "</ul>";

echo "<h3>PHP Configuration:</h3>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>PDO Available: " . (extension_loaded('pdo') ? 'Yes' : 'No') . "</li>";
echo "<li>PDO MySQL Available: " . (extension_loaded('pdo_mysql') ? 'Yes' : 'No') . "</li>";
echo "</ul>";
?>