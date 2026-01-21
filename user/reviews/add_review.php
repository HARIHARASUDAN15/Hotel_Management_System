<?php
session_start();
require_once "../../config/db.php";

// Initialize messages
$success = $error = "";

/* ✅ Fetch room types dynamically from DB */
$room_types = [];
$room_sql = "SELECT DISTINCT room_type FROM rooms WHERE room_type IS NOT NULL";
$room_result = $conn->query($room_sql);

if ($room_result) {
    while ($row = $room_result->fetch_assoc()) {
        $room_types[] = $row['room_type'];
    }
}

/* ✅ Handle form submission */
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $guest_name     = trim($_POST['guest_name']);
    $guest_email    = trim($_POST['guest_email']);
    $room_type      = trim($_POST['room_type']);
    $rating         = intval($_POST['rating']);
    $review_message = trim($_POST['review_message']);

    // Basic validation
    if (
        empty($guest_name) ||
        empty($guest_email) ||
        empty($room_type) ||
        $rating < 1 || $rating > 5 ||
        empty($review_message)
    ) {
        $error = "Please fill all fields correctly.";
    } else {

        /* 🔒 CHECK: one review per email */
$check_stmt = $conn->prepare(
    "SELECT review_id FROM reviews WHERE guest_email = ? LIMIT 1"
);
$check_stmt->bind_param("s", $guest_email);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    $error = "You have already submitted a review using this email.";
    $check_stmt->close();
} else {
    $check_stmt->close();

    // Insert review
    $stmt = $conn->prepare("
        INSERT INTO reviews
        (guest_name, guest_email, room_type, rating, review_message, status, created_at)
        VALUES (?, ?, ?, ?, ?, 1, NOW())
    ");

    $stmt->bind_param(
        "sssis",
        $guest_name,
        $guest_email,
        $room_type,
        $rating,
        $review_message
    );

    if ($stmt->execute()) {
        $success = "Thank you! Your review has been submitted.";
    } else {
        $error = "Failed to submit review. Try again.";
    }

    $stmt->close();
}
    }
}   
?>

<?php include __DIR__ . '/../../includes/navbar.php'; ?>

<link rel="stylesheet" href="../../admin/admin.css">
<link rel="stylesheet" href="../../assets/css/common.css">
<link rel="stylesheet" href="user.css">
<link rel="stylesheet" href="../reviews/add_review.css">

<div class="main-content">
    <div class="review-container">
        <h2>Submit Your Review</h2>

        <?php if ($success): ?>
            <p class="success-msg">
                <?= htmlspecialchars($success) ?><br>
                <small>You will be redirected to reviews page in 5 seconds...</small>
            </p>

            <!-- ✅ Auto redirect after 5 seconds -->
            <script>
                setTimeout(function () {
                    window.location.href = "view_reviews.php";
                }, 5000);
            </script>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="error-msg"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>

        <form method="POST" class="review-form">

            <label for="guest_name">Name:</label>
            <input type="text" name="guest_name" id="guest_name">

            <label for="guest_email">Email:</label>
            <input type="email" name="guest_email" id="guest_email">

            <label for="room_type">Room Type:</label>
            <select name="room_type" id="room_type">
                <option value="">Select Room Type</option>
                <?php foreach ($room_types as $type): ?>
                    <option value="<?= htmlspecialchars($type) ?>">
                        <?= htmlspecialchars($type) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label for="rating">Rating:</label>
            <select name="rating" id="rating">
                <option value="">Select rating</option>
                <option value="1">1 ⭐</option>
                <option value="2">2 ⭐</option>
                <option value="3">3 ⭐</option>
                <option value="4">4 ⭐</option>
                <option value="5">5 ⭐</option>
            </select>

            <label for="review_message">Review:</label>
            <textarea name="review_message" id="review_message"></textarea>

            <button type="submit">Submit Review</button>
        </form>
    </div>
</div>

<?php include "../../includes/footer.php"; ?>
