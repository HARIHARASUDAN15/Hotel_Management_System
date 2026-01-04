<?php
include '../config/db.php';

$msg = "";

if (isset($_POST['register'])) {

    $name   = mysqli_real_escape_string($conn, $_POST['name']);
    $email  = mysqli_real_escape_string($conn, $_POST['email']);
    $age    = mysqli_real_escape_string($conn, $_POST['age']);
    $mobile = mysqli_real_escape_string($conn, $_POST['mobile']);
    $ptype  = mysqli_real_escape_string($conn, $_POST['proof_type']);
    $pno    = mysqli_real_escape_string($conn, $_POST['proof_number']);
    $pass   = $_POST['password'];
    $cpass  = $_POST['confirm_password'];

    // default values
    $role   = 'user';
    $status = 'active';
    $created_at = date('Y-m-d H:i:s');
    $profile_image = NULL;
    $proof_doc = NULL;

    // ===== Validation =====
    if (
        empty($name) || empty($email) || empty($age) || empty($mobile) ||
        empty($ptype) || empty($pno) || empty($pass) || empty($cpass) ||
        !isset($_FILES['proof_image']) || $_FILES['proof_image']['error'] != 0
    ) {
        $msg = "Please fill all fields";
    } elseif ($pass != $cpass) {
        $msg = "Passwords do not match";
    } else {
        // ===== File Upload =====
        $img  = $_FILES['proof_image']['name'];
        $size = $_FILES['proof_image']['size'];
        $tmp  = $_FILES['proof_image']['tmp_name'];

        if ($size > 2097152) {
            $msg = "Image must be less than 2MB";
        } else {

            $folder = "../assets/images/proofs/";
            if (!is_dir($folder)) {
                mkdir($folder, 0777, true);
            }

            $img_name = time() . "_" . basename($img);
            move_uploaded_file($tmp, $folder . $img_name);

            $password = password_hash($pass, PASSWORD_DEFAULT);

            // ===== Check if email already exists =====
            $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
            if (mysqli_num_rows($check) > 0) {
                $msg = "Email already registered";
            } else {

                // ===== Insert into DB =====
                $query = "INSERT INTO users
                    (name, email, age, mobile, proof_type, proof_number, proof_image,
                     password, role, status, created_at, profile_image, proof_doc)
                    VALUES
                    ('$name', '$email', '$age', '$mobile', '$ptype', '$pno', '$img_name',
                     '$password', '$role', '$status', '$created_at', '$profile_image', '$proof_doc')";

                if (mysqli_query($conn, $query)) {
                    $msg = "Registration successful. Please login.";
                } else {
                    $msg = "Registration failed";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>User Registration</title>
    <link rel="stylesheet" href="register.css">
</head>
<body>

<div class="login-box">
    <h2>User Registration</h2>

    <?php if ($msg): ?>
        <div class="notification <?php echo ($msg === "Registration successful. Please login.") ? 'success' : 'error'; ?>">
            <?php echo $msg; ?>
        </div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data">

        <input type="text" name="name" placeholder="Full Name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>">
        <input type="email" name="email" placeholder="Email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
        <input type="number" name="age" placeholder="Age" value="<?php echo isset($age) ? htmlspecialchars($age) : ''; ?>">
        <input type="text" name="mobile" placeholder="Mobile Number" value="<?php echo isset($mobile) ? htmlspecialchars($mobile) : ''; ?>">

        <select name="proof_type" onchange="showProof()">
            <option value="">Select ID Proof</option>
            <option value="Aadhar" <?php echo (isset($ptype) && $ptype=='Aadhar')?'selected':''; ?>>Aadhar</option>
            <option value="Voter" <?php echo (isset($ptype) && $ptype=='Voter')?'selected':''; ?>>Voter ID</option>
            <option value="Passport" <?php echo (isset($ptype) && $ptype=='Passport')?'selected':''; ?>>Passport</option>
        </select>

        <input type="text" name="proof_number" id="proofNumber"
               placeholder="Enter Proof Number" style="display:<?php echo (isset($pno) && $pno!='') ? 'block' : 'none'; ?>;"
               value="<?php echo isset($pno) ? htmlspecialchars($pno) : ''; ?>">

        <input type="file" name="proof_image" accept="image/*">

        <input type="password" name="password" placeholder="Password" >
        <input type="password" name="confirm_password" placeholder="Confirm Password" >

        <button type="submit" name="register">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
</div>

<script>
function showProof() {
    const proof = document.getElementById("proofNumber");
    const select = document.querySelector("select[name='proof_type']");
    if (select.value) {
        proof.style.display = "block";
        proof.required = true;
    } else {
        proof.style.display = "none";
        proof.required = false;
    }
}

// Auto-hide notifications after 4 seconds
window.addEventListener('DOMContentLoaded', () => {
    const notification = document.querySelector('.notification');
    if (notification) {
        setTimeout(() => {
            notification.style.opacity = '0';
            notification.style.transition = 'opacity 0.5s ease';
            setTimeout(() => notification.remove(), 500);
        }, 4000);
    }
});
</script>

</body>
</html>
