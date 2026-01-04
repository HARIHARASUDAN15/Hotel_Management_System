<?php
session_start();
include "../../config/db.php";
require_once '../../assets/razorpay/razorpay-php/Razorpay.php';
use Razorpay\Api\Api;

// Get POST from Razorpay
$razorpay_payment_id = $_POST['razorpay_payment_id'];
$razorpay_order_id   = $_POST['razorpay_order_id'];
$razorpay_signature  = $_POST['razorpay_signature'];

$api = new Api('YOUR_KEY_ID','YOUR_KEY_SECRET');

// Fetch booking
$booking_res = mysqli_query($conn, "SELECT * FROM bookings WHERE razorpay_order_id='$razorpay_order_id'");
$booking = mysqli_fetch_assoc($booking_res);

// Verify signature
$generated_signature = hash_hmac('sha256', $razorpay_order_id . "|" . $razorpay_payment_id, 'YOUR_KEY_SECRET');

if($generated_signature === $razorpay_signature){
    // Payment success
    mysqli_query($conn, "UPDATE bookings SET booking_status='paid', payment_id='$razorpay_payment_id' WHERE id=".$booking['id']);
    header("Location: generate_invoice.php?booking_id=".$booking['id']);
    exit;
}else{
    // Payment failed
    mysqli_query($conn, "UPDATE bookings SET booking_status='failed' WHERE id=".$booking['id']);
    echo "Payment failed!";
}
?>

<head><link rel="stylesheet" href="booking.css"></head>