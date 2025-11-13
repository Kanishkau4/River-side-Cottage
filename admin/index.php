<?php
session_start();
require_once '../includes/db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: login.php');
    exit();
}

// Fetch all bookings
$bookings = [];
try {
    $stmt = $pdo->query("SELECT b.*, u.first_name, u.last_name, u.email, r.room_number, rt.name as room_type 
                         FROM bookings b 
                         JOIN users u ON b.user_id = u.id 
                         JOIN rooms r ON b.room_id = r.id 
                         JOIN room_types rt ON r.room_type_id = rt.id 
                         ORDER BY b.booking_date DESC");
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $booking_error = "Error fetching bookings: " . $e->getMessage();
}

// Fetch all users
$users = [];
try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $user_error = "Error fetching users: " . $e->getMessage();
}

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - River side Cottage</title>
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,600,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        .admin-section {
            margin-bottom: 40px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .section-header {
            background: #007bff;
            color: white;
            padding: 15px 20px;
            margin: 0;
        }
        .section-content {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-pending {
            background-color: #ffc107;
            color: #212529;
        }
        .status-confirmed {
            background-color: #28a745;
            color: white;
        }
        .status-cancelled {
            background-color: #dc3545;
            color: white;
        }
        .status-completed {
            background-color: #17a2b8;
            color: white;
        }
        .logout-btn {
            background: #dc3545;
            color: white;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
        }
        .logout-btn:hover {
            background: #c82333;
            color: white;
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
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <div>
                Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?>! 
                <a href="?logout=1" class="logout-btn">Logout</a>
            </div>
        </div>
        
        <!-- Admin Navigation -->
        <div class="admin-section">
            <div class="section-content" style="padding: 10px;">
                <a href="manage-images.php" class="btn btn-primary" style="margin-right: 10px;">Manage Room Images</a>
                <a href="index.php" class="btn btn-secondary">View Bookings & Users</a>
            </div>
        </div>
        
        <!-- Bookings Section -->
        <div class="admin-section">
            <h2 class="section-header">Recent Bookings</h2>
            <div class="section-content">
                <?php if (isset($booking_error)): ?>
                    <p class="error"><?php echo htmlspecialchars($booking_error); ?></p>
                <?php elseif (empty($bookings)): ?>
                    <p>No bookings found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>User</th>
                                    <th>Room</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                    <th>Guests</th>
                                    <th>Total Price</th>
                                    <th>Status</th>
                                    <th>Booking Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $booking): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($booking['id']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']); ?>
                                            <br>
                                            <small><?php echo htmlspecialchars($booking['email']); ?></small>
                                        </td>
                                        <td>
                                            <?php echo htmlspecialchars($booking['room_type']); ?>
                                            <br>
                                            <small>Room <?php echo htmlspecialchars($booking['room_number']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($booking['check_in_date']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['check_out_date']); ?></td>
                                        <td><?php echo htmlspecialchars($booking['guests']); ?></td>
                                        <td>Rs <?php echo number_format($booking['total_price'], 2); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo htmlspecialchars($booking['status']); ?>">
                                                <?php echo ucfirst(htmlspecialchars($booking['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo htmlspecialchars(date('M j, Y g:i A', strtotime($booking['booking_date']))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Users Section -->
        <div class="admin-section">
            <h2 class="section-header">Users</h2>
            <div class="section-content">
                <?php if (isset($user_error)): ?>
                    <p class="error"><?php echo htmlspecialchars($user_error); ?></p>
                <?php elseif (empty($users)): ?>
                    <p>No users found.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table>
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Registration Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['id']); ?></td>
                                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone']); ?></td>
                                        <td><?php echo htmlspecialchars(date('M j, Y', strtotime($user['created_at']))); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
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