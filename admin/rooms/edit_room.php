<?php
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';

if (!isset($_GET['id'])) {
    die("Room ID missing");
}

$msg = "";
$room_id = (int)$_GET['id'];

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

/* Update room (INCLUDING price_per_day) */
if (isset($_POST['submit'])) {

    $room_no       = $_POST['room_no'];
    $floor_id      = (int)$_POST['floor_id'];
    $room_type     = $_POST['room_type'];
    $room_status   = $_POST['room_status'];
    $price_per_day = (float)$_POST['price_per_day'];

    if ($price_per_day <= 0) {
        $msg = "Price per day must be greater than 0";
    } else {

        $stmt = $conn->prepare(
            "UPDATE rooms 
             SET room_no=?, floor_id=?, room_type=?, room_status=?, price_per_day=? 
             WHERE room_id=?"
        );

        $stmt->bind_param(
            "sissdi",
            $room_no,
            $floor_id,
            $room_type,
            $room_status,
            $price_per_day,
            $room_id
        );

        if ($stmt->execute()) {
            header("Location: manage_rooms.php?success=Room updated successfully");
            exit;
        } else {
            $msg = "Error updating room!";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Room</title>
    <link rel="stylesheet" href="../rooms/add_room.css">
    <link rel="stylesheet" href="../rooms/manage_rooms.css">
    
</head>

<body>

<div class="container">
    <h2>Edit Room</h2>

    

    <?php if ($msg != "") { ?>
        <p class="error-msg"><?php echo $msg; ?></p>
    <?php } ?>

    <form method="post">

        <label>Room Number</label>
        <input type="text" name="room_no" value="<?= htmlspecialchars($room['room_no']) ?>" required>

        <label>Floor</label>
        <select name="floor_id" required>
            <?php while ($f = $floors->fetch_assoc()) { ?>
                <option value="<?= $f['floor_id']; ?>"
                    <?php if ($f['floor_id'] == $room['floor_id']) echo "selected"; ?>>
                    <?= htmlspecialchars($f['floor_name']); ?>
                </option>
            <?php } ?>
        </select>

        <label>Room Type</label>
        <input type="text" name="room_type" value="<?= htmlspecialchars($room['room_type']) ?>" required>

        <!-- 🔥 NEW FIELD -->
        <label>Price / Day (₹)</label>
        <input type="number" name="price_per_day" step="0.01"
               value="<?= $room['price_per_day'] ?>" required>

        <label>Status</label>
        <select name="room_status">
            <option value="Available" <?= $room['room_status']=="Available" ? "selected" : "" ?>>Available</option>
            <option value="Booked" <?= $room['room_status']=="Booked" ? "selected" : "" ?>>Booked</option>
            <option value="Maintenance" <?= $room['room_status']=="Maintenance" ? "selected" : "" ?>>Maintenance</option>
        </select>

        <button type="submit" name="submit">Update Room</button>

    </form>

    <button onclick="window.location.href='../dashboard.php'" class="btn back-btn"> Back</button>
</div>

</body>
</html>
