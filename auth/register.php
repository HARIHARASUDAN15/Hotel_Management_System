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

    if ($pass != $cpass) {
        $msg = "Passwords do not match";
    } else {

        if (!isset($_FILES['proof_image']) || $_FILES['proof_image']['error'] != 0) {
            $msg = "Proof image required";
        } else {

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

                // check email
                $check = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
                if (mysqli_num_rows($check) > 0) {
                    $msg = "Email already registered";
                } else {

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

    <?php if ($msg) { ?>
        <p class="error"><?php echo $msg; ?></p>
    <?php } ?>

    <form method="post" enctype="multipart/form-data">

        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="number" name="age" placeholder="Age" required>
        <input type="text" name="mobile" placeholder="Mobile Number" required>

        <select name="proof_type" onchange="showProof()" required>
            <option value="">Select ID Proof</option>
            <option value="Aadhar">Aadhar</option>
            <option value="Voter">Voter ID</option>
            <option value="Passport">Passport</option>
        </select>

        <input type="text" name="proof_number" id="proofNumber"
               placeholder="Enter Proof Number" style="display:none;" required>

        <input type="file" name="proof_image" accept="image/*" required>

        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm Password" required>

        <button type="submit" name="register">Register</button>
    </form>

    <p>Already have an account? <a href="login.php">Login</a></p>
</div>

<script>
function showProof() {
    document.getElementById("proofNumber").style.display = "block";
}
</script>

</body>
</html>