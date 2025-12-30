<?php
session_start();

// Check if user is logged in and role is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to login page if not admin
    header("Location: ../auth/login.php");
    exit();
}
?>