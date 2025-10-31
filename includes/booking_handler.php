<?php
require_once 'db_connect.php';

function createBooking($check_in, $check_out, $room_id, $guests, $first_name, $last_name, $email, $phone, $special_requests = '') {
    global $pdo;
    
    // Check if database connection is available
    if ($pdo === null) {
        return ['success' => false, 'message' => 'Database connection failed. Please contact the administrator.'];
    }
    
    try {
        // Check if user exists, if not create new user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user) {
            $user_id = $user['id'];
        } else {
            // Create new user
            $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, phone) VALUES (?, ?, ?, ?)");
            $stmt->execute([$first_name, $last_name, $email, $phone]);
            $user_id = $pdo->lastInsertId();
        }
        
        // Get room price and details
        $stmt = $pdo->prepare("SELECT r.room_number, rt.name, rt.price FROM rooms r JOIN room_types rt ON r.room_type_id = rt.id WHERE r.id = ?");
        $stmt->execute([$room_id]);
        $room = $stmt->fetch();
        
        if (!$room) {
            return ['success' => false, 'message' => 'Room not found'];
        }
        
        // Calculate total price (simplified - just nights * room price)
        $check_in_date = new DateTime($check_in);
        $check_out_date = new DateTime($check_out);
        $interval = $check_in_date->diff($check_out_date);
        $nights = $interval->days;
        $total_price = $nights * $room['price'];
        
        // Create booking
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, room_id, check_in_date, check_out_date, guests, total_price, special_requests) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $room_id, $check_in, $check_out, $guests, $total_price, $special_requests]);
        $booking_id = $pdo->lastInsertId();
        
        // Update room status to booked
        $stmt = $pdo->prepare("UPDATE rooms SET status = 'booked' WHERE id = ?");
        $stmt->execute([$room_id]);
        
        // Send confirmation email
        sendConfirmationEmail($email, $first_name, $last_name, $booking_id, $room, $check_in, $check_out, $guests, $total_price, $special_requests);
        
        return ['success' => true, 'booking_id' => $booking_id, 'total_price' => $total_price];
    } catch (Exception $e) {
        error_log("Booking Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Booking failed. Please try again later.'];
    }
}

function sendConfirmationEmail($email, $first_name, $last_name, $booking_id, $room, $check_in, $check_out, $guests, $total_price, $special_requests) {
    // Email configuration
    $to = $email;
    $subject = "Booking Confirmation - LuxeVista Resort";
    
    // Email body
    $message = "
    <html>
    <head>
        <title>Booking Confirmation</title>
    </head>
    <body>
        <h2>Booking Confirmation - LuxeVista Resort</h2>
        <p>Dear $first_name $last_name,</p>
        <p>Thank you for booking with LuxeVista Resort. Your reservation details are as follows:</p>
        
        <table border='1' cellpadding='10'>
            <tr>
                <td><strong>Booking ID:</strong></td>
                <td>$booking_id</td>
            </tr>
            <tr>
                <td><strong>Room Type:</strong></td>
                <td>{$room['name']}</td>
            </tr>
            <tr>
                <td><strong>Room Number:</strong></td>
                <td>{$room['room_number']}</td>
            </tr>
            <tr>
                <td><strong>Check-in Date:</strong></td>
                <td>$check_in</td>
            </tr>
            <tr>
                <td><strong>Check-out Date:</strong></td>
                <td>$check_out</td>
            </tr>
            <tr>
                <td><strong>Number of Guests:</strong></td>
                <td>$guests</td>
            </tr>
            <tr>
                <td><strong>Total Price:</strong></td>
                <td>$$total_price</td>
            </tr>
        </table>";
        
    if (!empty($special_requests)) {
        $message .= "<p><strong>Special Requests:</strong> " . nl2br(htmlspecialchars($special_requests)) . "</p>";
    }
        
    $message .= "
        <p>We look forward to welcoming you to LuxeVista Resort!</p>
        <p>Best regards,<br/>
        The LuxeVista Resort Team</p>
    </body>
    </html>
    ";
    
    // Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: LuxeVista Resort <noreply@luxevistaresort.com>" . "\r\n";
    
    // Send email
    mail($to, $subject, $message, $headers);
}

function getAvailableRooms($check_in, $check_out) {
    global $pdo;
    
    // Check if database connection is available
    if ($pdo === null) {
        return [];
    }
    
    try {
        // This is a simplified query that doesn't check for overlapping bookings
        // In a production environment, you would need a more complex query
        $stmt = $pdo->prepare("SELECT r.id, r.room_number, rt.name, rt.price, rt.capacity, rt.description, rt.amenities, rt.image_url 
                              FROM rooms r 
                              JOIN room_types rt ON r.room_type_id = rt.id 
                              WHERE r.status = 'available'");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Get Rooms Error: " . $e->getMessage());
        return [];
    }
}

function getRoomDetails($room_type_id) {
    global $pdo;
    
    // Check if database connection is available
    if ($pdo === null) {
        return null;
    }
    
    try {
        // Get room type details
        $stmt = $pdo->prepare("SELECT * FROM room_types WHERE id = ?");
        $stmt->execute([$room_type_id]);
        $room_type = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$room_type) {
            return null;
        }
        
        // Get room images
        $stmt = $pdo->prepare("SELECT image_url, caption FROM room_images WHERE room_type_id = ? ORDER BY id");
        $stmt->execute([$room_type_id]);
        $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $room_type['images'] = $images;
        
        return $room_type;
    } catch (Exception $e) {
        error_log("Get Room Details Error: " . $e->getMessage());
        return null;
    }
}

function getAllRoomTypes() {
    global $pdo;
    
    // Check if database connection is available
    if ($pdo === null) {
        return [];
    }
    
    try {
        // Get all room types
        $stmt = $pdo->query("SELECT * FROM room_types");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Get All Room Types Error: " . $e->getMessage());
        return [];
    }
}

?>