<?php
session_start();
require_once "../../config/db.php";
require_once "../../includes/user_auth.php";

require_once __DIR__ . '/../../assets/razorpay/razorpay-php-master/Razorpay.php';

use Razorpay\Api\Api;

/* -------------------------
   CHECK ROOM ID
--------------------------*/
if (!isset($_GET['room_id'])) {
    die("Room ID missing!");
}

$room_id = (int)$_GET['room_id'];

/* -------------------------
   FETCH ROOM DETAILS
--------------------------*/
$stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id=?");
$stmt->bind_param("i", $room_id);
$stmt->execute();
$result = $stmt->get_result();
$room = $result->fetch_assoc();

if (!$room) {
    die("Room not found!");
}

$msg = "";

/* -------------------------
   HANDLE BOOKING
--------------------------*/
if (isset($_POST['book'])) {

    $user_id   = $_SESSION['user_id'];
    $check_in  = $_POST['check_in'];
    $check_out = $_POST['check_out'];

    /* Date validation */
    if (!$check_in || !$check_out || $check_in >= $check_out) {
        $msg = "Please select valid check-in and check-out dates.";
    } else {

        /* Calculate total amount (SERVER SIDE - SECURE) */
        $days = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);
        $total_amount = $days * $room['price_per_day'];

        /* Check room availability */
        $stmt2 = $conn->prepare(
            "SELECT room_id FROM rooms WHERE room_id=? AND room_status='Available'"
        );
        $stmt2->bind_param("i", $room_id);
        $stmt2->execute();
        $res2 = $stmt2->get_result();

        if ($res2->num_rows == 0) {
            $msg = "Sorry! This room is not available.";
        } else {

            /* Insert booking (PENDING) */
            $stmt3 = $conn->prepare(
                "INSERT INTO bookings 
                (user_id, room_id, check_in, check_out, status, created_at) 
                VALUES (?, ?, ?, ?, 'pending', NOW())"
            );
            $stmt3->bind_param("iiss", $user_id, $room_id, $check_in, $check_out);

            if ($stmt3->execute()) {

                $booking_id = $conn->insert_id;

                /* Razorpay API */
                $api = new Api(
                    "rzp_test_XXXXXXXXXX",
                    "XXXXXXXXXXXXXXX"
                );

                /* Create Razorpay Order */
                $order = $api->order->create([
                    'receipt'  => (string)$booking_id,
                    'amount'   => $total_amount * 100, // paise
                    'currency' => 'INR'
                ]);

                $razorpay_order_id = $order['id'];

                /* Save order id 
                $stmt4 = $conn->prepare(
                    "UPDATE bookings SET razorpay_order_id=? WHERE id=?"
                );
                $stmt4->bind_param("si", $razorpay_order_id, $booking_id);
                $stmt4->execute();*/

                /* Redirect to payment page */
                header("Location: pay_now.php?booking_id=$booking_id");
                exit;

            } else {
                $msg = "Booking failed! Please try again.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Book Room</title>
    <link rel="stylesheet" href="booking.css">
</head>
<body>

<div class="container">
    <h2>
        Book Room: 
        <?= htmlspecialchars($room['room_no']); ?>
        (<?= htmlspecialchars($room['room_type']); ?>)
    </h2>

    <?php if ($msg != ""): ?>
        <p class="error-msg"><?= htmlspecialchars($msg) ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Check-in Date</label>
        <input type="date" name="check_in" required>

        <label>Check-out Date</label>
        <input type="date" name="check_out" required>

        <p><b>Price / Day:</b> ₹<?= number_format($room['price_per_day'], 2) ?></p>

        <p>
            <b>Total Amount:</b> ₹
            <span id="total_amount">0.00</span>
        </p>

        <input type="hidden" name="book" value="1">

        <button type="submit">Book Now</button>

        <div class="back-link">
            <a href="../floors/floors.php" class="back-btn">← Back</a>
        </div>
    </form>
</div>

<script>
const checkIn  = document.querySelector('[name="check_in"]');
const checkOut = document.querySelector('[name="check_out"]');
const pricePerDay = <?= (float)$room['price_per_day'] ?>;
const totalSpan = document.getElementById('total_amount');

function calculateTotal() {
    const start = new Date(checkIn.value);
    const end   = new Date(checkOut.value);

    if (start && end && end > start) {
        const days = (end - start) / (1000 * 60 * 60 * 24);
        const total = days * pricePerDay;
        totalSpan.textContent = total.toFixed(2);
    } else {
        totalSpan.textContent = "0.00";
    }
}

checkIn.addEventListener('change', calculateTotal);
checkOut.addEventListener('change', calculateTotal);
</script>

</body>
</html>
