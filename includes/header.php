<?php
// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple header for admin pages
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="../admin.css">
</head>
<body>c
    <header style="background:#2c3e50;color:white;padding:15px;text-align:center;">
        <h1>Hotel Management - Admin Panel</h1>
        <nav>
            <a href="../dashboard.php" style="color:white;margin:0 10px;">Dashboard</a> |
            <a href="../floors/manage_floors.php" style="color:white;margin:0 10px;">Floors</a> |
            <a href="../rooms/manage_rooms.php" style="color:white;margin:0 10px;">Rooms</a> |
            <a href="../auth/logout.php" style="color:white;margin:0 10px;">Logout</a>
        </nav>
    </header>
    <main style="padding:20px;">