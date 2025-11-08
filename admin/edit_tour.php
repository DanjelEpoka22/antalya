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

// Merr të dhënat e turit
$sql = "SELECT * FROM tours WHERE id = '$tour_id'";
$result = mysqli_query($conn, $sql);

if(mysqli_num_rows($result) == 0) {
    header("Location: dashboard.php");
    exit;
}

$tour = mysqli_fetch_assoc($result);

// Përditëso turin
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_tour'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $price = mysqli_real_escape_string($conn, $_POST['price']);
    $duration = mysqli_real_escape_string($conn, $_POST['duration']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    
    // Përpunimi i fotove
    $image = $tour['image']; // Mbaje foton e vjetër si parazgjedhje
    
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $target_dir = "../assets/images/tours/";
        $image = basename($_FILES["image"]["name"]);
        $target_file = $target_dir . $image;
        move_uploaded_file($_FILES["image"]["tmp_name"], $target_file);
    }
    
    $sql = "UPDATE tours SET 
            title = '$title', 
            description = '$description', 
            price = '$price', 
            duration = '$duration', 
            location = '$location', 
            image = '$image' 
            WHERE id = '$tour_id'";
    
    if(mysqli_query($conn, $sql)) {
        $success = "Tour updated successfully!";
        // Rifresko të dhënat e turit
        $result = mysqli_query($conn, "SELECT * FROM tours WHERE id = '$tour_id'");
        $tour = mysqli_fetch_assoc($result);
    } else {
        $error = "Error updating tour: " . mysqli_error($conn);
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
</head>
<body>
    <?php include '../includes/header.php'; ?>
    
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Edit Tour</h1>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
        
        <?php if(isset($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <?php if(isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Edit Tour: <?php echo $tour['title']; ?></h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="title" class="form-label">Tour Title</label>
                                <input type="text" class="form-control" id="title" name="title" 
                                       value="<?php echo $tour['title']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="5" required><?php echo $tour['description']; ?></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="price" class="form-label">Price ($)</label>
                                    <input type="number" step="0.01" class="form-control" id="price" name="price" 
                                           value="<?php echo $tour['price']; ?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="duration" class="form-label">Duration</label>
                                    <input type="text" class="form-control" id="duration" name="duration" 
                                           value="<?php echo $tour['duration']; ?>" required>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" 
                                       value="<?php echo $tour['location']; ?>" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image" class="form-label">Tour Image</label>
                                <input type="file" class="form-control" id="image" name="image">
                                <?php if($tour['image']): ?>
                                    <div class="mt-2">
                                        <p>Current Image: <?php echo $tour['image']; ?></p>
                                        <img src="../assets/images/tours/<?php echo $tour['image']; ?>" 
                                             alt="Current tour image" class="img-thumbnail" style="max-height: 200px;">
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="update_tour" class="btn btn-primary">Update Tour</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>