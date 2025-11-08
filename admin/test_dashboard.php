<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test Dashboard</h1>";

include '../includes/config.php';
echo "<p>Config included successfully</p>";

echo "<p>Session user_id: " . (isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'NOT SET') . "</p>";
echo "<p>Session username: " . (isset($_SESSION['username']) ? $_SESSION['username'] : 'NOT SET') . "</p>";

if(!isset($_SESSION['user_id']) || $_SESSION['username'] != 'admin') {
    echo "<p style='color: red;'>NOT ADMIN - Would redirect to login</p>";
} else {
    echo "<p style='color: green;'>ADMIN ACCESS GRANTED</p>";
    
    // Test database connection
    $result = mysqli_query($conn, "SELECT COUNT(*) as count FROM tours");
    if($result) {
        $row = mysqli_fetch_assoc($result);
        echo "<p>Tours in database: " . $row['count'] . "</p>";
    } else {
        echo "<p>Database error: " . mysqli_error($conn) . "</p>";
    }
}
?>