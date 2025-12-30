<?php
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';

// Handle delete action
if (isset($_GET['delete'])) {
    $floor_id = (int)$_GET['delete']; // sanitize input
    $stmt = $conn->prepare("DELETE FROM floors WHERE floor_id = ?");
    $stmt->bind_param("i", $floor_id);

    if ($stmt->execute()) {
        $stmt->close();
        header("Location: manage_floors.php?success=Floor+deleted+successfully");
        exit;
    } else {
        $error_msg = "Failed to delete floor: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Floors | Admin</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../floors/manage_floors.css">
    <link rel="stylesheet" href="../includes/navbar_admin.css">
</head>
<body>
<?php include __DIR__ . '/../includes/navbar_admin.php'; ?>

<div class="container">
    <h2>Manage Floors</h2>

    <?php 
    if (isset($_GET['success'])) {
        echo "<p class='success-msg'>".htmlspecialchars($_GET['success'])."</p>";
    }
    if (isset($error_msg)) {
        echo "<p class='error-msg'>".htmlspecialchars($error_msg)."</p>";
    }
    ?>

    <div class="buttons">
        <a href="add_floor.php" class="btn">➕ Add New Floor</a>
        <button onclick="window.location.href='../dashboard.php'" class="btn back-btn">← Back</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Floor Name</th>
                <th>Total Rooms</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $floors = $conn->query("SELECT * FROM floors");
        if ($floors && $floors->num_rows > 0) {
            while ($row = $floors->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['floor_id']) ?></td>
                    <td><?= htmlspecialchars($row['floor_name']) ?></td>
                    <td><?= htmlspecialchars($row['total_rooms']) ?></td>
                    <td class="actions">
                        <a href="edit_floor.php?id=<?= $row['floor_id'] ?>" title="Edit"><i class="fas fa-edit"></i></a>
                        <a href="manage_floors.php?delete=<?= $row['floor_id'] ?>" title="Delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash-alt"></i></a>
                    </td>
                </tr>
            <?php endwhile;
        } else {
            echo "<tr><td colspan='4'>No floors found</td></tr>";
        } ?>
        </tbody>
    </table>
</div>
</body>
</html>
