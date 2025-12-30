<?php
require_once __DIR__ . '/../includes/admin_auth.php';
require_once __DIR__ . '/../../config/db.php';

$msg = "";

// Check if floors exist
$floors = $conn->query("SELECT * FROM floors");
if ($floors->num_rows == 0) {
    die("<p>Please add a floor first! <a href='../floors/add_floor.php'>Add Floor</a></p>");
}

if (isset($_POST['submit'])) {
    $room_no = $_POST['room_no'];
    $floor_id = $_POST['floor_id'];
    $room_type = $_POST['room_type'];
    $room_status = $_POST['room_status'];

    // Duplicate room number check
    $check = $conn->prepare("SELECT * FROM rooms WHERE room_no = ?");
    $check->bind_param("s", $room_no);
    $check->execute();
    $res = $check->get_result();

    if ($res->num_rows > 0) {
        $msg = "Error: Room number $room_no already exists!";
    } else {
        // Count rooms in this floor
        $count_floor = $conn->prepare("SELECT COUNT(*) as total FROM rooms WHERE floor_id = ?");
        $count_floor->bind_param("i", $floor_id);
        $count_floor->execute();
        $count_res = $count_floor->get_result()->fetch_assoc();

        if ($count_res['total'] >= 10) {
            $msg = "Error: This floor already has 10 rooms. Cannot add more!";
        } else {
            $stmt = $conn->prepare("INSERT INTO rooms (room_no, floor_id, room_type, room_status) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siss", $room_no, $floor_id, $room_type, $room_status);
            if ($stmt->execute()) {
                header("Location: manage_rooms.php?success=Room added successfully");
                exit;
            } else {
                $msg = "Error adding room!";
            }
        }
    }
}
?>
<head>
    <link rel="stylesheet" href="../rooms/add_room.css">
</head>
<div class="container">
    <h2>Add Room</h2>
    <?php if($msg != "") { echo "<p class='error-msg'>$msg</p>"; } ?>
    <form method="POST">
        <label>Room Number</label>
        <input type="text" name="room_no" placeholder="101" required>

        <label>Floor</label>
        <select name="floor_id" required>
            <?php while($floor = $floors->fetch_assoc()) {
                echo "<option value='".$floor['floor_id']."'>".$floor['floor_name']."</option>";
            } ?>
        </select>

        <label>Room Type</label>
        <select name="room_type" required>
            <option value="">Select Room Type</option>
            <option value="Deluxe">Deluxe</option>
            <option value="Standard">Standard</option>
            <option value="Suite">Suite</option>
            <option value="Executive">Executive</option>
        </select>

        <label>Room Status</label>
        <select name="room_status" required>
            <option value="">Select Room Status</option>
            <option value="Available">Available</option>
            <option value="Occupied">Occupied</option>
            <option value="Maintenance">Under Maintenance</option>
        </select>

        <button type="submit" name="submit">Add Room</button>
    </form>

    <a href="../dashboard.php" class="back-btn">← Back</a>
</div>
