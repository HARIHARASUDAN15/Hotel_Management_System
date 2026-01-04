<?php
session_start();
require_once __DIR__ . '/../../includes/user_auth.php';
require_once __DIR__ . '/../../config/db.php';

// Fetch floors
$sql = "SELECT floor_id, floor_name, total_rooms FROM floors ORDER BY floor_id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hotel Floors</title>
    <link rel="stylesheet" href="../floors/floor_user.css">
    <link rel="stylesheet" href="../../assets/css/common.css">
</head>
<body>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<div class="container">
    <h2>🏢 Hotel Floors</h2>
    <p class="sub-text">Select a floor to view available rooms</p>

    <div class="floor-grid">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="floor-card">
                    <h3><?php echo htmlspecialchars($row['floor_name']); ?></h3>
                    <p>Total Rooms: <?php echo $row['total_rooms']; ?></p>

                    <a href="../rooms/rooms.php?floor_id=<?php echo $row['floor_id']; ?>">
                        View Rooms
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No floors available.</p>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

</body>
</html>