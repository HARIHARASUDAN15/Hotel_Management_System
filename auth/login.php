<?php
session_start();
require_once(__DIR__ . '/../config/db.php');

$msg = "";
$msg_type = ""; // success | error

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email == "" || $password == "") {
        $msg = "Please fill all fields";
        $msg_type = "error";
    } else {
        // Prepare statement to avoid SQL injection
        $stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) == 1) {
            $user = mysqli_fetch_assoc($result);

            // Verify password
            if (password_verify($password, $user['password'])) {

                // ✅ Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name']    = $user['name'];
                $_SESSION['role']    = strtolower($user['role']); // admin | worker | customer

                // ✅ Role-based redirect
                switch ($_SESSION['role']) {
                    case 'admin':
                        header("Location: ../admin/dashboard.php");
                        exit;
                    case 'worker':
                        header("Location: ../worker/dashboard.php");
                        exit;
                    case 'customer':
                        header("Location: ../user/home.php");
                        exit;
                    default:
                        // Invalid role
                        $msg = "Invalid role. Contact admin.";
                        $msg_type = "error";
                        session_destroy();
                }
            } else {
                $msg = "Invalid password";
                $msg_type = "error";
            }
        } else {
            $msg = "User not found. Please register.";
            $msg_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | SDET Hotel</title>
    <link rel="stylesheet" href="login.css">
</head>
<body>

<?php if ($msg != ""): ?>
<div class="notification <?php echo $msg_type; ?>">
    <?php echo $msg; ?>
</div>
<?php endif; ?>

<div class="login-box">
    <h2>Login</h2>
    <form method="post">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit" name="login">Login</button>
    </form>
    <p class="register-link">
    Don't have an account?
    <a href="register.php">
        Register here
        </a>
    </p>
    <p class="register-link">
    <a href="../index.php">
        Go back Home
        </a>
    </p>
</div>

<script>
// Auto-hide notification after 5 seconds
setTimeout(() => {
    const note = document.querySelector('.notification');
    if (note) note.style.display = 'none';
}, 5000);
</script>

</body>
</html>