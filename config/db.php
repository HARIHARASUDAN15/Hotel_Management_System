<?php
$host = "localhost";
$user = "root";
$pass = ""; // your DB password
$db   = "hotel_db";

$conn = mysqli_connect($host, $user, $pass, $db);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Optional: create default admin if not exists
$checkAdmin = mysqli_query($conn, "SELECT * FROM users WHERE role='admin'");
if(mysqli_num_rows($checkAdmin) == 0){
    $admin_name = "Hariharasudan";
    $admin_email = "saravananhari2006@gmai.com";
    $admin_password = password_hash("Hari2015@a", PASSWORD_DEFAULT);
    mysqli_query($conn, "INSERT INTO users (name,email,password,role) VALUES ('$admin_name','$admin_email','$admin_password','admin')");
}
?>