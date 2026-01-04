<?php
session_start();
require_once __DIR__ . '/../../includes/user_auth.php';
require_once __DIR__ . '/../../config/db.php';

$floor_id = $_GET['floor_id'] ?? 0;

// Fetch rooms for selected floor
$sql = "SELECT * FROM rooms WHERE floor_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $floor_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Rooms</title>

    <!-- Common CSS -->
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../user/rooms/rooms.css">


</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="container">

    <!-- Header row -->
    <div class="page-header">
        <h2>Rooms</h2>
        <a href="../../user/home.php" class="btn">Back</a>
    </div>

    <p class="sub-text">Select a room from this floor</p>

    <div class="room-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($room = $result->fetch_assoc()): ?>
                <div class="room-card">
                    <h3>Room <?php echo $room['room_no']; ?></h3>

                    <p class="room-type">
                        <?php echo $room['room_type']; ?>
                    </p>

                    <span class="status 
                        <?php echo ($room['room_status'] === 'Available') ? 'available' : 'booked'; ?>">
                        <?php echo $room['room_status']; ?>
                    </span>

                    <a href="room_details.php?room_id=<?php echo $room['room_id']; ?>"
                       class="details-btn">
                        View Details
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="no-data">No rooms available for this floor.</p>
        <?php endif; ?>
    </div>

</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>
