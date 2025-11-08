<?php
include 'includes/config.php';

// Kontrollo nëse përdoruesi është i loguar
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Merr të dhënat e përdoruesit
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Proceso përditësimin e profilit
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $sql = "UPDATE users SET full_name = '$full_name', phone = '$phone', email = '$email' WHERE id = '$user_id'";
    
    if(mysqli_query($conn, $sql)) {
        $_SESSION['full_name'] = $full_name;
        $_SESSION['email'] = $email;
        $success = "Profile updated successfully!";
        
        // Rifresko të dhënat e përdoruesit
        $result = mysqli_query($conn, "SELECT * FROM users WHERE id = '$user_id'");
        $user = mysqli_fetch_assoc($result);
    } else {
        $error = "Error updating profile: " . mysqli_error($conn);
    }
}
?>

<?php include 'includes/header.php'; ?>

<div class="container mt-4" style="margin-top: 100px;">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow border-0">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-user-circle me-2"></i>My Profile
                    </h4>
                </div>
                <div class="card-body p-4">
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
                    
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Username</label>
                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" readonly>
                                <div class="form-text">Username cannot be changed</div>
                            </div>
                            
                            <div class="col-md-6">
                                <label for="email" class="form-label fw-bold">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            
                            <div class="col-12">
                                <label for="full_name" class="form-label fw-bold">Full Name *</label>
                                <input type="text" class="form-control" id="full_name" name="full_name" 
                                       value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                            </div>
                            
                            <div class="col-12">
                                <label for="phone" class="form-label fw-bold">Phone Number</label>
                                <input type="tel" class="form-control" id="phone" name="phone" 
                                       value="<?php echo htmlspecialchars($user['phone']); ?>">
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label fw-bold">Member Since</label>
                                <input type="text" class="form-control" 
                                       value="<?php echo date('F j, Y', strtotime($user['created_at'])); ?>" readonly>
                            </div>
                            
                            <div class="col-12">
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Update Profile
                                </button>
                                <a href="my_bookings.php" class="btn btn-outline-primary ms-2">
                                    <i class="fas fa-calendar me-2"></i>View My Bookings
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>