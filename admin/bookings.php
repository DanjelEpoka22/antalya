<?php
include '../includes/config.php';

// Kontrollo nëse përdoruesi është admin
if(!isset($_SESSION['user_id']) || $_SESSION['username'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Ndrysho statusin e rezervimit
if(isset($_POST['update_status'])) {
    $booking_id = mysqli_real_escape_string($conn, $_POST['booking_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $sql = "UPDATE bookings SET status = '$status' WHERE id = '$booking_id'";
    
    if(mysqli_query($conn, $sql)) {
        $success = "Booking status updated successfully!";
    } else {
        $error = "Error updating booking status: " . mysqli_error($conn);
    }
}

// Merr të gjitha rezervimet
$sql = "SELECT b.*, u.username, u.full_name, u.email, u.phone, t.title as tour_title, t.price as tour_price, t.image as tour_image
        FROM bookings b
        JOIN users u ON b.user_id = u.id
        JOIN tours t ON b.tour_id = t.id
        ORDER BY b.created_at DESC";
$result = mysqli_query($conn, $sql);

// Merr statistikat
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings"))['total'];
$confirmed_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE status = 'confirmed'"))['total'];
$pending_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE status = 'pending'"))['total'];
$cancelled_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings WHERE status = 'cancelled'"))['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Bookings - Rreze Antalya</title>
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
        
        .admin-navbar {
            background: linear-gradient(135deg, var(--primary), #1a252f) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            border: none;
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-card i {
            font-size: 2.5rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .stats-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .booking-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            border-left: 4px solid var(--accent);
            background: white;
        }
        
        .booking-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        
        .status-badge {
            font-size: 0.8rem;
            padding: 6px 12px;
            border-radius: 20px;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--primary));
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .form-select {
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 8px 12px;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .form-select:focus {
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
    <!-- Simple Admin Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark admin-navbar fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">
                <i class="fas fa-sun me-2"></i>Rreze Antalya - Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-1"></i>Dashboard
                </a>
                <a class="nav-link" href="../index.php">
                    <i class="fas fa-eye me-1"></i>View Site
                </a>
                <a class="nav-link" href="logout.php">
                    <i class="fas fa-sign-out-alt me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>

    <div class="container" style="margin-top: 100px;">
        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="fw-bold text-white">Manage Bookings</h1>
                        <p class="text-white-50">View and manage all tour bookings from customers</p>
                    </div>
                    <div class="text-end">
                        <a href="dashboard.php" class="btn btn-primary">
                            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-5">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card">
                    <i class="fas fa-calendar-check"></i>
                    <h3><?php echo $total_bookings; ?></h3>
                    <p>Total Bookings</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card" style="background: linear-gradient(135deg, #28a745, #20c997);">
                    <i class="fas fa-check-circle"></i>
                    <h3><?php echo $confirmed_bookings; ?></h3>
                    <p>Confirmed</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card" style="background: linear-gradient(135deg, #ffc107, #fd7e14);">
                    <i class="fas fa-clock"></i>
                    <h3><?php echo $pending_bookings; ?></h3>
                    <p>Pending</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="stats-card" style="background: linear-gradient(135deg, #dc3545, #e83e8c);">
                    <i class="fas fa-times-circle"></i>
                    <h3><?php echo $cancelled_bookings; ?></h3>
                    <p>Cancelled</p>
                </div>
            </div>
        </div>

        <!-- Alerts -->
        <?php if(isset($success)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Bookings List -->
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2 text-primary"></i>All Bookings
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if(mysqli_num_rows($result) > 0): ?>
                            <div class="row">
                                <?php while($row = mysqli_fetch_assoc($result)): 
                                    $status_class = [
                                        'pending' => 'warning',
                                        'confirmed' => 'success',
                                        'cancelled' => 'danger'
                                    ][$row['status']];
                                ?>
                                <div class="col-lg-6 mb-4">
                                    <div class="booking-card">
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <!-- Tour Image & Basic Info -->
                                                <div class="col-12">
                                                    <div class="d-flex align-items-start mb-3">
                                                        <?php if($row['tour_image']): ?>
                                                            <img src="../assets/images/tours/<?php echo $row['tour_image']; ?>" 
                                                                 class="rounded me-3" 
                                                                 style="width: 80px; height: 80px; object-fit: cover;" 
                                                                 alt="<?php echo htmlspecialchars($row['tour_title']); ?>">
                                                        <?php else: ?>
                                                            <div class="rounded me-3 d-flex align-items-center justify-content-center bg-light" 
                                                                 style="width: 80px; height: 80px;">
                                                                <i class="fas fa-map-marked-alt text-muted fa-2x"></i>
                                                            </div>
                                                        <?php endif; ?>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                                <h6 class="fw-bold text-primary mb-0"><?php echo htmlspecialchars($row['tour_title']); ?></h6>
                                                                <span class="badge bg-<?php echo $status_class; ?> status-badge">
                                                                    <?php echo ucfirst($row['status']); ?>
                                                                </span>
                                                            </div>
                                                            <p class="text-muted small mb-2">
                                                                <i class="fas fa-user me-1"></i>
                                                                <strong><?php echo htmlspecialchars($row['full_name']); ?></strong>
                                                            </p>
                                                            <p class="text-muted small mb-0">
                                                                <i class="fas fa-envelope me-1"></i>
                                                                <?php echo htmlspecialchars($row['email']); ?>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Booking Details -->
                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fas fa-calendar-day text-primary me-2"></i>
                                                        <div>
                                                            <small class="text-muted d-block">Booking Date</small>
                                                            <strong><?php echo date('M j, Y', strtotime($row['booking_date'])); ?></strong>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fas fa-users text-info me-2"></i>
                                                        <div>
                                                            <small class="text-muted d-block">Guests</small>
                                                            <strong><?php echo $row['guests']; ?> Person(s)</strong>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="d-flex align-items-center mb-2">
                                                        <i class="fas fa-dollar-sign text-success me-2"></i>
                                                        <div>
                                                            <small class="text-muted d-block">Total Price</small>
                                                            <strong>$<?php echo $row['total_price']; ?></strong>
                                                        </div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-clock text-warning me-2"></i>
                                                        <div>
                                                            <small class="text-muted d-block">Booked On</small>
                                                            <strong><?php echo date('M j, Y g:i A', strtotime($row['created_at'])); ?></strong>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Status Update Form -->
                                                <div class="col-12">
                                                    <div class="border-top pt-3">
                                                        <form method="POST" class="d-flex align-items-center gap-3">
                                                            <input type="hidden" name="booking_id" value="<?php echo $row['id']; ?>">
                                                            <div class="flex-grow-1">
                                                                <label class="form-label fw-bold small mb-2">Update Status:</label>
                                                                <select name="status" class="form-select" onchange="this.form.submit()">
                                                                    <option value="pending" <?php echo $row['status'] == 'pending' ? 'selected' : ''; ?>>⏳ Pending</option>
                                                                    <option value="confirmed" <?php echo $row['status'] == 'confirmed' ? 'selected' : ''; ?>>✅ Confirmed</option>
                                                                    <option value="cancelled" <?php echo $row['status'] == 'cancelled' ? 'selected' : ''; ?>>❌ Cancelled</option>
                                                                </select>
                                                            </div>
                                                            <div class="mt-4">
                                                                <button type="submit" name="update_status" class="btn btn-primary btn-sm">
                                                                    <i class="fas fa-sync-alt me-1"></i>Update
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                                <h4 class="text-muted">No Bookings Found</h4>
                                <p class="text-muted">There are no bookings in the system yet.</p>
                                <a href="../tours.php" class="btn btn-primary">
                                    <i class="fas fa-eye me-2"></i>View Tours
                                </a>
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
        // Add confirmation for cancellation
        document.addEventListener('change', function(e) {
            if (e.target.name === 'status' && e.target.value === 'cancelled') {
                if (!confirm('Are you sure you want to cancel this booking? This action cannot be undone.')) {
                    // Reset to previous value
                    e.target.form.reset();
                    return false;
                }
            }
            
            // Show loading state for status updates
            if (e.target.name === 'status') {
                const button = e.target.form.querySelector('button[type="submit"]');
                if (button) {
                    const originalText = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Updating...';
                    button.disabled = true;
                    
                    // Revert after 3 seconds (safety)
                    setTimeout(() => {
                        if (button.disabled) {
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }
                    }, 3000);
                }
            }
        });
    </script>
</body>
</html>