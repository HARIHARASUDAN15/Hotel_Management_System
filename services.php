<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services | SDET Hotel</title>
    <link rel="stylesheet" href="../Hotel_Management_System/assets/css/about.css">
    <link rel="stylesheet" href="../Hotel_Management_System/assets/css/common.css">
    <!-- Optional: FontAwesome for service icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<!-- Navbar -->
<?php include 'includes/navbar.php'; ?>

<!-- Services Section -->
<section class="section" style="margin-top:120px;">
    <h2>Our Services</h2>
    <div class="section-services">
        <p><i class="fas fa-bed"></i>Luxury Rooms</p>
        <p><i class="fas fa-wifi"></i>Free Wi-Fi</p>
        <p><i class="fas fa-concierge-bell"></i>24/7 Room Service</p>
        <p><i class="fas fa-utensils"></i>Restaurant & Parking</p>
    </div>
</section>


<!-- Footer -->
<?php include 'includes/footer.php'; ?>

</body>
</html>
