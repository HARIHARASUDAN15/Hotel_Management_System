<?php
session_start();

/* Destroy session */
session_unset();
session_destroy();

/* Redirect to login page */
header("Location: /hotel_management_system/auth/login.php");
exit;
