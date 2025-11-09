<?php
include 'includes/config.php';

// Kontrollo nëse përdoruesi është i loguar
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Merr rezervimet e turneve të përdoruesit me të dhënat e reja
$user_id = $_SESSION['user_id'];
$tours_sql = "SELECT b.*, t.title, t.location, t.duration, t.image, 
                     t.price_adult, t.price_child, t.price_infant,
                     t.included, t.excluded
              FROM bookings b 
              JOIN tours t ON b.tour_id = t.id 
              WHERE b.user_id = '$user_id' 
              ORDER BY b.created_at DESC";
$tours_result = mysqli_query($conn, $tours_sql);

// Merr rezervimet e transportit të përdoruesit
$transport_sql = "SELECT * FROM transport_bookings 
                  WHERE user_id = '$user_id' 
                  ORDER BY created_at DESC";
$transport_result = mysqli_query($conn, $transport_sql);
?>

<?php include 'includes/header.php'; ?>

<style>
    .booking-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
    }
    
    .tour-booking {
        border-left: 4px solid #3498db;
    }
    
    .transport-booking {
        border-left: 4px solid #28a745;
    }
    
    .booking-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }
    
    .status-badge {
        font-size: 0.75rem;
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 600;
    }
    
    .booking-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
    }
    
    .info-item {
        display: flex;
        align-items: center;
        margin-bottom: 8px;
        padding: 8px 0;
    }
    
    .empty-state {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 20px;
        padding: 60px 20px;
        color: white;
    }
    
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.9;
    }
    
    .booking-type-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 0.7rem;
    }
    
    .guest-breakdown {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 12px;
        margin: 10px 0;
    }
    
    .guest-item {
        display: flex;
        justify-content: space-between;
        font-size: 0.85rem;
        margin-bottom: 5px;
    }
    
    .guest-item:last-child {
        margin-bottom: 0;
    }
    
    .services-badge {
        font-size: 0.7rem;
        margin-right: 5px;
        margin-bottom: 5px;
    }
    
    .price-details {
        background: #e8f5e8;
        border-radius: 8px;
        padding: 10px;
        margin-top: 10px;
    }
    
    .price-line {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        margin-bottom: 3px;
    }
</style>

<div class="container mt-5 pt-4">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fw-bold text-dark mb-2">My Bookings</h1>
                    <p class="text-muted mb-0">Manage and view all your tour and transfer reservations</p>
                </div>
                <div class="d-flex gap-2">
                    <a href="tours.php" class="btn btn-primary px-4 py-2">
                        <i class="fas fa-compass me-2"></i>Book Tour
                    </a>
                    <a href="transport.php" class="btn btn-success px-4 py-2">
                        <i class="fas fa-car me-2"></i>Book Transfer
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php 
    $total_tour_bookings = $tours_result ? mysqli_num_rows($tours_result) : 0;
    $total_transport_bookings = $transport_result ? mysqli_num_rows($transport_result) : 0;
    $total_bookings = $total_tour_bookings + $total_transport_bookings;
    ?>

    <?php if($total_bookings > 0): ?>
        <!-- Bookings Grid -->
        <div class="row">
            <!-- Tour Bookings -->
            <?php if($total_tour_bookings > 0): ?>
                <?php while($booking = mysqli_fetch_assoc($tours_result)): 
                    $status_class = [
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger'
                    ][$booking['status']] ?? 'secondary';
                    
                    $status_icon = [
                        'pending' => 'fas fa-clock',
                        'confirmed' => 'fas fa-check-circle',
                        'cancelled' => 'fas fa-times-circle'
                    ][$booking['status']] ?? 'fas fa-info-circle';
                    
                    // Përpunimi i shërbimeve
                    $included_services = !empty($booking['included']) ? explode(',', $booking['included']) : [];
                    $excluded_services = !empty($booking['excluded']) ? explode(',', $booking['excluded']) : [];
                    
                    // Llogaritja e çmimeve individuale
                    $adults_count = $booking['adults'] ?? 1;
                    $children_count = $booking['children'] ?? 0;
                    $infants_count = $booking['infants'] ?? 0;
                    
                    $price_adult = $booking['price_adult'] ?? $booking['total_price'] / $booking['guests'];
                    $price_child = $booking['price_child'] ?? ($booking['price_adult'] ?? $booking['total_price'] / $booking['guests']) * 0.7;
                    $price_infant = $booking['price_infant'] ?? 0;
                ?>
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card booking-card tour-booking h-100">
                        <div class="card-body p-4 position-relative">
                            <!-- Booking Type Badge -->
                            <span class="badge bg-primary booking-type-badge">
                                <i class="fas fa-map-marked-alt me-1"></i>TOUR
                            </span>
                            
                            <!-- Header with title and status -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title fw-bold text-dark mb-0"><?php echo htmlspecialchars($booking['title']); ?></h5>
                                <span class="badge status-badge bg-<?php echo $status_class; ?>">
                                    <i class="<?php echo $status_icon; ?> me-1"></i>
                                    <?php echo ucfirst($booking['status']); ?>
                                </span>
                            </div>
                            
                            <!-- Location -->
                            <p class="text-muted mb-3">
                                <i class="fas fa-map-marker-alt text-primary me-2"></i>
                                <?php echo htmlspecialchars($booking['location']); ?>
                            </p>

                            <!-- Guest Breakdown -->
                            <div class="guest-breakdown">
                                <div class="guest-item">
                                    <span>Adults (12+):</span>
                                    <strong><?php echo $adults_count; ?> x $<?php echo number_format($price_adult, 2); ?></strong>
                                </div>
                                <?php if($children_count > 0): ?>
                                <div class="guest-item">
                                    <span>Children (4-11):</span>
                                    <strong><?php echo $children_count; ?> x $<?php echo number_format($price_child, 2); ?></strong>
                                </div>
                                <?php endif; ?>
                                <?php if($infants_count > 0): ?>
                                <div class="guest-item">
                                    <span>Infants (0-3):</span>
                                    <strong><?php echo $infants_count; ?> x $<?php echo number_format($price_infant, 2); ?></strong>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Booking Details -->
                            <div class="booking-details">
                                <div class="info-item">
                                    <div class="booking-icon bg-primary text-white">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Booking Date</small>
                                        <strong><?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></strong>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="booking-icon bg-info text-white">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Total Guests</small>
                                        <strong><?php echo $booking['guests']; ?> Person(s)</strong>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="booking-icon bg-warning text-white">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Duration</small>
                                        <strong><?php echo htmlspecialchars($booking['duration']); ?></strong>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="booking-icon bg-success text-white">
                                        <i class="fas fa-dollar-sign"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Total Price</small>
                                        <strong class="text-success">$<?php echo number_format($booking['total_price'], 2); ?></strong>
                                    </div>
                                </div>
                            </div>

                            <!-- Services Included -->
                            <?php if(!empty($included_services) && count($included_services) > 0): ?>
                            <div class="mt-3">
                                <small class="text-muted d-block mb-2">Included Services:</small>
                                <div class="d-flex flex-wrap">
                                    <?php foreach(array_slice($included_services, 0, 3) as $service): ?>
                                        <span class="badge bg-success services-badge">
                                            <i class="fas fa-check me-1"></i><?php echo htmlspecialchars(trim($service)); ?>
                                        </span>
                                    <?php endforeach; ?>
                                    <?php if(count($included_services) > 3): ?>
                                        <span class="badge bg-secondary services-badge">+<?php echo count($included_services) - 3; ?> more</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Footer -->
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <div class="border-top pt-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    Booked on <?php echo date('M j, Y g:i A', strtotime($booking['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>

            <!-- Transport Bookings -->
            <?php if($total_transport_bookings > 0): ?>
                <?php while($transport = mysqli_fetch_assoc($transport_result)): 
                    $status_class = [
                        'pending' => 'warning',
                        'confirmed' => 'success',
                        'cancelled' => 'danger'
                    ][$transport['status']] ?? 'secondary';
                    
                    $status_icon = [
                        'pending' => 'fas fa-clock',
                        'confirmed' => 'fas fa-check-circle',
                        'cancelled' => 'fas fa-times-circle'
                    ][$transport['status']] ?? 'fas fa-info-circle';
                    
                    $vehicle_icon = [
                        'vito' => 'fas fa-shuttle-van',
                        'sprinter' => 'fas fa-bus'
                    ][$transport['vehicle_type']] ?? 'fas fa-car';
                ?>
                <div class="col-lg-6 col-xl-4 mb-4">
                    <div class="card booking-card transport-booking h-100">
                        <div class="card-body p-4 position-relative">
                            <!-- Booking Type Badge -->
                            <span class="badge bg-success booking-type-badge">
                                <i class="fas fa-car me-1"></i>TRANSFER
                            </span>
                            
                            <!-- Header with route and status -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <h5 class="card-title fw-bold text-dark mb-0">Private Transfer</h5>
                                <span class="badge status-badge bg-<?php echo $status_class; ?>">
                                    <i class="<?php echo $status_icon; ?> me-1"></i>
                                    <?php echo ucfirst($transport['status']); ?>
                                </span>
                            </div>
                            
                            <!-- Route -->
                            <p class="text-muted mb-4">
                                <i class="fas fa-route text-success me-2"></i>
                                <?php echo htmlspecialchars($transport['zone_from']); ?> → <?php echo htmlspecialchars($transport['zone_to']); ?>
                            </p>

                            <!-- Booking Details -->
                            <div class="booking-details">
                                <div class="info-item">
                                    <div class="booking-icon bg-success text-white">
                                        <i class="fas fa-calendar-day"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Transfer Date</small>
                                        <strong><?php echo date('F j, Y', strtotime($transport['booking_date'])); ?></strong>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="booking-icon bg-info text-white">
                                        <i class="fas fa-clock"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Time</small>
                                        <strong><?php echo date('g:i A', strtotime($transport['booking_time'])); ?></strong>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="booking-icon bg-warning text-white">
                                        <i class="<?php echo $vehicle_icon; ?>"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Vehicle & Passengers</small>
                                        <strong><?php echo ucfirst($transport['vehicle_type']); ?> (<?php echo $transport['passengers']; ?> pax)</strong>
                                    </div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="booking-icon bg-primary text-white">
                                        <i class="fas fa-euro-sign"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Total Price</small>
                                        <strong class="text-success">€<?php echo $transport['price']; ?></strong>
                                    </div>
                                </div>
                                
                                <?php if($transport['flight_number']): ?>
                                <div class="info-item">
                                    <div class="booking-icon bg-secondary text-white">
                                        <i class="fas fa-plane"></i>
                                    </div>
                                    <div>
                                        <small class="text-muted d-block">Flight Number</small>
                                        <strong><?php echo htmlspecialchars($transport['flight_number']); ?></strong>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class="card-footer bg-transparent border-0 pt-0">
                            <div class="border-top pt-3">
                                <small class="text-muted">
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    Booked on <?php echo date('M j, Y g:i A', strtotime($transport['created_at'])); ?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="empty-state text-center">
                    <i class="fas fa-calendar-plus"></i>
                    <h3 class="mb-3">No Bookings Yet</h3>
                    <p class="mb-4 opacity-75">You haven't made any tour or transfer bookings yet. Start exploring our services!</p>
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <a href="tours.php" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-compass me-2"></i>Explore Tours
                        </a>
                        <a href="transport.php" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-car me-2"></i>Book Transfer
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>