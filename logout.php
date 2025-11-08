<?php
include 'includes/config.php';

// Shkatërro session
session_destroy();

// Ridrejto në faqen kryesore
header("Location: index.php");
exit;
?>