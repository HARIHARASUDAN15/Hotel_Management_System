<?php
session_start();
require_once __DIR__ . '/../includes/user_auth.php';
require_once __DIR__ . '/../config/db.php';

$user_id = $_SESSION['user_id'];

// fetch user details
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Profile</title>
    <link rel="stylesheet" href="../user/user.css">
    <link rel="stylesheet" href="../assets/css/common.css"> <!-- Navbar CSS -->
    <link rel="stylesheet" href="/admin/floors/manage_floors.css"> <!-- Footer CSS -->
</head>
<body>

<?php include __DIR__ . '/../includes/navbar.php'; ?>

<div class="container">
    <div class="profile-card">
        <h2>👤 My Profile</h2>

        

        <!-- Table-like user info -->
        <div class="profile-info">
            <div class="info-row"><span class="label">Name:</span> <span class="value"><?php echo htmlspecialchars($user['name']); ?></span></div>
            <div class="info-row"><span class="label">Email:</span> <span class="value"><?php echo htmlspecialchars($user['email']); ?></span></div>
            <div class="info-row"><span class="label">Age:</span> <span class="value"><?php echo htmlspecialchars($user['age']); ?></span></div>
            <div class="info-row"><span class="label">Mobile:</span> <span class="value"><?php echo htmlspecialchars($user['mobile']); ?></span></div>
            <div class="info-row"><span class="label">Proof Type:</span> <span class="value"><?php echo htmlspecialchars($user['proof_type']); ?></span></div>

            <?php if(!empty($user['proof_image'])): ?>
                <div class="info-row">
                    <span class="label">Proof Image:</span> 
                    <span class="value"><img src="../../assets/images/proofs/<?php echo $user['proof_image']; ?>" class="proof-img"></span>
                </div>
            <?php endif; ?>

            <div class="info-row"><span class="label">Proof Number:</span> <span class="value"><?php echo htmlspecialchars($user['proof_number']); ?></span></div>
            <div class="info-row"><span class="label">Role:</span> <span class="value"><?php echo htmlspecialchars($user['role']); ?></span></div>
            <div class="info-row"><span class="label">Status:</span> <span class="value"><?php echo htmlspecialchars($user['status']); ?></span></div>
            <div class="info-row"><span class="label">Joined On:</span> <span class="value"><?php echo date("d M Y", strtotime($user['created_at'])); ?></span></div>

            <?php if(!empty($user['proof_doc'])): ?>
                <div class="info-row">
                    <span class="label">Proof Document:</span> 
                    <span class="value"><a href="../../assets/images/proofs/<?php echo $user['proof_doc']; ?>" target="_blank">View Document</a></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="buttons">
        <button onclick="window.location.href='home.php'" class="btn back-btn">← Back</button>
    </div>
</div>


<?php include __DIR__ . '/../includes/footer.php'; ?>

</body>
</html>
