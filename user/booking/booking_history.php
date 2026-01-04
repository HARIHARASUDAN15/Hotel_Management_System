<?php
session_start();
include "../../config/db.php";
include "../../includes/auth_check.php";

$user_id = $_SESSION['user_id'];

$bookings = mysqli_query($conn, "SELECT b.*, r.room_number, r.room_type 
                                FROM bookings b 
                                JOIN rooms r ON b.room_id=r.id 
                                WHERE b.user_id='$user_id' 
                                ORDER BY b.created_at DESC");
?>

<head><link rel="stylesheet" href="booking.css"></head>
<h2>My Bookings</h2>
<table border="1" cellpadding="8" cellspacing="0">
<tr>
    <th>Booking ID</th>
    <th>Room</th>
    <th>Check In</th>
    <th>Check Out</th>
    <th>Amount</th>
    <th>Status</th>
    <th>Invoice</th>
</tr>
<?php while($row = mysqli_fetch_assoc($bookings)){ ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['room_number']." - ".$row['room_type'] ?></td>
    <td><?= $row['check_in'] ?></td>
    <td><?= $row['check_out'] ?></td>
    <td>₹<?= $row['total_amount'] ?></td>
    <td><?= ucfirst($row['booking_status']) ?></td>
    <td>
        <?php if($row['booking_status']=="paid"){ ?>
            <a href="generate_invoice.php?booking_id=<?= $row['id'] ?>" target="_blank">Download</a>
        <?php } else { echo "-"; } ?>
    </td>
</tr>
<?php } ?>
</table>