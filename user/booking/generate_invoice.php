<?php
session_start();
include "../../config/db.php";
include "../../includes/auth_check.php";
require('../../assets/fpdf/fpdf.php');

if(!isset($_GET['booking_id'])){
    die("Invalid request");
}

$booking_id = $_GET['booking_id'];

// Fetch booking info
$booking_res = mysqli_query($conn, "SELECT b.*, r.room_number, r.room_type, u.name, u.email 
                                   FROM bookings b 
                                   JOIN rooms r ON b.room_id=r.id
                                   JOIN users u ON b.user_id=u.id
                                   WHERE b.id='$booking_id'");
$booking = mysqli_fetch_assoc($booking_res);

if(!$booking){
    die("Booking not found!");
}

// Create PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

$pdf->Cell(0,10,"Hotel Booking Invoice",0,1,'C');
$pdf->Ln(5);

$pdf->SetFont('Arial','',12);
$pdf->Cell(50,10,"Invoice ID: ",0,0);
$pdf->Cell(0,10,$booking['id'],0,1);

$pdf->Cell(50,10,"Name: ",0,0);
$pdf->Cell(0,10,$booking['name'],0,1);

$pdf->Cell(50,10,"Email: ",0,0);
$pdf->Cell(0,10,$booking['email'],0,1);

$pdf->Cell(50,10,"Room: ",0,0);
$pdf->Cell(0,10,$booking['room_number']." - ".$booking['room_type'],0,1);

$pdf->Cell(50,10,"Check In: ",0,0);
$pdf->Cell(0,10,$booking['check_in'],0,1);

$pdf->Cell(50,10,"Check Out: ",0,0);
$pdf->Cell(0,10,$booking['check_out'],0,1);

$pdf->Cell(50,10,"Amount Paid: ",0,0);
$pdf->Cell(0,10,"₹".$booking['total_amount'],0,1);

$pdf->Cell(50,10,"Payment Status: ",0,0);
$pdf->Cell(0,10,ucfirst($booking['booking_status']),0,1);

$pdf->Output("I","Invoice_".$booking['id'].".pdf");
?>

<head><link rel="stylesheet" href="booking.css"></head>