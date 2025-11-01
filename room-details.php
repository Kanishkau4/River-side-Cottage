<?php
require_once 'includes/db_connect.php';
require_once 'includes/booking_handler.php';

// Get room type ID from URL parameter
$room_type_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Get room details
$room = getRoomDetails($room_type_id);

if (!$room) {
    // Redirect to rooms page if room not found
    header('Location: rooms.html');
    exit;
}

// Process booking if form was submitted
$booking_success = false;
$booking_error = '';
$booking_id = '';
$total_price = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_room'])) {
    // Get form data
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $guests = $_POST['guests'] ?? '';
    $first_name = $_POST['first_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $special_requests = $_POST['special_requests'] ?? '';
    
    // Validate required fields
    if (!empty($check_in) && !empty($check_out) && !empty($guests) && 
        !empty($first_name) && !empty($last_name) && !empty($email) && !empty($phone)) {
        
        // For demo purposes, we'll just use the first available room of this type
        // In a real system, you would check availability properly
        try {
            $stmt = $pdo->prepare("SELECT id FROM rooms WHERE room_type_id = ? AND status = 'available' LIMIT 1");
            $stmt->execute([$room_type_id]);
            $room_result = $stmt->fetch();
            
            if ($room_result) {
                $room_id = $room_result['id'];
                
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
                $booking_error = "No available rooms of this type for the selected dates.";
            }
        } catch (Exception $e) {
            $booking_error = "Booking failed. Please try again later.";
        }
    } else {
        $booking_error = "Please fill in all required fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <title>River side Cottage - <?php echo htmlspecialchars($room['name']); ?></title>
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
  </head>
  <body>

    <nav class="navbar navbar-expand-lg navbar-dark ftco_navbar bg-dark ftco-navbar-light" id="ftco-navbar">
	    <div class="container">
	      <a class="navbar-brand" href="index.html">River<span>side</span></a>
	      <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#ftco-nav" aria-controls="ftco-nav" aria-expanded="false" aria-label="Toggle navigation">
	        <span class="oi oi-menu"></span> Menu
	      </button>

	      <div class="collapse navbar-collapse" id="ftco-nav">
	        <ul class="navbar-nav ml-auto">
	          <li class="nav-item"><a href="index.html" class="nav-link">Home</a></li>
	          <li class="nav-item active"><a href="rooms.html" class="nav-link">Our Rooms</a></li>
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
    <!-- END nav -->
		<div class="hero-wrap" style="background-image: url('images/bg_3.jpg');">
      <div class="overlay"></div>
      <div class="container">
        <div class="row no-gutters slider-text d-flex align-itemd-center justify-content-center">
          <div class="col-md-9 ftco-animate text-center d-flex align-items-end justify-content-center">
          	<div class="text">
	            <p class="breadcrumbs mb-2"><span class="mr-2"><a href="index.html">Home</a></span> <span class="mr-2"><a href="rooms.html">Rooms</a></span> <span><?php echo htmlspecialchars($room['name']); ?></span></p>
	            <h1 class="mb-4 bread"><?php echo htmlspecialchars($room['name']); ?></h1>
            </div>
          </div>
        </div>
      </div>
    </div>

    <section class="ftco-section">
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
          	<div class="row">
          		<div class="col-md-12 ftco-animate">
          			<div class="single-slider owl-carousel">
                    <?php foreach ($room['images'] as $image): ?>
          				<div class="item">
          					<div class="room-img" style="background-image: url(<?php echo htmlspecialchars($image['image_url']); ?>);"></div>
          				</div>
                    <?php endforeach; ?>
          			</div>
          		</div>
          		<div class="col-md-12 room-single mt-4 mb-5 ftco-animate">
          			<h2 class="mb-4"><?php echo htmlspecialchars($room['name']); ?></h2>
    						<p><?php echo htmlspecialchars($room['description']); ?></p>
    						<div class="d-md-flex mt-5 mb-5">
    							<ul class="list">
	    							<li><span>Max:</span> <?php echo $room['capacity']; ?> Persons</li>
	    							<li><span>Price:</span> $<?php echo number_format($room['price'], 2); ?> per night</li>
	    						</ul>
	    						<ul class="list ml-md-5">
	    							<li><span>Amenities:</span></li>
                                <?php 
                                $amenities = explode(',', $room['amenities']);
                                foreach ($amenities as $amenity): ?>
                                    <li><?php echo htmlspecialchars(trim($amenity)); ?></li>
                                <?php endforeach; ?>
	    						</ul>
    						</div>
    						<p><?php echo htmlspecialchars($room['description']); ?></p>
          		</div>
          	</div>
          </div> <!-- .col-md-8 -->
          
          <div class="col-lg-4 sidebar ftco-animate">
            <div class="sidebar-box ftco-animate">
              <h3>Book This Room</h3>
              
              <form action="book.php" method="GET" class="bg-light p-4">
                <input type="hidden" name="room_type" value="<?php echo $room_type_id; ?>">
                <div class="form-group">
                  <label for="check_in">Check-in Date *</label>
                  <input type="text" name="check_in" class="form-control checkin_date" placeholder="Check-in date" required>
                </div>
                <div class="form-group">
                  <label for="check_out">Check-out Date *</label>
                  <input type="text" name="check_out" class="form-control checkout_date" placeholder="Check-out date" required>
                </div>
                <div class="form-group">
                  <label for="guests">Number of Guests *</label>
                  <select name="guests" class="form-control" required>
                    <option value="">Select Number of Guests</option>
                    <?php for ($i = 1; $i <= $room['capacity']; $i++): ?>
                      <option value="<?php echo $i; ?>"><?php echo $i; ?> Guest<?php echo $i > 1 ? 's' : ''; ?></option>
                    <?php endfor; ?>
                  </select>
                </div>
                
                <div class="form-group">
                  <input type="submit" value="Check Availability & Book" class="btn btn-primary py-3 px-5">
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section> <!-- .section -->

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
	                <li><a href="#"><span class="icon icon-phone"></span><span class="text">0771313951 WhatsApp, 0771313951 normal, 0717999566 normal</span></a></li>
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