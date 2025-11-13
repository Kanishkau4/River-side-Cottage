<?php
require_once 'db_connect.php';

function createBooking($check_in, $check_out, $room_id, $guests, $first_name, $last_name, $email, $phone, $special_requests = '') {
    global $pdo;
    
    // Check if database connection is available
    if ($pdo === null) {
        return ['success' => false, 'message' => 'Database connection failed. Please contact the administrator.'];
    }
    
    try {
        // Validate and format dates - try multiple formats
        $check_in_date = DateTime::createFromFormat('Y-m-d', $check_in);
        $check_out_date = DateTime::createFromFormat('Y-m-d', $check_out);
        
        // If the standard format doesn't work, try other common formats
        if (!$check_in_date) {
            // Try m/d/Y format (common US format)
            $check_in_date = DateTime::createFromFormat('m/d/Y', $check_in);
            
            // Try other possible formats if needed
            if (!$check_in_date) {
                $check_in_date = DateTime::createFromFormat('d/m/Y', $check_in);
            }
        }
        
        if (!$check_out_date) {
            // Try m/d/Y format (common US format)
            $check_out_date = DateTime::createFromFormat('m/d/Y', $check_out);
            
            // Try other possible formats if needed
            if (!$check_out_date) {
                $check_out_date = DateTime::createFromFormat('d/m/Y', $check_out);
            }
        }
        
        if (!$check_in_date || !$check_out_date) {
            return ['success' => false, 'message' => 'Invalid date format. Please use a valid date.'];
        }
        
        // Validate that check-out is after check-in
        if ($check_in_date >= $check_out_date) {
            return ['success' => false, 'message' => 'Check-out date must be after check-in date.'];
        }
        
        // Format dates as strings in the correct format for database storage
        $check_in = $check_in_date->format('Y-m-d');
        $check_out = $check_out_date->format('Y-m-d');
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
    $subject = "Booking Confirmation - River side Cottage";
    
    // Email body
    $message = "
    <html>
    <head>
        <title>Booking Confirmation</title>
    </head>
    <body>
        <h2>Booking Confirmation - River side Cottage</h2>
        <p>Thank you for booking with River side Cottage. Your reservation details are as follows:</p>
        
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
                <td>Rs $total_price</td>
            </tr>
        </table>";
        
    if (!empty($special_requests)) {
        $message .= "<p><strong>Special Requests:</strong> " . nl2br(htmlspecialchars($special_requests)) . "</p>";
    }
        
    $message .= "
        <p>We look forward to welcoming you to River side Cottage!</p>
        <p>For more information, please call us at 0771313951, 0717999566, or 0762831769.</p>
        <p>Best regards,<br/>
        The River side Cottage Team</p>
    </body>
    </html>
    ";
    
    // Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: River side Cottage <noreply@riversidecottage.com>" . "\r\n";
    
    // Send email (suppress warnings in case mail server is not configured)
    $mail_result = @mail($to, $subject, $message, $headers);
    
    // Optionally log the result for debugging (only in development)
    if (!$mail_result) {
        error_log("Failed to send confirmation email to: $to");
    }
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