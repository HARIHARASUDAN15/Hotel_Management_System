<?php
session_start();
include '../../config/db.php';

/* Admin check */
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

/* Approve review */
if (isset($_GET['approve'])) {
    $id = (int) $_GET['approve'];
    $conn->query("UPDATE reviews SET status='Approved' WHERE review_id=$id");
    header("Location: manage_reviews.php");
    exit;
}

/* Reject review */
if (isset($_GET['reject'])) {
    $id = (int) $_GET['reject'];
    $conn->query("UPDATE reviews SET status='Rejected' WHERE review_id=$id");
    header("Location: manage_reviews.php");
    exit;
}

/* Fetch reviews */
$sql = "
SELECT 
    review_id,
    guest_name,
    guest_email,
    rating,
    review_message,
    status,
    created_at
FROM reviews
ORDER BY created_at DESC
";

$result = $conn->query($sql);

if (!$result) {
    die('SQL Error: ' . $conn->error);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Reviews</title>
    <link rel="stylesheet" href="../reviews/review.css">
</head>
<body>

<div class="container">
    <h2>⭐ Manage Reviews</h2>

    <table>
        <tr>
            <th>Guest Name</th>
            <th>Guest Email</th>
            <th>Rating</th>
            <th>Review</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()) { ?>
        <tr>
            <td><?= htmlspecialchars($row['guest_name']) ?></td>
            <td><?= htmlspecialchars($row['guest_email']) ?></td>
            <td><?= (int)$row['rating'] ?>/5</td>
            <td><?= htmlspecialchars($row['review_message']) ?></td>
            <td class="<?= strtolower($row['status']) ?>">
                <?= $row['status'] ?>
            </td>
            <td>
                <?php
                /* ⭐ CORE LOGIC ⭐
                   Show Approve/Reject ONLY if:
                   - rating <= 2
                   - status is Pending
                */
                if ($row['rating'] <= 2 && $row['status'] === 'Pending') {
                ?>
                    <a class="approve" href="?approve=<?= $row['review_id'] ?>">Approve</a>
                    <a class="reject" href="?reject=<?= $row['review_id'] ?>">Reject</a>
                <?php } else { ?>
                    —
                <?php } ?>
            </td>
        </tr>
        <?php } ?>
    </table>

    <a href="../dashboard.php" class="back">← Back</a>
</div>

</body>
</html>
