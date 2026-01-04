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

    $room_no       = $_POST['room_no'];
    $floor_id      = (int)$_POST['floor_id'];
    $room_type     = $_POST['room_type'];
    $room_status   = $_POST['room_status'];
    $price_per_day = (float)$_POST['price_per_day'];

    if ($price_per_day <= 0) {
        $msg = "Error: Price per day must be greater than 0!";
    } else {

        // Duplicate room number check
        $check = $conn->prepare("SELECT room_id FROM rooms WHERE room_no = ?");
        $check->bind_param("s", $room_no);
        $check->execute();
        $res = $check->get_result();

        if ($res->num_rows > 0) {
            $msg = "Error: Room number $room_no already exists!";
        } else {

            // Count rooms in this floor
            $count_floor = $conn->prepare(
                "SELECT COUNT(*) as total FROM rooms WHERE floor_id = ?"
            );
            $count_floor->bind_param("i", $floor_id);
            $count_floor->execute();
            $count_res = $count_floor->get_result()->fetch_assoc();

            if ($count_res['total'] >= 10) {
                $msg = "Error: This floor already has 10 rooms. Cannot add more!";
            } else {

                // INSERT room WITH price_per_day
                $stmt = $conn->prepare(
                    "INSERT INTO rooms 
                    (room_no, floor_id, room_type, room_status, price_per_day) 
                    VALUES (?, ?, ?, ?, ?)"
                );

                $stmt->bind_param(
                    "sissd",
                    $room_no,
                    $floor_id,
                    $room_type,
                    $room_status,
                    $price_per_day
                );

                if ($stmt->execute()) {
                    header("Location: manage_rooms.php?success=Room added successfully");
                    exit;
                } else {
                    $msg = "Error adding room!";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Room</title>
    <link rel="stylesheet" href="../rooms/add_room.css">
</head>
<body>

<div class="container">
    <h2>Add Room</h2>

    <?php if($msg != "") { ?>
        <p class="error-msg"><?= $msg ?></p>
    <?php } ?>

    <form method="POST">

        <label>Room Number</label>
        <input type="text" name="room_no" placeholder="101" required>

        <label>Floor</label>
        <select name="floor_id" required>
            <?php while($floor = $floors->fetch_assoc()) { ?>
                <option value="<?= $floor['floor_id'] ?>">
                    <?= htmlspecialchars($floor['floor_name']) ?>
                </option>
            <?php } ?>
        </select>

        <label>Room Type</label>
        <select name="room_type" id="room_type" required onchange="updatePrice()">
            <option value="">Select Room Type</option>
            <option value="Standard">Standard</option>
            <option value="Deluxe">Deluxe</option>
            <option value="Suite">Suite</option>
            <option value="Executive">Executive</option>
        </select>

        <!-- 🔥 NEW FIELD -->
        <label>Price / Day (₹)</label>
        <input type="number" name="price_per_day" id="price_per_day" step="0.01" placeholder="1500" readonly>

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

<!-- JavaScript for Auto Price -->
<script>
function updatePrice() {
    const roomType = document.getElementById("room_type").value;
    const priceField = document.getElementById("price_per_day");

    // Price mapping
    const prices = {
        "Standard": 1500,
        "Deluxe": 2500,
        "Suite": 4000,
        "Executive": 3500
    };

    if (prices[roomType]) {
        priceField.value = prices[roomType];
    } else {
        priceField.value = "";
    }
}
</script>

</body>
</html>
