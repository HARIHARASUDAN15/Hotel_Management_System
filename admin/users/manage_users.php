<?php
session_start();
include '../../config/db.php';

// Admin check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

// Handle user deletion
$success_msg = $error_msg = "";
if (isset($_GET['delete'])) {
    $delete_id = (int)$_GET['delete'];

    if ($delete_id == $_SESSION['user_id']) {
        $error_msg = "You cannot delete your own account!";
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id=?");
        $stmt->bind_param("i", $delete_id);
        if ($stmt->execute()) {
            $success_msg = "User deleted successfully!";
        } else {
            $error_msg = "Error deleting user: " . $conn->error;
        }
        $stmt->close();
    }
}

// Fetch users
$sql = "SELECT id, name, email, role FROM users ORDER BY id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users</title>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="users.css">
</head>
<body>

<div class="container">
    <h2>⭐ Manage Users</h2>

    <?php if($success_msg): ?>
        <p class="success-msg"><?= htmlspecialchars($success_msg) ?></p>
    <?php endif; ?>
    <?php if($error_msg): ?>
        <p class="error-msg"><?= htmlspecialchars($error_msg) ?></p>
    <?php endif; ?>

    <div class="buttons">
        <button onclick="window.location.href='../dashboard.php'" class="btn back-btn">← Back</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td class="<?= $row['role'] ?>"><?= ucfirst($row['role']) ?></td>
                <td class="actions">
                    <a href="manage_users.php?delete=<?= $row['id'] ?>" title="Delete" onclick="return confirm('Are you sure you want to delete this user?');">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
