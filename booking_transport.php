<?php
include 'includes/config.php';

// Kontrollo nëse përdoruesi është i loguar
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Merr të dhënat e zonës nëse ka zone_id në URL
$zone = null;
if(isset($_GET['zone_id'])) {
    $zone_id = mysqli_real_escape_string($conn, $_GET['zone_id']);
    $sql = "SELECT * FROM transport_zones WHERE id = '$zone_id'";
    $result = mysqli_query($conn, $sql);
    if(mysqli_num_rows($result) > 0) {
        $zone = mysqli_fetch_assoc($result);
    }
}

// Proceso rezervimin e transportit
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['book_transport'])) {
    $zone_from = mysqli_real_escape_string($conn, $_POST['zone_from']);
    $zone_to = mysqli_real_escape_string($conn, $_POST['zone_to']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $vehicle_type = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $passengers = mysqli_real_escape_string($conn, $_POST['passengers']);
    $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);
    $booking_time = mysqli_real_escape_string($conn, $_POST['booking_time']);
    $flight_number = mysqli_real_escape_string($conn, $_POST['flight_number']);
    $special_requests = mysqli_real_escape_string($conn, $_POST['special_requests']);
    $user_id = $_SESSION['user_id'];
    
    // Shto rezervimin në databazë
    $sql = "INSERT INTO transport_bookings (user_id, zone_from, zone_to, price, vehicle_type, passengers, booking_date, booking_time, flight_number, special_requests) 
            VALUES ('$user_id', '$zone_from', '$zone_to', '$price', '$vehicle_type', '$passengers', '$booking_date', '$booking_time', '$flight_number', '$special_requests')";
    
    if(mysqli_query($conn, $sql)) {
        // Reset the form by redirecting to avoid resubmission
        header("Location: booking_transport.php?success=1&zone_id=" . ($zone ? $zone['id'] : ''));
        exit;
    } else {
        $error = "Error making booking. Please try again.";
    }
}

// Check for success redirect
if(isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "Transport booking successful! We'll contact you soon to confirm your transfer.";
    if(isset($_GET['zone_id'])) {
        $zone_id = mysqli_real_escape_string($conn, $_GET['zone_id']);
        $sql = "SELECT * FROM transport_zones WHERE id = '$zone_id'";
        $result = mysqli_query($conn, $sql);
        if(mysqli_num_rows($result) > 0) {
            $zone = mysqli_fetch_assoc($result);
        }
    }
}
?>

<?php include 'includes/header.php'; ?>

<style>
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
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
</style>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-12">
            <div class="text-center mb-4">
                <h1 class="text-dark fw-bold">Book Private Transfer</h1>
                <p class="text-muted">Secure your comfortable and reliable transfer service</p>
            </div>
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo $success; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    <div class="mt-2">
                        <a href="booking_transport.php" class="btn btn-sm btn-outline-success me-2">Book Another Transfer</a>
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
                <!-- Transfer Selection -->
                <div class="col-lg-6 mb-4">
                    <div class="card booking-card h-100">
                        <div class="card-header bg-success text-white">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-route me-2"></i>Transfer Details
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php if($zone): ?>
                                <div class="transfer-selection">
                                    <div class="d-flex align-items-start mb-3">
                                        <div class="bg-success text-white rounded p-3 me-3">
                                            <i class="fas fa-car fa-2x"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold text-success"><?php echo htmlspecialchars($zone['name']); ?></h6>
                                            <p class="text-muted small mb-1"><?php echo htmlspecialchars($zone['description']); ?></p>
                                            <div class="d-flex flex-wrap gap-3">
                                                <small class="text-success fw-bold">
                                                    <i class="fas fa-euro-sign me-1"></i>
                                                    €<?php echo $zone['price']; ?>
                                                </small>
                                                <small class="text-info">
                                                    <i class="fas fa-shuttle-van me-1"></i>
                                                    Mercedes Vito
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <a href="transport.php" class="btn btn-outline-success btn-sm">
                                            <i class="fas fa-undo me-1"></i>Change Route
                                        </a>
                                    </div>
                                </div>
                            <?php else: ?>
                                <p class="text-muted">Please select a transfer route:</p>
                                <div class="list-group">
                                    <a href="transport.php" class="list-group-item list-group-item-action border-0 rounded mb-2 text-center py-3">
                                        <i class="fas fa-route me-2"></i>Choose Transfer Route
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Sprinter Info -->
                            <div class="mt-4 p-3 bg-light rounded">
                                <h6 class="fw-bold mb-2">
                                    <i class="fas fa-bus me-2 text-warning"></i>Need a Sprinter?
                                </h6>
                                <p class="small text-muted mb-2">For groups larger than 6 people, contact us directly for Sprinter vehicle options and pricing.</p>
                                <div class="d-grid gap-2">
                                    <a href="tel:+1234567890" class="btn btn-warning btn-sm">
                                        <i class="fas fa-phone me-1"></i>Call for Sprinter
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Booking Form -->
                <div class="col-lg-6">
                    <?php if($zone): ?>
                        <div class="card booking-card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-calendar-check me-2"></i>Booking Details
                                </h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <input type="hidden" name="zone_from" value="<?php echo explode(' → ', $zone['name'])[0]; ?>">
                                    <input type="hidden" name="zone_to" value="<?php echo explode(' → ', $zone['name'])[1]; ?>">
                                    <input type="hidden" name="price" value="<?php echo $zone['price']; ?>">
                                    
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label for="vehicle_type" class="form-label fw-bold">
                                                <i class="fas fa-shuttle-van me-2"></i>Vehicle Type
                                            </label>
                                            <select class="form-select" id="vehicle_type" name="vehicle_type" required>
                                                <option value="vito">Mercedes Vito (up to 6 passengers)</option>
                                                <option value="sprinter">Mercedes Sprinter (contact for pricing)</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-12">
                                            <label for="passengers" class="form-label fw-bold">
                                                <i class="fas fa-users me-2"></i>Number of Passengers
                                            </label>
                                            <select class="form-select" id="passengers" name="passengers" required>
                                                <option value="1">1 Passenger</option>
                                                <option value="2">2 Passengers</option>
                                                <option value="3">3 Passengers</option>
                                                <option value="4">4 Passengers</option>
                                                <option value="5">5 Passengers</option>
                                                <option value="6">6 Passengers</option>
                                                <option value="7">7+ Passengers (Sprinter required)</option>
                                            </select>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="booking_date" class="form-label fw-bold">
                                                <i class="fas fa-calendar-day me-2"></i>Transfer Date
                                            </label>
                                            <input type="date" class="form-control" id="booking_date" name="booking_date" 
                                                   min="<?php echo date('Y-m-d'); ?>" required>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <label for="booking_time" class="form-label fw-bold">
                                                <i class="fas fa-clock me-2"></i>Transfer Time
                                            </label>
                                            <input type="time" class="form-control" id="booking_time" name="booking_time" required>
                                        </div>
                                        
                                        <div class="col-12">
                                            <label for="flight_number" class="form-label fw-bold">
                                                <i class="fas fa-plane me-2"></i>Flight Number (Optional)
                                            </label>
                                            <input type="text" class="form-control" id="flight_number" name="flight_number" 
                                                   placeholder="e.g., TK1234">
                                            <div class="form-text">Helpful for airport pickups</div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <label for="special_requests" class="form-label fw-bold">
                                                <i class="fas fa-comments me-2"></i>Special Requests
                                            </label>
                                            <textarea class="form-control" id="special_requests" name="special_requests" 
                                                      rows="3" placeholder="Any special requirements..."></textarea>
                                        </div>
                                        
                                        <div class="col-12">
                                            <div class="p-3 bg-light rounded">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <span class="fw-bold">Total Price:</span>
                                                    <span class="h5 text-success mb-0">
                                                        €<span id="total_price"><?php echo $zone['price']; ?></span>
                                                    </span>
                                                </div>
                                                <small class="text-muted">Price includes all taxes and fees</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-12">
                                            <button type="submit" name="book_transport" class="btn btn-success btn-lg w-100">
                                                <i class="fas fa-check-circle me-2"></i>Confirm Booking
                                            </button>
                                            
                                            <div class="text-center mt-3">
                                                <small class="text-muted">
                                                    <i class="fas fa-shield-alt me-1"></i>
                                                    Your booking is secure and protected
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="card booking-card">
                            <div class="card-body text-center py-5">
                                <i class="fas fa-route fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Select a Transfer Route First</h5>
                                <p class="text-muted">Please choose a transfer route to proceed with booking.</p>
                                <a href="transport.php" class="btn btn-success">
                                    <i class="fas fa-route me-2"></i>Choose Route
                                </a>
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

<!-- JavaScript for dynamic pricing -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const vehicleSelect = document.getElementById('vehicle_type');
    const passengersSelect = document.getElementById('passengers');
    
    function updatePassengerOptions() {
        if (vehicleSelect.value === 'sprinter') {
            // Disable passenger selection for sprinter
            passengersSelect.innerHTML = `
                <option value="7">7+ Passengers (Contact for pricing)</option>
            `;
            document.getElementById('total_price').textContent = 'Contact';
        } else {
            // Enable normal passenger selection for vito
            passengersSelect.innerHTML = `
                <option value="1">1 Passenger</option>
                <option value="2">2 Passengers</option>
                <option value="3">3 Passengers</option>
                <option value="4">4 Passengers</option>
                <option value="5">5 Passengers</option>
                <option value="6">6 Passengers</option>
                <option value="7">7+ Passengers (Sprinter required)</option>
            `;
            document.getElementById('total_price').textContent = '<?php echo $zone ? $zone['price'] : '0'; ?>';
        }
    }
    
    if (vehicleSelect) {
        vehicleSelect.addEventListener('change', updatePassengerOptions);
        updatePassengerOptions(); // Initialize on page load
    }
    
    // Date validation
    const bookingDate = document.getElementById('booking_date');
    if (bookingDate) {
        bookingDate.addEventListener('change', function() {
            const selectedDate = new Date(this.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                alert('Please select a future date.');
                this.value = '';
            }
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>