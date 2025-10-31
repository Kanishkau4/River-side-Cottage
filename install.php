<?php
// Installation script for LuxeVista Resort Booking System

echo "<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>LuxeVista Resort - Installation</title>
    <link href='https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,600,700&display=swap' rel='stylesheet'>
    <link rel='stylesheet' href='css/style.css'>
    <style>
        .install-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .install-item {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        .info { color: #17a2b8; }
        pre {
            background: #f1f1f1;
            padding: 10px;
            border-radius: 5px;
            overflow-x: auto;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 10px 0;
        }
        .btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <nav class='navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light' id='ftco-navbar'>
        <div class='container'>
            <a class='navbar-brand' href='index.html'>Luxe<span>Vista</span></a>
        </div>
    </nav>

    <div class='install-container'>
        <h1 class='text-center mb-4'>LuxeVista Resort Installation</h1>";

// Check if already installed
if (file_exists('installed.txt')) {
    echo "<div class='install-item'>";
    echo "<h3>System Already Installed</h3>";
    echo "<p class='success'>The booking system is already installed.</p>";
    echo "<p><a href='index.html' class='btn'>Go to Homepage</a></p>";
    echo "<p><a href='diagnostics.php' class='btn'>Run Diagnostics</a></p>";
    echo "</div>";
    echo "</div></body></html>";
    exit;
}

echo "<div class='install-item'>";
echo "<h3>Installation Progress</h3>";

try {
    // Include database connection
    require_once 'includes/db_connect.php';
    
    if ($pdo === null) {
        echo "<p class='error'>Database connection failed. Please check your database configuration in includes/db_connect.php</p>";
        echo "<p><a href='diagnostics.php' class='btn'>Run Diagnostics</a></p>";
        echo "</div></div></body></html>";
        exit;
    }
    
    echo "<p class='success'>Database connection successful.</p>";
    
    // Read the SQL schema file
    $sqlFile = 'db_schema.sql';
    if (!file_exists($sqlFile)) {
        echo "<p class='error'>Database schema file not found: $sqlFile</p>";
        echo "</div></div></body></html>";
        exit;
    }
    
    $sqlContent = file_get_contents($sqlFile);
    if ($sqlContent === false) {
        echo "<p class='error'>Failed to read database schema file: $sqlFile</p>";
        echo "</div></div></body></html>";
        exit;
    }
    
    echo "<p class='info'>Database schema file loaded successfully.</p>";
    
    // Split SQL content into individual statements
    $statements = explode(';', $sqlContent);
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (empty($statement)) {
            continue;
        }
        
        try {
            $pdo->exec($statement);
            $successCount++;
        } catch (Exception $e) {
            $errorCount++;
            echo "<p class='error'>Error executing statement: " . $e->getMessage() . "</p>";
            echo "<pre>" . htmlspecialchars($statement) . "</pre>";
        }
    }
    
    echo "<p class='info'>Executed $successCount statements successfully.</p>";
    if ($errorCount > 0) {
        echo "<p class='error'>$errorCount statements failed.</p>";
    } else {
        // Create installed.txt file to indicate successful installation
        file_put_contents('installed.txt', date('Y-m-d H:i:s'));
        echo "<p class='success'>Installation completed successfully!</p>";
        echo "<p><a href='index.html' class='btn'>Go to Homepage</a></p>";
        echo "<p><a href='diagnostics.php' class='btn'>Run Diagnostics</a></p>";
    }
    
} catch (Exception $e) {
    echo "<p class='error'>Installation error: " . $e->getMessage() . "</p>";
    echo "<p><a href='diagnostics.php' class='btn'>Run Diagnostics</a></p>";
}

echo "</div></div></body></html>";
?>