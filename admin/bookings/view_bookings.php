<?php
session_start();
include '../../config/db.php';

// Admin check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Correct SQL query matching standard column names
$sql = "
SELECT 
    b.id,
    b.check_in,
    b.check_out,
    b.status AS booking_status,
    b.created_at,
    u.name AS user_name,
    r.room_no AS room_number,
    r.room_type,
    r.room_status
FROM bookings AS b
INNER JOIN users AS u ON b.user_id = u.id
INNER JOIN rooms AS r ON b.room_id = r.room_id
ORDER BY b.created_at DESC
";

$result = $conn->query($sql);

// Debug SQL error
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
        <td><?= htmlspecialchars($row['user_name']) ?></td>
        <td><?= htmlspecialchars($row['room_number']) ?></td>
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
