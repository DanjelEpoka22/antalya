<?php
include 'includes/config.php';

// Kontrollo nëse përdoruesi është i loguar
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Merr të dhënat e turit nëse ka tour_id në URL
$tour = null;
if(isset($_GET['tour_id'])) {
    $tour_id = mysqli_real_escape_string($conn, $_GET['tour_id']);
    $sql = "SELECT t.*, 
                   GROUP_CONCAT(ti.image_path ORDER BY ti.sort_order) as images 
            FROM tours t 
            LEFT JOIN tour_images ti ON t.id = ti.tour_id 
            WHERE t.id = '$tour_id' 
            GROUP BY t.id";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0) {
        $tour = mysqli_fetch_assoc($result);
        
        // Përpunimi i fotove
        $tour_images = [];
        if(!empty($tour['images'])) {
            $tour_images = explode(',', $tour['images']);
        }
        if(empty($tour_images) && $tour['image']) {
            $tour_images = [$tour['image']];
        }
        if(empty($tour_images)) {
            $tour_images = ['default.jpg'];
        }
        
        // Përpunimi i shërbimeve
        $included_services = !empty($tour['included']) ? explode(',', $tour['included']) : [];
        $excluded_services = !empty($tour['excluded']) ? explode(',', $tour['excluded']) : [];
    }
}

// Proceso rezervimin
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_tour'])) {
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $user_id = $_SESSION['user_id'];
    $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);
    $adults = mysqli_real_escape_string($conn, $_POST['adults']);
    $children = mysqli_real_escape_string($conn, $_POST['children']);
    $infants = mysqli_real_escape_string($conn, $_POST['infants']);
    
    // Merr çmimet e turit
    $sql = "SELECT price_adult, price_child, price_infant FROM tours WHERE id = '$tour_id'";
    $result = mysqli_query($conn, $sql);
    
    if($result && mysqli_num_rows($result) > 0) {
        $tour_data = mysqli_fetch_assoc($result);
        
        // Llogarit çmimin total
        $price_adult = $tour_data['price_adult'] ?? $tour_data['price'] ?? 0;
        $price_child = $tour_data['price_child'] ?? ($tour_data['price'] * 0.7) ?? 0;
        $price_infant = $tour_data['price_infant'] ?? 0;
        
        $total_adults = $price_adult * $adults;
        $total_children = $price_child * $children;
        $total_infants = $price_infant * $infants;
        $total_price = $total_adults + $total_children + $total_infants;
        $total_guests = $adults + $children + $infants;
        
        // Shto rezervimin në databazë
        $sql = "INSERT INTO bookings (user_id, tour_id, booking_date, guests, adults, children, infants, total_price) 
                VALUES ('$user_id', '$tour_id', '$booking_date', '$total_guests', '$adults', '$children', '$infants', '$total_price')";
        
        if(mysqli_query($conn, $sql)) {
            // Reset the form by redirecting to avoid resubmission
            header("Location: booking.php?success=1&tour_id=" . $tour_id);
            exit;
        } else {
            $error = "Error making booking. Please try again.";
        }
    } else {
        $error = "Tour not found!";
    }
}

// Check for success redirect
if(isset($_GET['success']) && $_GET['success'] == 1 && isset($_GET['tour_id'])) {
    $success = "Booking successful! We'll contact you soon to confirm your tour.";
    $tour_id = mysqli_real_escape_string($conn, $_GET['tour_id']);
    $sql = "SELECT * FROM tours WHERE id = '$tour_id'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0) {
        $tour = mysqli_fetch_assoc($result);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Tour - Rreze Antalya</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #2c3e50;
            --secondary: #e74c3c;
            --accent: #3498db;
            --light: #ecf0f1;
            --dark: #2c3e50;
        }
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .booking-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            border-bottom: none;
            padding: 1.5rem;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
        
        .nav-brand {
            color: white;
            text-decoration: none;
            font-weight: 700;
            font-size: 1.5rem;
        }
        
        .price-breakdown {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin: 10px 0;
        }
        
        .price-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e9ecef;
        }
        
        .price-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .price-total {
            background: #e8f5e8;
            border-left: 4px solid #28a745;
        }
        
        .guest-counter {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .counter-btn {
            width: 35px;
            height: 35px;
            border: 1px solid #dee2e6;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .counter-btn:hover {
            background: #f8f9fa;
            border-color: #007bff;
        }
        
        .counter-input {
            width: 60px;
            text-align: center;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 5px;
        }
        
        .image-slider {
            position: relative;
            height: 300px;
            overflow: hidden;
            border-radius: 15px 15px 0 0;
        }
        
        .slider-container {
            position: relative;
            height: 100%;
        }
        
        .slider-images {
            display: flex;
            transition: transform 0.5s ease;
            height: 100%;
        }
        
        .slider-image {
            min-width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .slider-nav {
            position: absolute;
            bottom: 10px;
            left: 0;
            right: 0;
            display: flex;
            justify-content: center;
            gap: 5px;
        }
        
        .slider-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: rgba(255,255,255,0.5);
            cursor: pointer;
            transition: background 0.3s ease;
        }
        
        .slider-dot.active {
            background: white;
        }
        
        .services-list {
            font-size: 0.9rem;
        }
        
        .services-list li {
            margin-bottom: 5px;
        }
        
        .included-services {
            color: #28a745;
        }
        
        .excluded-services {
            color: #dc3545;
        }
    </style>
</head>
<body>
    <!-- Simple Navigation -->
    <nav class="navbar navbar-dark">
        <div class="container">
            <a class="nav-brand" href="index.php">
                <i class="fas fa-sun me-2"></i>Rreze Antalya
            </a>
            <div class="navbar-nav ms-auto">
                <a href="tours.php" class="text-white text-decoration-none me-3">
                    <i class="fas fa-compass me-1"></i>All Tours
                </a>
                <a href="index.php" class="text-white text-decoration-none">
                    <i class="fas fa-home me-1"></i>Home
                </a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="text-center mb-4">
                    <h1 class="text-white fw-bold">Book Your Tour</h1>
                    <p class="text-white-50">Secure your spot for an unforgettable experience</p>
                </div>
                
                <?php if(isset($success)): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i>
                        <?php echo $success; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        <div class="mt-2">
                            <a href="booking.php" class="btn btn-sm btn-outline-success me-2">Book Another Tour</a>
                            <a href="my_bookings.php" class="btn btn-sm btn-success">View My Bookings</a>
                        </div>
                    </div>
                <?php endif; ?>
                
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <!-- Tour Selection & Details -->
                    <div class="col-lg-6 mb-4">
                        <div class="card booking-card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-map-marked-alt me-2"></i>Tour Details
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if($tour): ?>
                                    <!-- Image Slider -->
                                    <?php if(!empty($tour_images)): ?>
                                    <div class="image-slider mb-4">
                                        <div class="slider-container" id="slider-tour">
                                            <div class="slider-images">
                                                <?php foreach($tour_images as $index => $image): ?>
                                                    <img src="assets/images/tours/<?php echo trim($image); ?>" 
                                                         class="slider-image" 
                                                         alt="<?php echo htmlspecialchars($tour['title']); ?> - Image <?php echo $index + 1; ?>">
                                                <?php endforeach; ?>
                                            </div>
                                            <?php if(count($tour_images) > 1): ?>
                                                <div class="slider-nav">
                                                    <?php foreach($tour_images as $index => $image): ?>
                                                        <div class="slider-dot <?php echo $index === 0 ? 'active' : ''; ?>" 
                                                             onclick="goToSlide(<?php echo $index; ?>)"></div>
                                                    <?php endforeach; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="tour-selection">
                                        <h4 class="text-primary mb-3"><?php echo htmlspecialchars($tour['title']); ?></h4>
                                        <p class="text-muted mb-3"><?php echo htmlspecialchars($tour['description']); ?></p>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <i class="fas fa-clock me-1"></i>
                                                    <?php echo htmlspecialchars($tour['duration']); ?>
                                                </small>
                                            </div>
                                            <div class="col-md-6">
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    <?php echo htmlspecialchars($tour['location']); ?>
                                                </small>
                                            </div>
                                        </div>

                                        <!-- Price Breakdown -->
                                        <div class="price-breakdown">
                                            <h6 class="mb-3">Price Breakdown:</h6>
                                            <div class="price-item">
                                                <span>Adult (12+ years):</span>
                                                <strong>$<?php echo $tour['price_adult'] ?? $tour['price']; ?></strong>
                                            </div>
                                            <div class="price-item">
                                                <span>Child (4-11 years):</span>
                                                <strong>$<?php echo $tour['price_child'] ?? round($tour['price'] * 0.7, 2); ?></strong>
                                            </div>
                                            <div class="price-item">
                                                <span>Infant (0-3 years):</span>
                                                <strong>$<?php echo $tour['price_infant'] ?? '0'; ?></strong>
                                            </div>
                                        </div>

                                        <!-- Services -->
                                        <div class="services-list">
                                            <?php if(!empty($included_services)): ?>
                                                <h6 class="text-success mb-2"><i class="fas fa-check me-1"></i>Included:</h6>
                                                <ul class="included-services">
                                                    <?php foreach(array_slice($included_services, 0, 3) as $service): ?>
                                                        <li><?php echo htmlspecialchars(trim($service)); ?></li>
                                                    <?php endforeach; ?>
                                                    <?php if(count($included_services) > 3): ?>
                                                        <li>... and more</li>
                                                    <?php endif; ?>
                                                </ul>
                                            <?php endif; ?>
                                            
                                            <?php if(!empty($excluded_services)): ?>
                                                <h6 class="text-danger mb-2 mt-3"><i class="fas fa-times me-1"></i>Not Included:</h6>
                                                <ul class="excluded-services">
                                                    <?php foreach(array_slice($excluded_services, 0, 3) as $service): ?>
                                                        <li><?php echo htmlspecialchars(trim($service)); ?></li>
                                                    <?php endforeach; ?>
                                                    <?php if(count($excluded_services) > 3): ?>
                                                        <li>... and more</li>
                                                    <?php endif; ?>
                                                </ul>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <p class="text-muted">Please select a tour to book:</p>
                                    <div class="list-group">
                                        <?php
                                        $sql = "SELECT * FROM tours ORDER BY title";
                                        $result = mysqli_query($conn, $sql);
                                        
                                        if(mysqli_num_rows($result) > 0) {
                                            while($row = mysqli_fetch_assoc($result)) {
                                                echo '<a href="booking.php?tour_id=' . $row['id'] . '" class="list-group-item list-group-item-action border-0 rounded mb-2">';
                                                echo '<div class="d-flex justify-content-between align-items-center">';
                                                echo '<div>';
                                                echo '<h6 class="mb-1 fw-bold">' . htmlspecialchars($row['title']) . '</h6>';
                                                echo '<small class="text-muted">' . htmlspecialchars($row['location']) . ' • ' . htmlspecialchars($row['duration']) . '</small>';
                                                echo '</div>';
                                                echo '<span class="badge bg-primary">$' . ($row['price_adult'] ?? $row['price']) . '</span>';
                                                echo '</div>';
                                                echo '</a>';
                                            }
                                        } else {
                                            echo '<div class="text-center py-4">';
                                            echo '<i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>';
                                            echo '<p class="text-muted">No tours available at the moment.</p>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Booking Form -->
                    <div class="col-lg-6">
                        <?php if($tour): ?>
                            <div class="card booking-card h-100">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0">
                                        <i class="fas fa-calendar-check me-2"></i>Booking Details
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" id="bookingForm">
                                        <input type="hidden" name="tour_id" value="<?php echo $tour['id']; ?>">
                                        
                                        <div class="mb-4">
                                            <label for="booking_date" class="form-label fw-bold">
                                                <i class="fas fa-calendar-day me-2"></i>Preferred Date
                                            </label>
                                            <input type="date" class="form-control" id="booking_date" name="booking_date" 
                                                   min="<?php echo date('Y-m-d'); ?>" required>
                                            <div class="form-text">Select your preferred tour date</div>
                                        </div>
                                        
                                        <!-- Guests Selection -->
                                        <div class="mb-4">
                                            <label class="form-label fw-bold">
                                                <i class="fas fa-users me-2"></i>Number of Guests
                                            </label>
                                            
                                            <!-- Adults -->
                                            <div class="row align-items-center mb-3">
                                                <div class="col-6">
                                                    <label class="form-label">Adults (12+ years)</label>
                                                    <small class="form-text text-muted d-block">$<?php echo $tour['price_adult'] ?? $tour['price']; ?> per person</small>
                                                </div>
                                                <div class="col-6">
                                                    <div class="guest-counter">
                                                        <button type="button" class="counter-btn" onclick="updateGuests('adults', -1)">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number" class="counter-input" id="adults" name="adults" 
                                                               value="1" min="1" max="20" readonly>
                                                        <button type="button" class="counter-btn" onclick="updateGuests('adults', 1)">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Children -->
                                            <div class="row align-items-center mb-3">
                                                <div class="col-6">
                                                    <label class="form-label">Children (4-11 years)</label>
                                                    <small class="form-text text-muted d-block">$<?php echo $tour['price_child'] ?? round($tour['price'] * 0.7, 2); ?> per person</small>
                                                </div>
                                                <div class="col-6">
                                                    <div class="guest-counter">
                                                        <button type="button" class="counter-btn" onclick="updateGuests('children', -1)">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number" class="counter-input" id="children" name="children" 
                                                               value="0" min="0" max="20" readonly>
                                                        <button type="button" class="counter-btn" onclick="updateGuests('children', 1)">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Infants -->
                                            <div class="row align-items-center mb-3">
                                                <div class="col-6">
                                                    <label class="form-label">Infants (0-3 years)</label>
                                                    <small class="form-text text-muted d-block">$<?php echo $tour['price_infant'] ?? '0'; ?> per person</small>
                                                </div>
                                                <div class="col-6">
                                                    <div class="guest-counter">
                                                        <button type="button" class="counter-btn" onclick="updateGuests('infants', -1)">
                                                            <i class="fas fa-minus"></i>
                                                        </button>
                                                        <input type="number" class="counter-input" id="infants" name="infants" 
                                                               value="0" min="0" max="20" readonly>
                                                        <button type="button" class="counter-btn" onclick="updateGuests('infants', 1)">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Price Summary -->
                                        <div class="mb-4 p-3 bg-light rounded">
                                            <h6 class="mb-3">Price Summary:</h6>
                                            <div class="price-item">
                                                <span>Adults (<span id="adults-count">1</span>x):</span>
                                                <span>$<span id="adults-total"><?php echo $tour['price_adult'] ?? $tour['price']; ?></span></span>
                                            </div>
                                            <div class="price-item">
                                                <span>Children (<span id="children-count">0</span>x):</span>
                                                <span>$<span id="children-total">0.00</span></span>
                                            </div>
                                            <div class="price-item">
                                                <span>Infants (<span id="infants-count">0</span>x):</span>
                                                <span>$<span id="infants-total">0.00</span></span>
                                            </div>
                                            <div class="price-item price-total mt-2 pt-2">
                                                <span class="fw-bold">Total Price:</span>
                                                <span class="h5 text-success mb-0">$<span id="total-price"><?php echo $tour['price_adult'] ?? $tour['price']; ?></span></span>
                                            </div>
                                            <small class="text-muted d-block mt-2">Price includes all taxes and fees</small>
                                        </div>
                                        
                                        <button type="submit" name="book_tour" class="btn btn-success btn-lg w-100">
                                            <i class="fas fa-check-circle me-2"></i>Confirm Booking
                                        </button>
                                        
                                        <div class="text-center mt-3">
                                            <small class="text-muted">
                                                <i class="fas fa-shield-alt me-1"></i>
                                                Your booking is secure and protected
                                            </small>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="card booking-card">
                                <div class="card-body text-center py-5">
                                    <i class="fas fa-compass fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Select a Tour First</h5>
                                    <p class="text-muted">Please choose a tour from the list to proceed with booking.</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript for Booking System -->
    <script>
        // Prices from PHP
        const priceAdult = <?php echo $tour ? ($tour['price_adult'] ?? $tour['price']) : 0; ?>;
        const priceChild = <?php echo $tour ? ($tour['price_child'] ?? round($tour['price'] * 0.7, 2)) : 0; ?>;
        const priceInfant = <?php echo $tour ? ($tour['price_infant'] ?? 0) : 0; ?>;
        
        // Update guests counter
        function updateGuests(type, change) {
            const input = document.getElementById(type);
            let value = parseInt(input.value) + change;
            
            // Set limits
            if (value < 0) value = 0;
            if (value > 20) value = 20;
            
            input.value = value;
            calculateTotal();
        }
        
        // Calculate total price
        function calculateTotal() {
            const adults = parseInt(document.getElementById('adults').value);
            const children = parseInt(document.getElementById('children').value);
            const infants = parseInt(document.getElementById('infants').value);
            
            const adultsTotal = adults * priceAdult;
            const childrenTotal = children * priceChild;
            const infantsTotal = infants * priceInfant;
            const totalPrice = adultsTotal + childrenTotal + infantsTotal;
            
            // Update display
            document.getElementById('adults-count').textContent = adults;
            document.getElementById('children-count').textContent = children;
            document.getElementById('infants-count').textContent = infants;
            
            document.getElementById('adults-total').textContent = adultsTotal.toFixed(2);
            document.getElementById('children-total').textContent = childrenTotal.toFixed(2);
            document.getElementById('infants-total').textContent = infantsTotal.toFixed(2);
            document.getElementById('total-price').textContent = totalPrice.toFixed(2);
        }
        
        // Image slider functionality
        let currentSlide = 0;
        const slides = document.querySelectorAll('#slider-tour .slider-image');
        const dots = document.querySelectorAll('#slider-tour .slider-dot');
        
        function goToSlide(slideIndex) {
            currentSlide = slideIndex;
            updateSlider();
        }
        
        function updateSlider() {
            const slider = document.querySelector('#slider-tour .slider-images');
            slider.style.transform = `translateX(-${currentSlide * 100}%)`;
            
            dots.forEach((dot, index) => {
                dot.classList.toggle('active', index === currentSlide);
            });
        }
        
        // Auto-advance slides every 5 seconds if multiple images
        <?php if($tour && count($tour_images) > 1): ?>
        setInterval(() => {
            currentSlide = (currentSlide + 1) % slides.length;
            updateSlider();
        }, 5000);
        <?php endif; ?>
        
        // Date validation
        document.getElementById('booking_date')?.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                alert('Please select a future date.');
                this.value = '';
            }
        });
        
        // Initialize calculations
        document.addEventListener('DOMContentLoaded', function() {
            calculateTotal();
        });
    </script>
</body>
</html>