<?php
// Aktivizo error reporting në fillim
error_reporting(E_ALL);
ini_set('display_errors', 1);

include '../includes/config.php';

// Kontrollo nëse përdoruesi është admin
if(!isset($_SESSION['user_id']) || $_SESSION['username'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Shto tur të ri
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_tour'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price_adult = mysqli_real_escape_string($conn, $_POST['price_adult']);
    $price_child = mysqli_real_escape_string($conn, $_POST['price_child']);
    $price_infant = mysqli_real_escape_string($conn, $_POST['price_infant']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $included = mysqli_real_escape_string($conn, $_POST['included']);
    $excluded = mysqli_real_escape_string($conn, $_POST['excluded']);
    
    // Llogarit çmimin mesatar për kolonën e vjetër price (për backward compatibility)
    $average_price = ($price_adult + $price_child + $price_infant) / 3;
    
    // Shto turin në databazë
    $sql = "INSERT INTO tours (title, description, price, price_adult, price_child, price_infant, duration, location, included, excluded) 
            VALUES ('$title', '$description', '$average_price', '$price_adult', '$price_child', '$price_infant', '$duration', '$location', '$included', '$excluded')";
    
    if(mysqli_query($conn, $sql)) {
        $tour_id = mysqli_insert_id($conn);
        $success = "Tour added successfully!";
        
        // Përpunimi i fotove të shumta
        if(isset($_FILES['tour_images']) && !empty($_FILES['tour_images']['name'][0])) {
            $target_dir = "../assets/images/tours/";
            
            // Krijo folderin nëse nuk ekziston
            if(!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            $uploaded_images = [];
            
            // Përpunoni çdo foto
            foreach($_FILES['tour_images']['tmp_name'] as $key => $tmp_name) {
                if($_FILES['tour_images']['error'][$key] === 0) {
                    $image_name = time() . "_" . basename($_FILES["tour_images"]["name"][$key]);
                    $target_file = $target_dir . $image_name;
                    
                    if(move_uploaded_file($tmp_name, $target_file)) {
                        // Ruaj në tabelën tour_images
                        $insert_image_sql = "INSERT INTO tour_images (tour_id, image_path, sort_order) 
                                           VALUES ('$tour_id', '$image_name', '$key')";
                        mysqli_query($conn, $insert_image_sql);
                        $uploaded_images[] = $image_name;
                    }
                }
            }
            
            if(!empty($uploaded_images)) {
                $success .= " " . count($uploaded_images) . " images uploaded successfully!";
            }
        }
    } else {
        $error = "Error adding tour: " . mysqli_error($conn);
    }
}

// Merr të gjitha turat për shfaqje
$tours_result = mysqli_query($conn, "SELECT * FROM tours ORDER BY created_at DESC");
if(!$tours_result) {
    echo "<!-- Debug: Tour query error: " . mysqli_error($conn) . " -->";
}

// Merr statistikat
$tours_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM tours");
$tours_total = $tours_count ? mysqli_fetch_assoc($tours_count)['total'] : '0';

$bookings_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM bookings");
$bookings_total = $bookings_count ? mysqli_fetch_assoc($bookings_count)['total'] : '0';

$users_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM users WHERE username != 'admin'");
$users_total = $users_count ? mysqli_fetch_assoc($users_count)['total'] : '0';

// Merr statistikat e transportit
$transport_bookings_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM transport_bookings");
$transport_bookings_total = $transport_bookings_count ? mysqli_fetch_assoc($transport_bookings_count)['total'] : '0';

$transport_zones_count = mysqli_query($conn, "SELECT COUNT(*) as total FROM transport_zones");
$transport_zones_total = $transport_zones_count ? mysqli_fetch_assoc($transport_zones_count)['total'] : '0';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Rreze Antalya</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
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
        
        .admin-navbar {
            background: linear-gradient(135deg, var(--primary), #1a252f) !important;
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 20px rgba(0,0,0,0.1);
        }
        
        .dashboard-card {
            transition: all 0.3s ease;
            border: none;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-left: 4px solid var(--accent);
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .stats-card {
            color: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
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
        
        .stats-card p {
            opacity: 0.9;
            margin: 0;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--primary));
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
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
        
        .tour-list-item {
            transition: all 0.3s ease;
            border: none;
            border-radius: 10px;
            margin-bottom: 10px;
            border-left: 3px solid var(--accent);
        }
        
        .tour-list-item:hover {
            background: #f8f9fa;
            transform: translateX(5px);
        }
        
        .section-title {
            position: relative;
            margin-bottom: 2rem;
            color: var(--primary);
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, var(--accent), var(--secondary));
        }
    </style>
</head>
<body>
    <!-- Admin Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark admin-navbar fixed-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="../index.php">
                <i class="fas fa-sun me-2"></i>Rreze Antalya - Admin
            </a>
            <div class="navbar-nav ms-auto">
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
        <!-- Welcome Section -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="fw-bold text-dark">Admin Dashboard</h1>
                        <p class="text-muted">Welcome back, <?php echo $_SESSION['full_name']; ?>! Manage your tours, transfers and bookings.</p>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary fs-6">
                            <i class="fas fa-user-shield me-1"></i>Administrator
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-5">
            <div class="col-md-3 mb-4">
                <div class="stats-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-map-marked-alt"></i>
                    <h3><?php echo $tours_total; ?></h3>
                    <p>Total Tours</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stats-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fas fa-calendar-check"></i>
                    <h3><?php echo $bookings_total; ?></h3>
                    <p>Tour Bookings</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stats-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="fas fa-users"></i>
                    <h3><?php echo $users_total; ?></h3>
                    <p>Registered Users</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="stats-card" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <i class="fas fa-car"></i>
                    <h3><?php echo $transport_bookings_total; ?></h3>
                    <p>Transfer Bookings</p>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="row">
            <!-- Add Tour Form -->
            <div class="col-lg-6 mb-4">
                <div class="dashboard-card">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-plus-circle me-2 text-primary"></i>Add New Tour
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if(isset($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(isset($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" enctype="multipart/form-data" id="tourForm">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label for="title" class="form-label">Tour Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required 
                                           placeholder="Enter tour title">
                                </div>
                                
                                <div class="col-12">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="4" required 
                                              placeholder="Describe the tour experience"></textarea>
                                </div>

                                <!-- Prices by Age Group -->
                                <div class="col-md-4">
                                    <label for="price_adult" class="form-label">Price - Adult ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="price_adult" name="price_adult" required 
                                           placeholder="0.00" min="0" value="0">
                                    <small class="form-text text-muted">Age: 12+ years</small>
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="price_child" class="form-label">Price - Child ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="price_child" name="price_child" required 
                                           placeholder="0.00" min="0" value="0">
                                    <small class="form-text text-muted">Age: 4-11 years</small>
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="price_infant" class="form-label">Price - Infant ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="price_infant" name="price_infant" required 
                                           placeholder="0.00" min="0" value="0">
                                    <small class="form-text text-muted">Age: 0-3 years</small>
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="duration" class="form-label">Duration</label>
                                    <input type="text" class="form-control" id="duration" name="duration" required 
                                           placeholder="e.g., 3 days, 2 nights">
                                </div>
                                
                                <div class="col-12">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location" required 
                                           placeholder="Tour location">
                                </div>

                                <!-- Included Services -->
                                <div class="col-12">
                                    <label for="included" class="form-label">What's Included</label>
                                    <textarea class="form-control" id="included" name="included" rows="3" 
                                              placeholder="Enter services included in the tour price (separate with commas)&#10;Example: Hotel pickup, Guide, Lunch, Entrance fees"></textarea>
                                    <small class="form-text text-muted">Separate each service with a comma</small>
                                </div>

                                <!-- Excluded Services -->
                                <div class="col-12">
                                    <label for="excluded" class="form-label">What's Not Included</label>
                                    <textarea class="form-control" id="excluded" name="excluded" rows="3" 
                                              placeholder="Enter services not included in the tour price (separate with commas)&#10;Example: Personal expenses, Tips, Alcoholic drinks"></textarea>
                                    <small class="form-text text-muted">Separate each service with a comma</small>
                                </div>
                                
                                <!-- Multiple Images Upload -->
                                <div class="col-12">
                                    <label for="tour_images" class="form-label">Tour Images</label>
                                    <input type="file" class="form-control" id="tour_images" name="tour_images[]" 
                                           accept="image/*" multiple>
                                    <div class="form-text">You can select multiple images. Recommended size: 800x600px. Max 2MB per image.</div>
                                    <div id="imagePreview" class="mt-2"></div>
                                </div>
                                
                                <div class="col-12">
                                    <button type="submit" name="add_tour" class="btn btn-primary w-100">
                                        <i class="fas fa-plus me-2"></i>Add Tour
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Existing Tours & Stats -->
            <div class="col-lg-6">
                <!-- Quick Actions -->
                <div class="dashboard-card mb-4">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-rocket me-2 text-primary"></i>Quick Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <a href="bookings.php" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="fas fa-calendar-alt me-2"></i>Tour Bookings
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="transport_bookings.php" class="btn btn-outline-success w-100 mb-2">
                                    <i class="fas fa-car me-2"></i>Transfer Bookings
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="../tours.php" class="btn btn-outline-info w-100 mb-2">
                                    <i class="fas fa-eye me-2"></i>View Tours
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="../transport.php" class="btn btn-outline-warning w-100 mb-2">
                                    <i class="fas fa-car me-2"></i>View Transfers
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="../index.php" class="btn btn-outline-secondary w-100 mb-2">
                                    <i class="fas fa-home me-2"></i>Back to Site
                                </a>
                            </div>
                            <div class="col-md-6">
                                <a href="logout.php" class="btn btn-outline-danger w-100 mb-2">
                                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Existing Tours -->
                <div class="dashboard-card">
                    <div class="card-header bg-white border-0">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-list me-2 text-primary"></i>Existing Tours
                        </h5>
                    </div>
                    <div class="card-body">
                        <?php if($tours_result && mysqli_num_rows($tours_result) > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php while($row = mysqli_fetch_assoc($tours_result)): ?>
                                    <div class="list-group-item tour-list-item px-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($row['title']); ?></h6>
                                                <div class="d-flex flex-wrap gap-2">
                                                    <small class="text-muted">
                                                        <i class="fas fa-dollar-sign me-1"></i><?php echo $row['price']; ?>
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock me-1"></i><?php echo htmlspecialchars($row['duration']); ?>
                                                    </small>
                                                    <small class="text-muted">
                                                        <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($row['location']); ?>
                                                    </small>
                                                </div>
                                            </div>
                                            <div class="btn-group">
                                                <a href="edit_tour.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   data-bs-toggle="tooltip" title="Edit Tour">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="delete_tour.php?id=<?php echo $row['id']; ?>" 
                                                   class="btn btn-sm btn-outline-danger" 
                                                   onclick="return confirm('Are you sure you want to delete this tour?')"
                                                   data-bs-toggle="tooltip" title="Delete Tour">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No tours found. Add your first tour to get started!</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script>
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
        });

        // Image preview for multiple files
        document.getElementById('tour_images')?.addEventListener('change', function(e) {
            const preview = document.getElementById('imagePreview');
            preview.innerHTML = '';
            
            const files = e.target.files;
            
            for(let i = 0; i < files.length; i++) {
                const file = files[i];
                
                if(file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    
                    reader.onload = function(e) {
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'img-thumbnail me-2 mb-2';
                        img.style.width = '100px';
                        img.style.height = '80px';
                        img.style.objectFit = 'cover';
                        preview.appendChild(img);
                    }
                    
                    reader.readAsDataURL(file);
                }
            }
        });

        // Form validation for prices
        document.getElementById('tourForm')?.addEventListener('submit', function(e) {
            const priceAdult = parseFloat(document.getElementById('price_adult').value);
            const priceChild = parseFloat(document.getElementById('price_child').value);
            const priceInfant = parseFloat(document.getElementById('price_infant').value);
            
            if (priceAdult < 0 || priceChild < 0 || priceInfant < 0) {
                e.preventDefault();
                alert('Prices cannot be negative!');
                return false;
            }
            
            // Set default price as adult price for backward compatibility
            document.getElementById('price').value = priceAdult;
        });
    </script>
</body>
</html>