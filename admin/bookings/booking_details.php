<?php
session_start();
include '../../config/db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

if (!isset($_GET['id'])) {
    die("Invalid Booking ID");
}

$id = (int) $_GET['id'];

/* FINAL FIXED QUERY */
$sql = "
SELECT 
    b.id,
    b.user_id,
    b.room_id,
    b.check_in,
    b.check_out,
    b.total_amount,
    b.booking_status,
    b.created_at,
    u.name,
    u.email,
    r.room_no,
    r.room_type
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN rooms r ON b.room_id = r.room_id
WHERE b.id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

/* Update status */
if (isset($_POST['update'])) {
    $status = $_POST['status'];

    $up = $conn->prepare(
        "UPDATE bookings SET booking_status = ? WHERE id = ?"
    );
    $up->bind_param("si", $status, $id);
    $up->execute();

    header("Location: booking_details.php?id=$id");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Booking Details</title>
    <link rel="stylesheet" href="booking.css">
</head>
<body>

<h2>Booking Details</h2>


<div class="card">
    <p><b>User:</b> <?= $data['name'] ?> (<?= $data['email'] ?>)</p>
    <p><b>Room No:</b> <?= $data['room_no'] ?> (<?= $data['room_type'] ?>)</p>
    <p><b>Check In:</b> <?= $data['check_in'] ?></p>
    <p><b>Check Out:</b> <?= $data['check_out'] ?></p>
    <p><b>Amount:</b> ₹<?= $data['total_amount'] ?></p>
    <p><b>Status:</b> <?= $data['booking_status'] ?></p>

    <form method="post">
        <label>Status:</label>
        <select name="status">
            <option value="Booked" <?= $data['booking_status']=="Booked"?"selected":"" ?>>Booked</option>
            <option value="Checked-In" <?= $data['booking_status']=="Checked-In"?"selected":"" ?>>Checked-In</option>
            <option value="Checked-Out" <?= $data['booking_status']=="Checked-Out"?"selected":"" ?>>Checked-Out</option>
            <option value="Cancelled" <?= $data['booking_status']=="Cancelled"?"selected":"" ?>>Cancelled</option>
        </select>

        <button type="submit" name="update">Update Status</button>
    </form>
</div>

<a href="view_bookings.php" class="back">← Back</a>

</body>
</html>
