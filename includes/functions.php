<?php
// Funksione ndihmëse

// Funksion për të kontrolluar nëse përdoruesi është admin
function isAdmin() {
    return isset($_SESSION['username']) && $_SESSION['username'] == 'admin';
}

// Funksion për të marrë të gjitha turat
function getAllTours($conn, $limit = null) {
    $sql = "SELECT * FROM tours ORDER BY created_at DESC";
    if($limit) {
        $sql .= " LIMIT $limit";
    }
    return mysqli_query($conn, $sql);
}

// Funksion për të marrë turin me ID
function getTourById($conn, $id) {
    $id = mysqli_real_escape_string($conn, $id);
    $sql = "SELECT * FROM tours WHERE id = '$id'";
    $result = mysqli_query($conn, $sql);
    return mysqli_fetch_assoc($result);
}

// Funksion për të marrë rezervimet e përdoruesit
function getUserBookings($conn, $user_id) {
    $user_id = mysqli_real_escape_string($conn, $user_id);
    $sql = "SELECT b.*, t.title, t.image, t.location 
            FROM bookings b 
            JOIN tours t ON b.tour_id = t.id 
            WHERE b.user_id = '$user_id' 
            ORDER BY b.created_at DESC";
    return mysqli_query($conn, $sql);
}
?>