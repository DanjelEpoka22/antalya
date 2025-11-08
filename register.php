<?php
include 'includes/config.php';

// Nëse përdoruesi është tashmë i loguar, ridrejtoje
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Proceso regjistrimin
if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $terms = isset($_POST['terms']) ? true : false;
    
    // Validimet
    $errors = [];
    
    // Kontrollo terms
    if(!$terms) {
        $errors[] = "You must agree to the Terms of Service and Privacy Policy!";
    }
    
    // Kontrollo nëse fjalëkalimet përputhen
    if($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }
    
    // Kontrollo forcën e fjalëkalimit
    if(strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long!";
    }
    
    // Kontrollo nëse username ekziston
    $check_username = "SELECT id FROM users WHERE username = '$username'";
    $result = mysqli_query($conn, $check_username);
    if(mysqli_num_rows($result) > 0) {
        $errors[] = "Username already exists!";
    }
    
    // Kontrollo nëse email ekziston
    $check_email = "SELECT id FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $check_email);
    if(mysqli_num_rows($result) > 0) {
        $errors[] = "Email already exists!";
    }
    
    // Nëse nuk ka gabime, regjistro përdoruesin
    if(empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, email, password, full_name, phone) 
                VALUES ('$username', '$email', '$hashed_password', '$full_name', '$phone')";
        
        if(mysqli_query($conn, $sql)) {
            $user_id = mysqli_insert_id($conn);
            
            // Auto-login pas regjistrimit
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['full_name'] = $full_name;
            
            header("Location: index.php?registered=1");
            exit;
        } else {
            $errors[] = "Registration failed. Please try again.";
        }
    }
}

// Check for registration success redirect
if(isset($_GET['registered']) && $_GET['registered'] == 1) {
    $success = "Registration successful! Welcome to Rreze Antalya!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Rreze Antalya</title>
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
            display: flex;
            align-items: center;
        }
        
        .register-card {
            border: none;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary), #1a252f);
            border-bottom: none;
            padding: 2rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--accent), var(--primary));
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.4);
        }
        
        .form-control {
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card register-card">
                    <div class="card-header text-white text-center">
                        <h2 class="mb-0">
                            <i class="fas fa-sun me-2"></i>Rreze Antalya
                        </h2>
                        <p class="mb-0 mt-2 opacity-75">Create your account and start your journey</p>
                    </div>
                    <div class="card-body p-4">
                        <?php if(isset($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo $success; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                <div class="mt-2">
                                    <a href="tours.php" class="btn btn-sm btn-success me-2">Explore Tours</a>
                                    <a href="profile.php" class="btn btn-sm btn-outline-success">View Profile</a>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($errors)): ?>
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <strong>Please fix the following errors:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach($errors as $error): ?>
                                        <li><?php echo $error; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="username" class="form-label fw-bold">
                                        <i class="fas fa-user me-2"></i>Username *
                                    </label>
                                    <input type="text" class="form-control" id="username" name="username" 
                                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>" 
                                           required
                                           minlength="3"
                                           placeholder="Choose a username">
                                    <div class="form-text">Minimum 3 characters</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label fw-bold">
                                        <i class="fas fa-envelope me-2"></i>Email *
                                    </label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                                           required
                                           placeholder="your@email.com">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="password" class="form-label fw-bold">
                                        <i class="fas fa-lock me-2"></i>Password *
                                    </label>
                                    <input type="password" class="form-control" id="password" name="password" 
                                           required
                                           minlength="6"
                                           placeholder="Enter your password">
                                    <div class="form-text">Minimum 6 characters</div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="confirm_password" class="form-label fw-bold">
                                        <i class="fas fa-lock me-2"></i>Confirm Password *
                                    </label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                           required
                                           minlength="6"
                                           placeholder="Confirm your password">
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label fw-bold">
                                        <i class="fas fa-id-card me-2"></i>Full Name *
                                    </label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" 
                                           value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>" 
                                           required
                                           placeholder="Your full name">
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label fw-bold">
                                        <i class="fas fa-phone me-2"></i>Phone Number
                                    </label>
                                    <input type="tel" class="form-control" id="phone" name="phone" 
                                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>"
                                           placeholder="+1 234 567 890">
                                    <div class="form-text">Optional</div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="terms" name="terms" required>
                                    <label class="form-check-label" for="terms">
                                        I agree to the <a href="terms.php" target="_blank">Terms of Service</a> 
                                        and <a href="privacy.php" target="_blank">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>
                            
                            <div class="d-grid">
                                <button type="submit" name="register" class="btn btn-primary btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Create Account
                                </button>
                            </div>
                        </form>
                        
                        <div class="text-center mt-4">
                            <p class="mb-0">Already have an account? 
                                <a href="login.php" class="fw-bold text-primary">Login here</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>