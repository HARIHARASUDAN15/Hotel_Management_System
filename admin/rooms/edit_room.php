<?php
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';

if (!isset($_GET['id'])) {
    die("Room ID missing");
}

$msg = "";
$room_id = $_GET['id'];

/* Fetch room */
$stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id=?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();

if (!$room) {
    die("Room not found");
}

/* Fetch floors */
$floors = $conn->query("SELECT * FROM floors");

/* Update room */
if (isset($_POST['submit'])) {
    $room_no = $_POST['room_no'];
    $floor_id = $_POST['floor_id'];
    $room_type = $_POST['room_type'];
    $room_status = $_POST['room_status'];

    $stmt = $conn->prepare(
        "UPDATE rooms SET room_no=?, floor_id=?, room_type=?, room_status=? WHERE room_id=?"
    );
    $stmt->bind_param("sissi", $room_no, $floor_id, $room_type, $room_status, $room_id);

    if ($stmt->execute()) {
        header("Location: manage_rooms.php?success=Room updated successfully");
        exit;
    } else {
        $msg = "Error updating room!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Room</title>
    <link rel="stylesheet" href="../rooms/add_room.css">
</head>

<body>

<div class="container">
    <h2>Edit Room</h2>

    <?php if ($msg != "") { ?>
        <p class="error-msg"><?php echo $msg; ?></p>
    <?php } ?>

    <form method="post">

        <label>Room Number</label>
        <input type="text" name="room_no" value="<?php echo $room['room_no']; ?>" required>

        <label>Floor</label>
        <select name="floor_id" required>
            <?php while ($f = $floors->fetch_assoc()) { ?>
                <option value="<?php echo $f['floor_id']; ?>"
                    <?php if ($f['floor_id'] == $room['floor_id']) echo "selected"; ?>>
                    <?php echo $f['floor_name']; ?>
                </option>
            <?php } ?>
        </select>

        <label>Room Type</label>
        <input type="text" name="room_type" value="<?php echo $room['room_type']; ?>" required>

        <label>Status</label>
        <select name="room_status">
            <option value="Available" <?php if ($room['room_status']=="Available") echo "selected"; ?>>Available</option>
            <option value="Booked" <?php if ($room['room_status']=="Booked") echo "selected"; ?>>Booked</option>
            <option value="Maintenance" <?php if ($room['room_status']=="Maintenance") echo "selected"; ?>>Maintenance</option>
        </select>

        <button type="submit" name="submit">Update Room</button>

    </form>
</div>

</body>
</html>
