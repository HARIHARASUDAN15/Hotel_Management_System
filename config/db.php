<?php
// Start connection variables
$host = "localhost";   // Usually localhost
$user = "root";        // Your DB username
$pass = "";            // Your DB password
$db   = "hotel_db";    // Your database name

// Create connection
$conn = mysqli_connect($host, $user, $pass, $db);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
// Optional: set charset
mysqli_set_charset($conn, "utf8");