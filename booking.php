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
    $sql = "SELECT * FROM tours WHERE id = '$tour_id'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0) {
        $tour = mysqli_fetch_assoc($result);
    }
}

// Proceso rezervimin
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_tour'])) {
    $tour_id = mysqli_real_escape_string($conn, $_POST['tour_id']);
    $user_id = $_SESSION['user_id'];
    $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);
    $guests = mysqli_real_escape_string($conn, $_POST['guests']);
    
    // Merr çmimin e turit
    $sql = "SELECT price FROM tours WHERE id = '$tour_id'";
    $result = mysqli_query($conn, $sql);
    
    if($result && mysqli_num_rows($result) > 0) {
        $tour_data = mysqli_fetch_assoc($result);
        $tour_price = $tour_data['price'];
        
        // Llogarit çmimin total
        $total_price = $tour_price * $guests;
        
        // Shto rezervimin në databazë
        $sql = "INSERT INTO bookings (user_id, tour_id, booking_date, guests, total_price) 
                VALUES ('$user_id', '$tour_id', '$booking_date', '$guests', '$total_price')";
        
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
                <a href="index.php" class="text-white text-decoration-none me-3">
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
                    <!-- Tour Selection -->
                    <div class="col-lg-6 mb-4">
                        <div class="card booking-card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-map-marked-alt me-2"></i>Select a Tour
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if($tour): ?>
                                    <div class="tour-selection">
                                        <div class="d-flex align-items-start mb-3">
                                            <?php if($tour['image']): ?>
                                                <img src="assets/images/tours/<?php echo $tour['image']; ?>" 
                                                     class="rounded me-3" 
                                                     style="width: 80px; height: 80px; object-fit: cover;" 
                                                     alt="<?php echo htmlspecialchars($tour['title']); ?>">
                                            <?php endif; ?>
                                            <div>
                                                <h6 class="fw-bold text-primary"><?php echo htmlspecialchars($tour['title']); ?></h6>
                                                <p class="text-muted small mb-1"><?php echo htmlspecialchars($tour['description']); ?></p>
                                                <div class="d-flex flex-wrap gap-3">
                                                    <small class="text-success">
                                                        <i class="fas fa-dollar-sign me-1"></i>
                                                        <strong>$<?php echo $tour['price']; ?></strong> per person
                                                    </small>
                                                    <small class="text-info">
                                                        <i class="fas fa-clock me-1"></i>
                                                        <?php echo htmlspecialchars($tour['duration']); ?>
                                                    </small>
                                                    <small class="text-warning">
                                                        <i class="fas fa-map-marker-alt me-1"></i>
                                                        <?php echo htmlspecialchars($tour['location']); ?>
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <a href="booking.php" class="btn btn-outline-primary btn-sm">
                                                <i class="fas fa-undo me-1"></i>Change Tour
                                            </a>
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
                                                echo '<span class="badge bg-primary">$' . $row['price'] . '</span>';
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
                                    <form method="POST">
                                        <input type="hidden" name="tour_id" value="<?php echo $tour['id']; ?>">
                                        
                                        <div class="mb-3">
                                            <label for="booking_date" class="form-label fw-bold">
                                                <i class="fas fa-calendar-day me-2"></i>Preferred Date
                                            </label>
                                            <input type="date" class="form-control" id="booking_date" name="booking_date" 
                                                   min="<?php echo date('Y-m-d'); ?>" required>
                                            <div class="form-text">Select your preferred tour date</div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="guests" class="form-label fw-bold">
                                                <i class="fas fa-users me-2"></i>Number of Guests
                                            </label>
                                            <select class="form-select" id="guests" name="guests" required>
                                                <option value="1">1 Guest</option>
                                                <option value="2">2 Guests</option>
                                                <option value="3">3 Guests</option>
                                                <option value="4">4 Guests</option>
                                                <option value="5">5 Guests</option>
                                                <option value="6">6+ Guests (Contact us for larger groups)</option>
                                            </select>
                                        </div>
                                        
                                        <div class="mb-4 p-3 bg-light rounded">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold">Total Price:</span>
                                                <span class="h5 text-success mb-0">
                                                    $<span id="total_price"><?php echo $tour['price']; ?></span>
                                                </span>
                                            </div>
                                            <small class="text-muted">Price includes all taxes and fees</small>
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
    
    <!-- Simple JavaScript without conflicts -->
    <script>
    // Llogarit çmimin total kur ndryshon numri i të ftuarve
    document.getElementById('guests')?.addEventListener('change', function() {
        const guests = parseInt(this.value);
        const pricePerPerson = <?php echo $tour ? $tour['price'] : 0; ?>;
        const totalPrice = (guests * pricePerPerson).toFixed(2);
        document.getElementById('total_price').textContent = totalPrice;
    });

    // Validimi i datës
    document.getElementById('booking_date')?.addEventListener('change', function() {
        const selectedDate = new Date(this.value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        
        if (selectedDate < today) {
            alert('Please select a future date.');
            this.value = '';
        }
    });

    // Initialize with correct price
    document.addEventListener('DOMContentLoaded', function() {
        const guests = document.getElementById('guests');
        if (guests) {
            guests.dispatchEvent(new Event('change'));
        }
    });
    </script>
</body>
</html>