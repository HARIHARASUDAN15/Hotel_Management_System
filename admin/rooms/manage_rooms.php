<?php
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';

// Delete room
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id=?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: manage_rooms.php?success=Room deleted");
    exit;
}

// Fetch rooms with floor info + price
$rooms = $conn->query("
    SELECT 
        r.room_id,
        r.room_no,
        r.room_type,
        r.room_status,
        r.price_per_day,
        f.floor_name
    FROM rooms r
    JOIN floors f ON r.floor_id = f.floor_id
    ORDER BY r.room_no ASC
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Rooms</title>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../rooms/manage_rooms.css">
    <link rel="stylesheet" href="../includes/navbar_admin.css">
</head>
<body>

<?php include __DIR__ . '/../includes/navbar_admin.php'; ?>

<div class="container">
    <h2>⭐ Manage Rooms</h2>

    <?php if(isset($_GET['success'])): ?>
        <p class="success-msg"><?= htmlspecialchars($_GET['success']) ?></p>
    <?php endif; ?>

    <div class="buttons">
        <a href="add_room.php" class="btn">➕ Add Room</a>
        <button onclick="window.location.href='../dashboard.php'" class="btn back-btn">← Back</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Room No</th>
                <th>Floor</th>
                <th>Type</th>
                <th>Price / Day (₹)</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
        <?php while($row = $rooms->fetch_assoc()): ?>
            <tr>
                <td><?= $row['room_id'] ?></td>
                <td><?= htmlspecialchars($row['room_no']) ?></td>
                <td><?= htmlspecialchars($row['floor_name']) ?></td>
                <td><?= htmlspecialchars($row['room_type']) ?></td>

                <td>
                    ₹<?= number_format($row['price_per_day'], 2) ?>
                </td>

                <td><?= htmlspecialchars($row['room_status']) ?></td>

                <td class="actions">
                    <a href="edit_room.php?id=<?= $row['room_id'] ?>" 
                       class="action-btn edit" title="Edit Room">
                        <i class="fas fa-edit"></i>
                    </a>

                    <a href="manage_rooms.php?delete=<?= $row['room_id'] ?>" 
                       class="action-btn delete"
                       title="Delete Room"
                       onclick="return confirm('Are you sure you want to delete this room?')">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
