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

<div class="container mt-4" style="margin-top: 100px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="fw-bold">My Bookings</h1>
        <a href="tours.php" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Book New Tour
        </a>
    </div>

    <?php if(mysqli_num_rows($bookings_result) > 0): ?>
        <div class="row">
            <?php while($booking = mysqli_fetch_assoc($bookings_result)): 
                $status_class = [
                    'pending' => 'warning',
                    'confirmed' => 'success',
                    'cancelled' => 'danger'
                ][$booking['status']] ?? 'secondary';
            ?>
            <div class="col-lg-6 mb-4">
                <div class="card shadow border-0 h-100">
                    <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($booking['title']); ?></h6>
                        <span class="badge bg-<?php echo $status_class; ?>">
                            <?php echo ucfirst($booking['status']); ?>
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-calendar-day text-primary me-2"></i>
                                    <span><strong>Date:</strong> <?php echo date('M j, Y', strtotime($booking['booking_date'])); ?></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-users text-info me-2"></i>
                                    <span><strong>Guests:</strong> <?php echo $booking['guests']; ?></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-clock text-warning me-2"></i>
                                    <span><strong>Duration:</strong> <?php echo htmlspecialchars($booking['duration']); ?></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-map-marker-alt text-success me-2"></i>
                                    <span><strong>Location:</strong> <?php echo htmlspecialchars($booking['location']); ?></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-dollar-sign text-danger me-2"></i>
                                    <span><strong>Total Price:</strong> $<?php echo $booking['total_price']; ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer bg-white border-0">
                        <small class="text-muted">
                            Booked on: <?php echo date('M j, Y g:i A', strtotime($booking['created_at'])); ?>
                        </small>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
            <h3 class="text-muted">No Bookings Yet</h3>
            <p class="text-muted mb-4">You haven't made any tour bookings yet.</p>
            <a href="tours.php" class="btn btn-primary btn-lg">
                <i class="fas fa-compass me-2"></i>Explore Tours
            </a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>