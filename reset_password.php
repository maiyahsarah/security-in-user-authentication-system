<?php
@include 'config.php';

if (!$conn) {
    die('<div class="error-msg">Database connection failed: ' . mysqli_connect_error() . '</div>');
}

if (isset($_GET['token'])) {
    $token = mysqli_real_escape_string($conn, $_GET['token']);

    // Verify if the token is valid and not expired
    $query = "SELECT * FROM user_form WHERE reset_token = '$token' AND token_expires > NOW()";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        // Token is valid
        if (isset($_POST['submit'])) {
            $new_password = mysqli_real_escape_string($conn, trim($_POST['password']));
            $confirm_password = mysqli_real_escape_string($conn, trim($_POST['cpassword']));

            // Validate input
            if (empty($new_password) || empty($confirm_password)) {
                echo '<div class="error-msg">Password fields cannot be empty!</div>';
            } elseif ($new_password !== $confirm_password) {
                echo '<div class="error-msg">Passwords do not match!</div>';
            } else {
                // Hash the new password and update it in the database
                $hashed_password = md5($new_password);

                $update_query = "UPDATE user_form SET password = '$hashed_password', reset_token = NULL, token_expires = NULL WHERE reset_token = '$token'";
                if (mysqli_query($conn, $update_query)) {
                    echo '<div class="success-msg">Password reset successfully! <a href="login_form.php">Login</a></div>';
                } else {
                    echo '<div class="error-msg">Failed to reset password! SQL Error: ' . mysqli_error($conn) . '</div>';
                }
            }
        }
    } else {
        echo '<div class="error-msg">Invalid or expired token!</div>';
    }
} else {
    echo '<div class="error-msg">No token provided!</div>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="form-container">
    <form action="" method="post">
        <h3>Reset Password</h3>
        <p>Enter your new password.</p>
        <input type="password" name="password" required placeholder="New password">
        <input type="password" name="cpassword" required placeholder="Confirm new password">
        <input type="submit" name="submit" value="Reset Password" class="form-btn">
    </form>
</div>
</body>
</html>
