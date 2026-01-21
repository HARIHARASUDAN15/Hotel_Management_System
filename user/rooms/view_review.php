<?php
session_start();
require_once "../../config/db.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Room Reviews</title>
    <link rel="stylesheet" href="view_review.css">
</head>
<body>

<div class="review-container">
    <h2>Room Reviews</h2>

    <?php
    $sql = "SELECT * 
            FROM reviews 
            WHERE status = 'approved'
            ORDER BY created_at DESC";

    $result = mysqli_query($conn, $sql);

    /* DEBUG SAFETY */
    if (!$result) {
        die("Query Failed: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
    ?>
        <div class="review-card">
            <div class="review-header">
                <span class="user-name">
                    <?php echo htmlspecialchars($row['guest_name']); ?>
                </span>
                <span class="rating">
                    ⭐ <?php echo (int)$row['rating']; ?>/5
                </span>
            </div>

            <p class="review-text">
                <?php echo htmlspecialchars($row['review_message']); ?>
            </p>

            <div class="review-date">
                <?php echo date("d M Y", strtotime($row['created_at'])); ?>
            </div>
        </div>
    <?php
        }
    } else {
        echo "<p class='no-review'>No reviews available.</p>";
    }
    ?>

</div>

</body>
</html>
