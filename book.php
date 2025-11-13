<?php
require_once 'includes/db_connect.php';
require_once 'includes/booking_handler.php';

// Get room type ID from URL parameter
$room_type_id = isset($_GET['room_type']) ? (int)$_GET['room_type'] : 0;
$guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 0;
$check_in = isset($_GET['check_in']) ? $_GET['check_in'] : '';
$check_out = isset($_GET['check_out']) ? $_GET['check_out'] : '';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $room_id = $_POST['room_id'] ?? '';
    $guests = $_POST['guests'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $special_requests = $_POST['special_requests'] ?? '';
    
    // Validate required fields
    if (!empty($check_in) && !empty($check_out) && !empty($room_id) && !empty($guests) && 
        !empty($first_name) && !empty($last_name) && !empty($email) && !empty($phone)) {
        
        // Create booking
        $result = createBooking($check_in, $check_out, $room_id, $guests, $first_name, $last_name, $email, $phone, $special_requests);
        
        if ($result['success']) {
            $booking_success = true;
            $booking_id = $result['booking_id'];
            $total_price = $result['total_price'];
        } else {
            $booking_error = $result['message'];
        }
    } else {
        $booking_error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>River side Cottage - Booking</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans:200,300,400,600,700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="css/open-iconic-bootstrap.min.css">
    <link rel="stylesheet" href="css/animate.css">
    
    <link rel="stylesheet" href="css/owl.carousel.min.css">
    <link rel="stylesheet" href="css/owl.theme.default.min.css">
    <link rel="stylesheet" href="css/magnific-popup.css">

    <link rel="stylesheet" href="css/aos.css">

    <link rel="stylesheet" href="css/ionicons.min.css">

    <link rel="stylesheet" href="css/bootstrap-datepicker.css">
    <link rel="stylesheet" href="css/jquery.timepicker.css">

    
    <link rel="stylesheet" href="css/flaticon.css">
    <link rel="stylesheet" href="css/icomoon.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
      /* Custom styles for booking form */
      .contact-form {
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        animation-duration: 0.5s;
      }
      
      .form-group label {
        font-weight: 600;
        color: #333;
      }
      
      .form-control {
        border-radius: 5px;
        padding: 12px 15px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
      }
      
      .form-control:focus {
        border-color: #2f89fc;
        box-shadow: 0 0 0 0.2rem rgba(47, 137, 252, 0.25);
      }
      
      .btn-primary {
        background: #2f89fc;
        border-color: #2f89fc;
        padding: 12px 30px;
        border-radius: 30px;
        font-weight: 600;
        transition: all 0.3s ease;
      }
      
      .btn-primary:hover {
        background: #1a75e8;
        border-color: #1a75e8;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(47, 137, 252, 0.3);
      }
      
      .alert {
        border-radius: 8px;
        animation-duration: 0.3s;
      }
      
      .text-center {
        text-align: center;
      }
      
      h3 {
        color: #2f89fc;
        position: relative;
        padding-bottom: 10px;
      }
      
      h3:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 50px;
        height: 3px;
        background: #2f89fc;
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
	          <li class="nav-item"><a href="contact.html" class="nav-link">Contact</a></li>
	        </ul>
	      </div>
	    </div>
	  </nav>
    <!-- END nav -->

    <div class="hero-wrap" style="background-image: url('images/bg_3.jpg');">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text d-flex align-itemd-center justify-content-center">
          <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
          	<div class="text">
	            <p class="breadcrumbs mb-2"><span class="mr-2"><a href="index.html">Home</a></span> <span>Booking</span></p>
	            <h1 class="mb-4 bread">Reserve Your Stay</h1>
            </div>
          </div>
        </div>
      </div>
    </div>

    <section class="ftco-section contact-section bg-light">
      <div class="container">
        <div class="row block-9 justify-content-center">
          <div class="col-md-12 d-flex"> <!-- Increased column size from 10 to 12 for full width -->
            <div class="row justify-content-center w-100">
              <div class="col-lg-8"> <!-- Added a container for the form with specific width -->
                <?php if (isset($booking_success) && $booking_success): ?>
                  <div class="alert alert-success animated fadeInUp" role="alert">
                    <h4 class="alert-heading">Booking Successful!</h4>
                    <p>Your booking has been confirmed. Booking ID: <?php echo $booking_id; ?></p>
                    <p>Total Price: Rs <?php echo number_format($total_price, 2); ?></p>
                    <hr>
                    <p class="mb-0">For more information, call us at 0771313951, 0717999566, or 0762831769.</p>
                  </div>
                <?php elseif (isset($booking_error)): ?>
                  <div class="alert alert-danger animated fadeInUp" role="alert">
                    <h4 class="alert-heading">Booking Error!</h4>
                    <p><?php echo $booking_error; ?></p>
                  </div>
                <?php endif; ?>
                
                <?php if ($pdo === null): ?>
                  <div class="alert alert-warning animated fadeInUp" role="alert">
                    <h4 class="alert-heading">Database Connection Issue</h4>
                    <p>Our booking system is currently unavailable. Please try again later or contact us directly.</p>
                  </div>
                <?php endif; ?>
                
                <form action="book.php" method="POST" class="bg-white p-5 contact-form animated fadeInUp">
                  <h3 class="mb-4 text-center">Booking Information</h3>
                  <div class="form-group">
                    <label for="check_in">Check-in Date *</label>
                    <input type="text" name="check_in" class="form-control checkin_date" placeholder="Check-in date" value="<?php echo htmlspecialchars($check_in); ?>" required>
                  </div>
                  <div class="form-group">
                    <label for="check_out">Check-out Date *</label>
                    <input type="text" name="check_out" class="form-control checkout_date" placeholder="Check-out date" value="<?php echo htmlspecialchars($check_out); ?>" required>
                  </div>
                  <div class="form-group">
                    <label for="room_id">Room Type *</label>
                    <select name="room_id" class="form-control" required>
                      <option value="">Select Room Type</option>
                      <?php 
                      // Get all room types
                      $room_types = getAllRoomTypes();
                      foreach ($room_types as $type): ?>
                        <option value="<?php echo $type['id']; ?>" <?php echo ($room_type_id == $type['id']) ? 'selected' : ''; ?>><?php echo htmlspecialchars($type['name']); ?> - Rs <?php echo number_format($type['price'], 2); ?>/night</option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="form-group">
                    <label for="guests">Number of Guests *</label>
                    <select name="guests" class="form-control" required>
                      <option value="">Select Number of Guests</option>
                      <option value="1" <?php echo ($guests == 1) ? 'selected' : ''; ?>>1 Guest</option>
                      <option value="2" <?php echo ($guests == 2) ? 'selected' : ''; ?>>2 Guests</option>
                    </select>
                  </div>
                  
                  <h3 class="mb-4 mt-5 text-center">Personal Information</h3>
                  <div class="form-group">
                    <label for="first_name">First Name *</label>
                    <input type="text" name="first_name" class="form-control" placeholder="Your First Name" required>
                  </div>
                  <div class="form-group">
                    <label for="last_name">Last Name *</label>
                    <input type="text" name="last_name" class="form-control" placeholder="Your Last Name" required>
                  </div>
                  <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                  </div>
                  <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="text" name="phone" class="form-control" placeholder="Your Phone Number" required>
                  </div>
                  <div class="form-group">
                    <label for="special_requests">Special Requests</label>
                    <textarea name="special_requests" cols="30" rows="5" class="form-control" placeholder="Any special requests or notes"></textarea>
                  </div>
                  <div class="form-group text-center"> <!-- Centered the submit button -->
                    <input type="submit" value="Complete Booking" class="btn btn-primary py-3 px-5" <?php if ($pdo === null) echo 'disabled'; ?>>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <footer class="ftco-footer ftco-section img" style="background-image: url(images/bg_4.jpg);">
    	<div class="overlay"></div>
      <div class="container">
        <div class="row mb-5">
          <div class="col-md">
                <div class="ftco-footer-widget mb-4">
                  <h2 class="ftco-heading-2">River side Cottage</h2>
                  <p>Far far away, behind the word mountains, far from the countries Vokalia and Consonantia, there live the blind texts.</p>
                  <ul class="ftco-footer-social list-unstyled float-md-left float-lft mt-5">
                    <li class="ftco-animate"><a href="#"><span class="icon-twitter"></span></a></li>
                    <li class="ftco-animate"><a href="#"><span class="icon-facebook"></span></a></li>
                    <li class="ftco-animate"><a href="#"><span class="icon-instagram"></span></a></li>
                  </ul>
                </div>
              </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4 ml-md-5">
              <h2 class="ftco-heading-2">Useful Links</h2>
              <ul class="list-unstyled">
                <li><a href="blog.html" class="py-2 d-block">Blog</a></li>
                <li><a href="rooms.html" class="py-2 d-block">Rooms</a></li>
                <li><a href="drinking-area.html" class="py-2 d-block">Drinking Area</a></li>
                <li><a href="kitchen.html" class="py-2 d-block">Kitchen</a></li>
                <li><a href="river-area.html" class="py-2 d-block">River Area</a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
             <div class="ftco-footer-widget mb-4">
              <h2 class="ftco-heading-2">Privacy</h2>
              <ul class="list-unstyled">
                <li><a href="about.html" class="py-2 d-block">About Us</a></li>
                <li><a href="contact.html" class="py-2 d-block">Contact Us</a></li>
              </ul>
            </div>
          </div>
          <div class="col-md">
            <div class="ftco-footer-widget mb-4">
            	<h2 class="ftco-heading-2">Have a Questions?</h2>
            	<div class="block-23 mb-3">
	              <ul>
	                <li><span class="icon icon-map-marker"></span><span class="text">Poramadilla, Pelwatta</span></li>
	                <li><a href="#"><span class="icon icon-phone"></span><span class="text">0771313951 WhatsApp, 0771313951 normal, 0717999566 normal, 0762831769</span></a></li>
	                <li><a href="#"><span class="icon icon-envelope"></span><span class="text">info@riversidecottage.com</span></a></li>
	              </ul>
	            </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12 text-center">

            <p>Copyright &copy;<script>document.write(new Date().getFullYear());</script> River side Cottage. All rights reserved.</p>
          </div>
        </div>
      </div>
    </footer>
    
  

  <!-- loader -->
  <div id="ftco-loader" class="show fullscreen"><svg class="circular" width="48px" height="48px"><circle class="path-bg" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke="#eeeeee"/><circle class="path" cx="24" cy="24" r="22" fill="none" stroke-width="4" stroke-miterlimit="10" stroke="#F96D00"/></svg></div>


  <script src="js/jquery.min.js"></script>
  <script src="js/jquery-migrate-3.0.1.min.js"></script>
  <script src="js/popper.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/jquery.easing.1.3.js"></script>
  <script src="js/jquery.waypoints.min.js"></script>
  <script src="js/jquery.stellar.min.js"></script>
  <script src="js/owl.carousel.min.js"></script>
  <script src="js/jquery.magnific-popup.min.js"></script>
  <script src="js/aos.js"></script>
  <script src="js/jquery.animateNumber.min.js"></script>
  <script src="js/bootstrap-datepicker.js"></script>
  <script src="js/scrollax.min.js"></script>
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBVWaKrjvy3MaE7SQ74_uJiULgl1JY0H2s&sensor=false"></script>
  <script src="js/google-map.js"></script>
  <script src="js/main.js"></script>
    
  </body>
</html>