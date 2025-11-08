<?php
session_start();

// Konfigurimi i databazës
define('DB_SERVER', '127.0.0.1');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'tours-db');

// Lidhja me databazën
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Kontrollo lidhjen
if($conn === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}
?>