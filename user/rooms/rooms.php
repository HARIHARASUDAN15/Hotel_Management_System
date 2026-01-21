<?php
session_start();
require_once __DIR__ . '/../../includes/user_auth.php';
require_once __DIR__ . '/../../config/db.php';

/* ===============================
   Accept floor_id OR id
================================ */
if (isset($_GET['floor_id']) && is_numeric($_GET['floor_id'])) {
    $floor_id = (int) $_GET['floor_id'];
} elseif (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $floor_id = (int) $_GET['id'];
} else {
    echo "<h3 style='color:red'>Invalid Floor Selected</h3>";
    exit;
}

/* ===============================
   Fetch rooms
================================ */
$sql = "SELECT 
            room_id,
            room_no,
            room_type,
            price_per_day,
            room_status
        FROM rooms
        WHERE floor_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $floor_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Rooms</title>
    <link rel="stylesheet" href="../../assets/css/common.css">
    <link rel="stylesheet" href="../../user/rooms/rooms.css">
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="container">

    <div class="page-header">
        <h2>Rooms</h2>
        <a href="../floors/floors.php" class="btn">Back</a>
    </div>

    <p class="sub-text">Select a room from this floor</p>

    <div class="room-grid">

        <?php if ($result->num_rows > 0) { ?>
            <?php while ($room = $result->fetch_assoc()) { ?>

                <?php
                    $status = strtolower($room['room_status']);
                    $statusClass = ($status === 'available') ? 'available' : 'booked';
                ?>

                <div class="room-card">
                    <h3>Room <?= htmlspecialchars($room['room_no']); ?></h3>

                    <p><?= htmlspecialchars($room['room_type']); ?></p>

                    <p>₹<?= number_format($room['price_per_day']); ?> / day</p>

                    <span class="status <?= $statusClass; ?>">
                        <?= ucfirst($room['room_status']); ?>
                    </span>

                    <?php if ($status === 'available') { ?>
                        <a href="room_details.php?room_id=<?= $room['room_id']; ?>" class="details-btn">
                            View Details
                        </a>
                    <?php } else { ?>
                        <span class="details-btn disabled">Booked</span>
                    <?php } ?>
                </div>

            <?php } ?>
        <?php } else { ?>
            <p>No rooms available for this floor.</p>
        <?php } ?>

    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>
