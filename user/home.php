<?php
session_start();
require_once __DIR__ . '/../includes/user_auth.php';
require_once __DIR__ . '/../config/db.php';

// Get user name from session
$username = $_SESSION['name'] ?? 'Guest';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Home</title>

    <!-- User Dashboard CSS -->
    <link rel="stylesheet" href="user.css">

    <!-- Navbar CSS -->
    <link rel="stylesheet" href="../assets/css/common.css">

    <!-- Font Awesome (optional, future use) -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<?php include __DIR__ . '/../includes/navbar.php'; ?>

<!-- Main Container -->
<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($username); ?> 👋</h1>

    <div class="cards">

        <div class="card">
            <h3>🏢 View Floors</h3>
            <p>Check available floors in hotel</p>
            <a href="floors/floors.php">View</a>
        </div>

        <div class="card">
            <h3>🛏️ Rooms</h3>
            <p>View rooms & details</p>
            <a href="rooms/rooms.php">View</a>
        </div>

        <div class="card">
            <h3>📅 My Bookings</h3>
            <p>Booking history & status</p>
            <a href="booking/booking_history.php">View</a>
        </div>

        <div class="card">
            <h3>⭐ Reviews</h3>
            <p>Add your hotel experience</p>
            <a href="reviews/add_review.php">Add</a>
        </div>

    </div>
</div>

<!-- Footer -->
<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
