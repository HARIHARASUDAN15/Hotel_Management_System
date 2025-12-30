<?php
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';

$msg = "";
$success = "";
$floor_name = "";
$total_rooms = "";

if (isset($_POST['submit'])) {
    // Get and trim form inputs
    $floor_name = trim($_POST['floor_name']);
    $total_rooms = trim($_POST['total_rooms']);

    // Validation
    if ($floor_name === "" || $total_rooms === "") {
        $msg = "All fields are required!";
    } else {
        // Check if floor name already exists
        $check = $conn->prepare("SELECT floor_id FROM floors WHERE floor_name = ?");
        $check->bind_param("s", $floor_name);
        $check->execute();
        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {
            $msg = "Floor name already exists!";
        } else {
            // Insert new floor
            $stmt = $conn->prepare("INSERT INTO floors (floor_name, total_rooms) VALUES (?, ?)");
            $stmt->bind_param("si", $floor_name, $total_rooms);

            if ($stmt->execute()) {
        header("Location: manage_floors.php?success=Floor added successfully");
    } else {
        $msg = "Error adding floor!";
    }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Floor | Admin</title>
    <link rel="stylesheet" href="../floors/add_floor.css">
</head>
<body>

<div class="container">
    <h2>Add Floor</h2>

    <?php if($msg != "") { echo "<p class='error-msg'>$msg</p>"; } ?>
    <?php if($success != "") { echo "<p class='success-msg'>$success</p>"; } ?>

    <!-- Add Floor Form -->
    <form method="POST">
        <label>Floor Name</label>
        <input type="text" name="floor_name" placeholder="Floor 1"
               value="<?php echo htmlspecialchars($floor_name); ?>" required>

        <label>Total Rooms</label>
        <input type="number" name="total_rooms" placeholder="5"
               value="<?php echo htmlspecialchars($total_rooms); ?>" required>

        <button type="submit" name="submit">Add Floor</button>

        <p class="back-link">
    Go back <a href="manage_floors.php">Manage Floors</a>
</p>

    </form>
</div>

</body>
</html>
