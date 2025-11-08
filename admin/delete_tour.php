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

// Fshi turin
$sql = "DELETE FROM tours WHERE id = '$tour_id'";

if(mysqli_query($conn, $sql)) {
    $_SESSION['success'] = "Tour deleted successfully!";
} else {
    $_SESSION['error'] = "Error deleting tour: " . mysqli_error($conn);
}

// Ridrejto në dashboard
header("Location: dashboard.php");
exit;
?>