<?php
session_start();
require_once _DIR_ . '/../includes/auth_check.php';
require_once _DIR_ . '/../config/db.php';

$user_id = $_SESSION['user_id'];

// fetch user details
$sql = "SELECT username, email, created_at FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>My Profile</title>
    <link rel="stylesheet" href="user.css">
</head>
<body>

<?php include _DIR_ . '/../includes/navbar.php'; ?>

<div class="container">
    <h2>👤 My Profile</h2>

    <div class="profile-card">
        <p><strong>Username:</strong> <?php echo $user['username']; ?></p>
        <p><strong>Email:</strong> <?php echo $user['email']; ?></p>
        <p><strong>Joined On:</strong> <?php echo date("d M Y", strtotime($user['created_at'])); ?></p>

        <a href="../auth/logout.php" class="logout-btn">Logout</a>
    </div>
</div>

<?php include _DIR_ . '/../includes/footer.php'; ?>

</body>
</html>