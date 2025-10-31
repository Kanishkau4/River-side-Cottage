<?php
// Simple admin page for managing room images
// Note: This is a basic example without authentication for demonstration purposes

require_once '../includes/db_connect.php';
require_once '../includes/booking_handler.php';

// Create admin directory if it doesn't exist
if (!is_dir('admin')) {
    mkdir('admin', 0777, true);
}

// Get all room types
$room_types = getAllRoomTypes();

// Handle image upload
$upload_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_image'])) {
    $room_type_id = $_POST['room_type_id'] ?? 0;
    $caption = $_POST['caption'] ?? '';
    
    if ($room_type_id && !empty($_FILES['image']['name'])) {
        $target_dir = "../images/room-images/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = "room-" . $room_type_id . "-" . time() . "." . $file_extension;
            $target_file = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                // Save to database
                try {
                    $stmt = $pdo->prepare("INSERT INTO room_images (room_type_id, image_url, caption) VALUES (?, ?, ?)");
                    $stmt->execute([$room_type_id, "images/room-images/" . $new_filename, $caption]);
                    $upload_message = "Image uploaded successfully!";
                } catch (Exception $e) {
                    $upload_message = "Database error: " . $e->getMessage();
                }
            } else {
                $upload_message = "Sorry, there was an error uploading your file.";
            }
        } else {
            $upload_message = "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        }
    } else {
        $upload_message = "Please select a room type and image file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Room Images - River side Cottage</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .admin-item {
            margin: 20px 0;
            padding: 15px;
            border-radius: 5px;
            background: white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .info { color: #17a2b8; }
        .room-images {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .room-image {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
        <div class="container">
            <a class="navbar-brand" href="../index.html">Luxe<span>Vista</span></a>
        </div>
    </nav>

    <div class="admin-container">
        <h1 class="text-center mb-4">Manage Room Images</h1>
        
        <?php if ($upload_message): ?>
            <div class="admin-item">
                <p class="<?php echo strpos($upload_message, 'successfully') !== false ? 'success' : 'error'; ?>">
                    <?php echo $upload_message; ?>
                </p>
            </div>
        <?php endif; ?>
        
        <div class="admin-item">
            <h3>Upload New Image</h3>
            <form action="manage-images.php" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="room_type_id">Room Type:</label>
                    <select name="room_type_id" class="form-control" required>
                        <option value="">Select Room Type</option>
                        <?php foreach ($room_types as $room): ?>
                            <option value="<?php echo $room['id']; ?>">
                                <?php echo htmlspecialchars($room['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="image">Image:</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="caption">Caption (optional):</label>
                    <input type="text" name="caption" class="form-control" placeholder="Image caption">
                </div>
                <div class="form-group">
                    <input type="submit" name="upload_image" value="Upload Image" class="btn btn-primary">
                </div>
            </form>
        </div>
        
        <div class="admin-item">
            <h3>Current Room Images</h3>
            <?php foreach ($room_types as $room): ?>
                <div class="admin-item">
                    <h4><?php echo htmlspecialchars($room['name']); ?></h4>
                    <?php
                    try {
                        $stmt = $pdo->prepare("SELECT image_url, caption FROM room_images WHERE room_type_id = ? ORDER BY id");
                        $stmt->execute([$room['id']]);
                        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        
                        if ($images):
                    ?>
                        <div class="room-images">
                            <?php foreach ($images as $image): ?>
                                <div>
                                    <img src="../<?php echo htmlspecialchars($image['image_url']); ?>" 
                                         alt="<?php echo htmlspecialchars($image['caption'] ?? $room['name']); ?>" 
                                         class="room-image">
                                    <?php if ($image['caption']): ?>
                                        <p><?php echo htmlspecialchars($image['caption']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p>No images found for this room type.</p>
                    <?php endif; ?>
                    <?php } catch (Exception $e) { ?>
                        <p class="error">Error loading images: <?php echo $e->getMessage(); ?></p>
                    <?php } ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <footer class="ftco-footer ftco-section img" style="background-image: url(../images/bg_4.jpg);">
        <div class="overlay"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-12 text-center">
                    <p>Copyright &copy;<script>document.write(new Date().getFullYear());</script> River side Cottage</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery-migrate-3.0.1.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>
</html>