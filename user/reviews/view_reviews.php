<?php
session_start();
require_once "../../config/db.php";
include __DIR__ . '/../../includes/navbar.php';
?>

<link rel="stylesheet" href="../../assets/css/common.css">
<link rel="stylesheet" href="../reviews/view_reviews.css">


<div class="main-content">
    <div class="review-container">
        <h2 class="page-title">Guest Reviews</h2>

        <?php
        // ✅ Fetch only approved reviews (prepared statement)
        $stmt = $conn->prepare("
            SELECT guest_name, room_type, rating, review_message, created_at
            FROM reviews
            WHERE status = 1
            ORDER BY created_at DESC
        ");
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0):
            while ($row = $result->fetch_assoc()):
        ?>
                <div class="review-card">
                    <div class="review-header">
                        <div>
                            <span class="user-name">
                                <?= htmlspecialchars($row['guest_name']) ?>
                            </span>
                            <span class="room-type">
                                <?= htmlspecialchars($row['room_type']) ?>
                            </span>
                        </div>

                        <div class="rating">
                            ⭐ <?= (int)$row['rating'] ?>/5
                        </div>
                    </div>

                    <p class="review-text">
                        <?= nl2br(htmlspecialchars($row['review_message'])) ?>
                    </p>

                    <div class="review-date">
                        <?= date("d M Y", strtotime($row['created_at'])) ?>
                    </div>
                </div>
        <?php
            endwhile;
        else:
        ?>
            <p class="no-review">No reviews available yet.</p>
        <?php
        endif;
        $stmt->close();
        ?>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
