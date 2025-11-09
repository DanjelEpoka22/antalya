<?php
include '../includes/config.php';

// Kontrollo nëse përdoruesi është admin
if(!isset($_SESSION['user_id']) || $_SESSION['username'] != 'admin') {
    header("Location: ../login.php");
    exit;
}

// Merr ID e turit nga URL
if(!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit;
}

$tour_id = mysqli_real_escape_string($conn, $_GET['id']);

// Merr të dhënat e turit me fotot
$sql = "SELECT * FROM tours WHERE id = '$tour_id'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0) {
    header("Location: dashboard.php");
    exit;
}

$tour = mysqli_fetch_assoc($result);

// Merr fotot e turit
$images_sql = "SELECT * FROM tour_images WHERE tour_id = '$tour_id' ORDER BY sort_order ASC";
$images_result = mysqli_query($conn, $images_sql);

// Përditëso turin
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_tour'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price_adult = mysqli_real_escape_string($conn, $_POST['price_adult']);
    $price_child = mysqli_real_escape_string($conn, $_POST['price_child']);
    $price_infant = mysqli_real_escape_string($conn, $_POST['price_infant']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $included = mysqli_real_escape_string($conn, $_POST['included']);
    $excluded = mysqli_real_escape_string($conn, $_POST['excluded']);
    
    // Llogarit çmimin mesatar për backward compatibility
    $average_price = ($price_adult + $price_child + $price_infant) / 3;
    
    // Përditëso të dhënat bazë të turit
    $sql = "UPDATE tours SET 
            title = '$title', 
            description = '$description', 
            price = '$average_price',
            price_adult = '$price_adult',
            price_child = '$price_child', 
            price_infant = '$price_infant',
            duration = '$duration', 
            location = '$location',
            included = '$included',
            excluded = '$excluded',
            updated_at = NOW()
            WHERE id = '$tour_id'";
    
    if(mysqli_query($conn, $sql)) {
        // Përpunimi i fotove të reja
        if(isset($_FILES['tour_images']) && !empty($_FILES['tour_images']['name'][0])) {
            $target_dir = "../assets/images/tours/";
            
            // Krijo folderin nëse nuk ekziston
            if(!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            
            // Gjej rendin maksimal aktual
            $max_order_sql = "SELECT MAX(sort_order) as max_order FROM tour_images WHERE tour_id = '$tour_id'";
            $max_order_result = mysqli_query($conn, $max_order_sql);
            $max_order = $max_order_result ? mysqli_fetch_assoc($max_order_result)['max_order'] : 0;
            $current_order = $max_order + 1;
            
            // Përpunoni çdo foto të re
            foreach($_FILES['tour_images']['tmp_name'] as $key => $tmp_name) {
                if($_FILES['tour_images']['error'][$key] === 0) {
                    $image_name = time() . "_" . $key . "_" . basename($_FILES["tour_images"]["name"][$key]);
                    $target_file = $target_dir . $image_name;
                    
                    if(move_uploaded_file($tmp_name, $target_file)) {
                        // Ruaj në tabelën tour_images
                        $insert_image_sql = "INSERT INTO tour_images (tour_id, image_path, sort_order) 
                                           VALUES ('$tour_id', '$image_name', '$current_order')";
                        mysqli_query($conn, $insert_image_sql);
                        $current_order++;
                    }
                }
            }
        }
        
        $success = "Tour updated successfully!";
        // Rifresko të dhënat e turit
        $result = mysqli_query($conn, "SELECT * FROM tours WHERE id = '$tour_id'");
        $tour = mysqli_fetch_assoc($result);
        
        // Rifresko listën e fotove
        $images_result = mysqli_query($conn, $images_sql);
    } else {
        $error = "Error updating tour: " . mysqli_error($conn);
    }
}

// Fshi foto
if(isset($_GET['delete_image'])) {
    $image_id = mysqli_real_escape_string($conn, $_GET['delete_image']);
    
    // Merr emrin e file për të fshirë fizikisht
    $image_sql = "SELECT image_path FROM tour_images WHERE id = '$image_id' AND tour_id = '$tour_id'";
    $image_result = mysqli_query($conn, $image_sql);
    
    if($image_result && mysqli_num_rows($image_result) > 0) {
        $image_data = mysqli_fetch_assoc($image_result);
        $image_path = "../assets/images/tours/" . $image_data['image_path'];
        
        // Fshi nga databaza
        $delete_sql = "DELETE FROM tour_images WHERE id = '$image_id' AND tour_id = '$tour_id'";
        if(mysqli_query($conn, $delete_sql)) {
            // Fshi file fizik
            if(file_exists($image_path)) {
                unlink($image_path);
            }
            $success = "Image deleted successfully!";
            // Rifresko listën e fotove
            $images_result = mysqli_query($conn, $images_sql);
        } else {
            $error = "Error deleting image!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Tour - Rreze Antalya</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .image-preview {
            width: 100px;
            height: 80px;
            object-fit: cover;
            margin: 5px;
            border-radius: 5px;
        }
        .existing-images {
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            background: #f8f9fa;
        }
        .image-item {
            position: relative;
            display: inline-block;
            margin: 5px;
        }
        .delete-image-btn {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.9);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Edit Tour</h1>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        
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
        
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Edit Tour: <?php echo $tour['title']; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data" id="editTourForm">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="title" class="form-label">Tour Title</label>
                                    <input type="text" class="form-control" id="title" name="title" 
                                           value="<?php echo htmlspecialchars($tour['title']); ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="location" class="form-label">Location</label>
                                    <input type="text" class="form-control" id="location" name="location" 
                                           value="<?php echo htmlspecialchars($tour['location']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($tour['description']); ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="price_adult" class="form-label">Price - Adult ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="price_adult" name="price_adult" 
                                           value="<?php echo isset($tour['price_adult']) ? $tour['price_adult'] : $tour['price']; ?>" required>
                                    <small class="form-text text-muted">Age: 12+ years</small>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="price_child" class="form-label">Price - Child ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="price_child" name="price_child" 
                                           value="<?php echo isset($tour['price_child']) ? $tour['price_child'] : ($tour['price'] * 0.7); ?>" required>
                                    <small class="form-text text-muted">Age: 4-11 years</small>
                                </div>
                                
                                <div class="col-md-4 mb-3">
                                    <label for="price_infant" class="form-label">Price - Infant ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="price_infant" name="price_infant" 
                                           value="<?php echo isset($tour['price_infant']) ? $tour['price_infant'] : 0; ?>" required>
                                    <small class="form-text text-muted">Age: 0-3 years</small>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="duration" class="form-label">Duration</label>
                                    <input type="text" class="form-control" id="duration" name="duration" 
                                           value="<?php echo htmlspecialchars($tour['duration']); ?>" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="included" class="form-label">What's Included</label>
                                    <textarea class="form-control" id="included" name="included" rows="3" 
                                              placeholder="Enter services included in the tour price (separate with commas)"><?php echo htmlspecialchars($tour['included'] ?? ''); ?></textarea>
                                    <small class="form-text text-muted">Separate each service with a comma</small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="excluded" class="form-label">What's Not Included</label>
                                    <textarea class="form-control" id="excluded" name="excluded" rows="3" 
                                              placeholder="Enter services not included in the tour price (separate with commas)"><?php echo htmlspecialchars($tour['excluded'] ?? ''); ?></textarea>
                                    <small class="form-text text-muted">Separate each service with a comma</small>
                                </div>
                            </div>
                            
                            <!-- Existing Images -->
                            <?php if($images_result && mysqli_num_rows($images_result) > 0): ?>
                            <div class="mb-3">
                                <label class="form-label">Existing Images</label>
                                <div class="existing-images">
                                    <?php while($image = mysqli_fetch_assoc($images_result)): ?>
                                        <div class="image-item">
                                            <img src="../assets/images/tours/<?php echo $image['image_path']; ?>" 
                                                 class="image-preview" 
                                                 alt="Tour image">
                                            <a href="edit_tour.php?id=<?php echo $tour_id; ?>&delete_image=<?php echo $image['id']; ?>" 
                                               class="delete-image-btn"
                                               onclick="return confirm('Are you sure you want to delete this image?')">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </div>
                                    <?php endwhile; ?>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Add New Images -->
                            <div class="mb-3">
                                <label for="tour_images" class="form-label">Add More Images</label>
                                <input type="file" class="form-control" id="tour_images" name="tour_images[]" 
                                       accept="image/*" multiple>
                                <div class="form-text">You can select multiple images. Recommended size: 800x600px. Max 2MB per image.</div>
                                <div id="imagePreview" class="mt-2"></div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="update_tour" class="btn btn-primary btn-lg">Update Tour</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
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
                        img.className = 'image-preview';
                        preview.appendChild(img);
                    }
                    
                    reader.readAsDataURL(file);
                }
            }
        });

        // Form validation
        document.getElementById('editTourForm')?.addEventListener('submit', function(e) {
            const priceAdult = parseFloat(document.getElementById('price_adult').value);
            const priceChild = parseFloat(document.getElementById('price_child').value);
            const priceInfant = parseFloat(document.getElementById('price_infant').value);
            
            if (priceAdult < 0 || priceChild < 0 || priceInfant < 0) {
                e.preventDefault();
                alert('Prices cannot be negative!');
                return false;
            }
        });
    </script>
</body>
</html>