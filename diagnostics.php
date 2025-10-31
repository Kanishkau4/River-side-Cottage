<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LuxeVista Resort - System Diagnostics</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .diagnostics-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .diagnostics-item {
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
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.html">Luxe<span>Vista</span></a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="oi oi-menu"></span> Menu
            </button>

            <div class="collapse navbar-collapse" id="ftco-nav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a href="index.html" class="nav-link">Home</a></li>
                    <li class="nav-item"><a href="rooms.html" class="nav-link">Our Rooms</a></li>
                    <li class="nav-item"><a href="drinking-area.html" class="nav-link">Drinking Area</a></li>
                    <li class="nav-item"><a href="kitchen.html" class="nav-link">Kitchen</a></li>
                    <li class="nav-item"><a href="river-area.html" class="nav-link">River Area</a></li>
                    <li class="nav-item"><a href="about.html" class="nav-link">About Us</a></li>
                    <li class="nav-item"><a href="blog.html" class="nav-link">Blog</a></li>
                    <li class="nav-item"><a href="contact.html" class="nav-link">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="diagnostics-container">
        <h1 class="text-center mb-4">System Diagnostics</h1>
        
        <?php
        // Check PHP version
        echo "<div class='diagnostics-item'>";
        echo "<h3>PHP Configuration</h3>";
        echo "<p class='info'>PHP Version: " . phpversion() . "</p>";
        echo "<p class='info'>PDO Extension: " . (extension_loaded('pdo') ? "<span class='success'>Available</span>" : "<span class='error'>Not Available</span>") . "</p>";
        echo "<p class='info'>PDO MySQL Extension: " . (extension_loaded('pdo_mysql') ? "<span class='success'>Available</span>" : "<span class='error'>Not Available</span>") . "</p>";
        echo "</div>";

        // Check database connection
        echo "<div class='diagnostics-item'>";
        echo "<h3>Database Connection</h3>";
        
        try {
            require_once 'includes/db_connect.php';
            
            if ($pdo === null) {
                echo "<p class='error'>Database connection failed. Check your database configuration.</p>";
            } else {
                echo "<p class='success'>Database connection successful.</p>";
                
                // Check if database exists
                try {
                    $stmt = $pdo->query("SHOW DATABASES LIKE 'luxevista_resort'");
                    $dbExists = $stmt->fetch();
                    
                    if ($dbExists) {
                        echo "<p class='success'>Database 'luxevista_resort' exists.</p>";
                        
                        // Check tables
                        $tables = ['room_types', 'rooms', 'users', 'bookings'];
                        foreach ($tables as $table) {
                            try {
                                $stmt = $pdo->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?");
                                $stmt->execute(['luxevista_resort', $table]);
                                $exists = $stmt->fetchColumn();
                                
                                if ($exists) {
                                    echo "<p class='success'>Table '$table' exists.</p>";
                                } else {
                                    echo "<p class='error'>Table '$table' does not exist.</p>";
                                }
                            } catch (Exception $e) {
                                echo "<p class='error'>Error checking table '$table': " . $e->getMessage() . "</p>";
                            }
                        }
                        
                        // Check sample data
                        try {
                            $stmt = $pdo->query("SELECT COUNT(*) FROM room_types");
                            $count = $stmt->fetchColumn();
                            echo "<p class='info'>Room types in database: $count</p>";
                        } catch (Exception $e) {
                            echo "<p class='error'>Error counting room types: " . $e->getMessage() . "</p>";
                        }
                    } else {
                        echo "<p class='error'>Database 'luxevista_resort' does not exist.</p>";
                    }
                } catch (Exception $e) {
                    echo "<p class='error'>Error checking database: " . $e->getMessage() . "</p>";
                }
            }
        } catch (Exception $e) {
            echo "<p class='error'>Error including db_connect.php: " . $e->getMessage() . "</p>";
        }
        echo "</div>";

        // Check file permissions
        echo "<div class='diagnostics-item'>";
        echo "<h3>File Permissions</h3>";
        
        $filesToCheck = [
            'includes/db_connect.php',
            'includes/booking_handler.php',
            'book.php'
        ];
        
        foreach ($filesToCheck as $file) {
            if (file_exists($file)) {
                $perms = fileperms($file);
                $readable = is_readable($file) ? "<span class='success'>Readable</span>" : "<span class='error'>Not Readable</span>";
                $writable = is_writable($file) ? "<span class='warning'>Writable</span>" : "<span class='info'>Not Writable</span>";
                echo "<p class='info'>$file: $readable, $writable</p>";
            } else {
                echo "<p class='error'>$file: File does not exist</p>";
            }
        }
        echo "</div>";

        // Show database configuration
        echo "<div class='diagnostics-item'>";
        echo "<h3>Database Configuration</h3>";
        echo "<pre>";
        echo "DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'Not defined') . "\n";
        echo "DB_NAME: " . (defined('DB_NAME') ? DB_NAME : 'Not defined') . "\n";
        echo "DB_USER: " . (defined('DB_USER') ? DB_USER : 'Not defined') . "\n";
        echo "DB_PASS: " . (defined('DB_PASS') ? (DB_PASS ? '**** (set)' : '(empty)') : 'Not defined') . "\n";
        echo "</pre>";
        echo "</div>";
        ?>
    </div>

    <footer class="ftco-footer ftco-section img" style="background-image: url(images/bg_4.jpg);">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>Copyright &copy;<script>document.write(new Date().getFullYear());</script> LuxeVista Resort</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="js/jquery.min.js"></script>
    <script src="js/jquery-migrate-3.0.1.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>