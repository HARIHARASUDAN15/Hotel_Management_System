<nav class="navbar">
    <div class="nav-container">

        <!-- Hotel Logo / Name -->
        <div class="logo">
            <a href="/hotel_management_system/index.php">SDET Hotel</a>
        </div>

        <!-- Menu Links -->
        <ul class="nav-links">
            <li><a href="/hotel_management_system/index.php">Home</a></li>
            <li><a href="/hotel_management_system/about.php">About</a></li>
            <li><a href="/hotel_management_system/services.php">Services</a></li>
            <li><a href="../../Hotel_Management_System/user/reviews/view_reviews.php">Reviews</a></li>
            <?php if(isset($_SESSION['user_id'])): ?>
                <li>
  <a href="/hotel_management_system/auth/logout.php" class="logout-btn">Logout</a>
</li>
            <?php else: ?>
                <li><a href="/hotel_management_system/auth/login.php">Login</a></li>
            <?php endif; ?>
        </ul>

    </div>
</nav>
