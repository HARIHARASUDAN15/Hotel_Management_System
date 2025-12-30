<?php
session_start();
include '../../config/db.php';

// Admin check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$sql = "
SELECT 
    b.id,
    b.check_in,
    b.check_out,
    b.total_amount,
    b.booking_status,
    b.created_at,
    u.name AS user_name,
    r.room_no AS room_number,
    r.room_type,
    r.room_status
FROM bookings b
JOIN users u ON b.user_id = u.id
JOIN rooms r ON b.room_id = r.room_id
ORDER BY b.created_at DESC
";

$result = $conn->query($sql);


if (!$result) {
    die("SQL Error: " . $conn->error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Bookings</title>
    <link rel="stylesheet" href="booking.css">
</head>
<body>
    
<button onclick="window.location.href='../dashboard.php'" class="btn back-btn">← Back</button>
<h2>All Bookings</h2>
<table>
    <tr>
        <th>ID</th>
        <th>User</th>
        <th>Room</th>
        <th>Check In</th>
        <th>Check Out</th>
        <th>Amount</th>
        <th>Status</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['user_name'] ?></td>
        <td><?= $row['room_number'] ?></td>
        <td><?= $row['check_in'] ?></td>
        <td><?= $row['check_out'] ?></td>
        <td>₹<?= $row['total_amount'] ?></td>
        <td><?= $row['booking_status'] ?></td>
        <td>
            <a href="booking_details.php?id=<?= $row['id'] ?>" class="btn">View</a>
        </td>
    </tr>
    <?php } ?>
</table>

</body>
</html>
