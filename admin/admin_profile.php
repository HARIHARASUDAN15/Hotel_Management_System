<?php
session_start();
require_once '../config/db.php'; // database connection

// Admin login check
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Fetch admin data
$admin_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id=?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

$msg = "";
$error_msg = "";

// Handle form submission
if (isset($_POST['update'])) {
    $name   = trim($_POST['name']);
    $email  = trim($_POST['email']);
    $mobile = trim($_POST['mobile']);
    $age    = isset($_POST['age']) ? (int)trim($_POST['age']) : 0;

    // Optional password change
    $password_param = null;
    if (!empty($_POST['password'])) {
        $password_param = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }

    // Keep existing profile image and proof document
    $profile_image = $admin['profile_image'];
    $proof_doc     = $admin['proof_doc'];

    // Prepare SQL update with optional password
    if ($password_param) {
        $update_stmt = $conn->prepare("
            UPDATE users 
            SET name=?, email=?, mobile=?, age=?, password=? 
            WHERE id=?
        ");
        $update_stmt->bind_param(
            "ssissi",
            $name,
            $email,
            $mobile,
            $age,
            $password_param,
            $admin_id
        );
    } else {
        $update_stmt = $conn->prepare("
            UPDATE users 
            SET name=?, email=?, mobile=?, age=? 
            WHERE id=?
        ");
        $update_stmt->bind_param(
            "sssii",
            $name,
            $email,
            $mobile,
            $age,
            $admin_id
        );
    }

    if ($update_stmt->execute()) {
        $msg = "Profile updated successfully";
        $_SESSION['name'] = $name; // Update session name

        // Update local $admin array for showing updated data
        $admin['name']   = $name;
        $admin['email']  = $email;
        $admin['mobile'] = $mobile;
        $admin['age']    = $age;
    } else {
        $error_msg = "Update failed: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Profile | SDET Hotel</title>
    <link rel="stylesheet" href="../admin/admin_profile.css">
    <link rel="stylesheet" href="../assets/css/navbar_admin.css"> <!-- Navbar CSS -->
</head>
<body>



<div class="container">
    <h2>Admin Profile</h2>
    <a href="dashboard.php" class="btn-back-topright">← Go Back</a>

    <?php if ($msg) { echo "<p class='success'>".htmlspecialchars($msg)."</p>"; } ?>
    <?php if ($error_msg) { echo "<p class='error-msg'>".htmlspecialchars($error_msg)."</p>"; } ?>

    <form method="post">
        <label>Name</label>
        <input type="text" name="name" value="<?php echo htmlspecialchars($admin['name']); ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>" required>

        <label>Mobile</label>
        <input type="text" name="mobile" value="<?php echo htmlspecialchars($admin['mobile']); ?>" required>

        <label>Age</label>
        <input type="number" name="age" value="<?php echo htmlspecialchars($admin['age']); ?>">

        <label>Password (leave blank to keep current)</label>
        <input type="password" name="password">

        <button type="submit" name="update">Update Profile</button>
    </form>
</div>



</body>
</html>
