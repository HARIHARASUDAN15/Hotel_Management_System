<?php
session_start();
include '../config/db.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Fetch admin name from database
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name FROM users WHERE id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Set display name safely
$display_name = isset($admin['name']) ? $admin['name'] : 'Admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SDET Hotel</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
</head>
<body>

<!-- Navbar -->
<nav class="navbar">
    <div class="logo">
        <a href="dashboard.php">SDET Hotel</a>
    </div>
    <ul class="nav-menu">
        
        <li> <a href="/Hotel_Management_System/auth/logout.php" class="logout-btn">Logout</a></li>
    </ul>
</nav>

<!-- Main Container -->
<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($display_name); ?></h1>

    <div class="cards">
        <div class="card">
            <i class="fas fa-users fa-2x"></i>
            <h3>Manage Users</h3>
            <a href="users/manage_users.php">Go</a>
        </div>
        <div class="card">
            <i class="fas fa-bed fa-2x"></i>
            <h3>Manage Rooms</h3>
            <a href="rooms/manage_rooms.php">Go</a>
        </div>
        <div class="card">
            <i class="fas fa-calendar-check fa-2x"></i>
            <h3>View Bookings</h3>
            <a href="bookings/view_bookings.php">Go</a>
        </div>
        <div class="card">
            <i class="fas fa-star fa-2x"></i>
            <h3>Manage Reviews</h3>
            <a href="reviews/manage_reviews.php">Go</a>
        </div>
        <div class="card">
            <i class="fas fa-user-cog fa-2x"></i>
            <h3>Admin Profile</h3>
            <a href="admin_profile.php">Go</a>
        </div>
        <div class="card">
            <i class="fas fa-layer-group fa-2x"></i>
            <h3>Manage Floors</h3>
            <a href="floors/manage_floors.php">Go</a>
        </div>
    </div>
</div>

<!-- Footer -->
<footer>
    <p>&copy; <?php echo date("Y"); ?> SDET Hotel. All rights reserved.</p>
</footer>

</body>
</html>
