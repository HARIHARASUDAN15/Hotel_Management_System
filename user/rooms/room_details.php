<?php
session_start();
require_once __DIR__ . '/../../includes/user_auth.php';
require_once __DIR__ . '/../../config/db.php';

$room_id = $_GET['room_id'] ?? 0;

// Fetch room details
$sql = "SELECT * FROM rooms WHERE room_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Room Details</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="room_details.css">

</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="container">
    <h2>🏨 Room Details</h2>

    <div class="page-header">
        <a href="../floors/floors.php" class="btn">Back</a>
    </div>

    <?php if ($room): ?>
        <div class="room-details-card">
            <p><strong>Room No:</strong> <?php echo $room['room_no']; ?></p>
            <p><strong>Room Type:</strong> <?php echo $room['room_type']; ?></p>
            <p><strong>Status:</strong>
                <span class="status 
                <?php echo ($room['room_status'] == 'Available') ? 'available' : 'booked'; ?>">
                <?php echo $room['room_status']; ?>
                </span>
            </p>

            <?php if ($room['room_status'] == 'Available'): ?>
                <a href="../booking/book_room.php?room_id=<?php echo $room['room_id']; ?>"
                   class="book-btn">Book Now</a>
            <?php else: ?>
                <p class="booked-text">This room is already booked</p>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <p>Room not found.</p>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>