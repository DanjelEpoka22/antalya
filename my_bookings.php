<?php
include 'includes/config.php';

// Kontrollo nëse përdoruesi është i loguar
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Merr rezervimet e përdoruesit
$user_id = $_SESSION['user_id'];
$sql = "SELECT b.*, t.title, t.location, t.duration, t.image 
        FROM bookings b 
        JOIN tours t ON b.tour_id = t.id 
        WHERE b.user_id = '$user_id' 
        ORDER BY b.created_at DESC";
$bookings_result = mysqli_query($conn, $sql);
?>

<?php include 'includes/header.php'; ?>

<style>
    .booking-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border: none;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        border-left: 4px solid #3498db;
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
</style>

<div class="container mt-5 pt-4">
    <!-- Header Section -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="fw-bold text-dark mb-2">My Bookings</h1>
                    <p class="text-muted mb-0">Manage and view all your tour reservations</p>
                </div>
                <a href="tours.php" class="btn btn-primary px-4 py-2">
                    <i class="fas fa-plus me-2"></i>Book New Tour
                </a>
            </div>
        </div>
    </div>

    <?php if(mysqli_num_rows($bookings_result) > 0): ?>
        <!-- Bookings Grid -->
        <div class="row">
            <?php while($booking = mysqli_fetch_assoc($bookings_result)): 
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
            ?>
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card booking-card h-100">
                    <div class="card-body p-4">
                        <!-- Header with title and status -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <h5 class="card-title fw-bold text-dark mb-0"><?php echo htmlspecialchars($booking['title']); ?></h5>
                            <span class="badge status-badge bg-<?php echo $status_class; ?>">
                                <i class="<?php echo $status_icon; ?> me-1"></i>
                                <?php echo ucfirst($booking['status']); ?>
                            </span>
                        </div>
                        
                        <!-- Location -->
                        <p class="text-muted mb-4">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            <?php echo htmlspecialchars($booking['location']); ?>
                        </p>

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
                                    <small class="text-muted d-block">Guests</small>
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
                                    <strong class="text-success">$<?php echo $booking['total_price']; ?></strong>
                                </div>
                            </div>
                        </div>
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
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="empty-state text-center">
                    <i class="fas fa-calendar-plus"></i>
                    <h3 class="mb-3">No Bookings Yet</h3>
                    <p class="mb-4 opacity-75">You haven't made any tour bookings yet. Start exploring our amazing tours!</p>
                    <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                        <a href="tours.php" class="btn btn-light btn-lg px-4">
                            <i class="fas fa-compass me-2"></i>Explore Tours
                        </a>
                        <a href="index.php" class="btn btn-outline-light btn-lg px-4">
                            <i class="fas fa-home me-2"></i>Back to Home
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>