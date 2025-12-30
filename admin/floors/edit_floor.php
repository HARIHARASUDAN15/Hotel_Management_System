
<?php
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';


$msg = "";
$floor_id = $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM floors WHERE floor_id = ?");
$stmt->bind_param("i", $floor_id);
$stmt->execute();
$result = $stmt->get_result();
$floor = $result->fetch_assoc();

if (!$floor) {
    die("Floor not found");
}

if (isset($_POST['submit'])) {
    $floor_name = $_POST['floor_name'];
    $total_rooms = $_POST['total_rooms'];

    $stmt = $conn->prepare("UPDATE floors SET floor_name=?, total_rooms=? WHERE floor_id=?");
    $stmt->bind_param("sii", $floor_name, $total_rooms, $floor_id);
    if ($stmt->execute()) {
        header("Location: manage_floors.php?success=Floor updated successfully");
    } else {
        $msg = "Error updating floor!";
    }
}
?>
<html>
<head>
    <link rel="stylesheet" href="../floors/edit_floor.css"> <!-- Your CSS -->
</head>
<div class="container">
    <h2>Edit Floor</h2>
    <?php if($msg != "") { echo "<p class='error-msg'>$msg</p>"; } ?>
    <form method="POST">
        <label>Floor Name</label>
        <input type="text" name="floor_name" value="<?= $floor['floor_name'] ?>" required>

        <label>Total Rooms</label>
        <input type="number" name="total_rooms" value="<?= $floor['total_rooms'] ?>" required>

        <button type="submit" name="submit">Update Floor</button>
    </form>
</div>
</body>
</html>