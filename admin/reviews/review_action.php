<?php
include '../../config/db.php';

if (isset($_GET['action'], $_GET['id'])) {

    $id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'approve') {
        $query = "UPDATE reviews SET status='Approved' WHERE review_id=$id";
    }
    elseif ($action == 'reject') {
        $query = "UPDATE reviews SET status='Rejected' WHERE review_id=$id";
    }
    elseif ($action == 'delete') {
        $query = "DELETE FROM reviews WHERE review_id=$id";
    }

    mysqli_query($conn, $query);
}

header("Location: review.php");
exit;