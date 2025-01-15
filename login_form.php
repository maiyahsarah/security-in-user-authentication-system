<?php

@include 'config.php';

session_start();

if (isset($_POST['submit'])) {

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = md5($_POST['password']); // Hash the entered password

    // Check if the user exists and fetch their details
    $check_user = "SELECT * FROM user_form WHERE email = '$email'";
    $user_result = mysqli_query($conn, $check_user);

    if (mysqli_num_rows($user_result) > 0) {
        $user_data = mysqli_fetch_assoc($user_result);
        $failed_attempts = $user_data['failed_attempts'];
        $locked_until = $user_data['locked_until'];
        $verification = $user_data['verification'];

        // Check if the account is verified
        if ($verification == 0) {
            $error[] = 'Your account is not verified. Please verify your email before logging in.';
        } else {
            // Check if the account is locked
            if ($locked_until && strtotime($locked_until) > time()) {
                $error[] = 'Account is locked. Try again later.';
            } else {
                // Attempt to login
                $select = "SELECT * FROM user_form WHERE email = '$email' AND password = '$pass'";
                $result = mysqli_query($conn, $select);

                if (mysqli_num_rows($result) > 0) {
                    $row = mysqli_fetch_array($result);

                    // Reset failed attempts upon successful login
                    $reset_attempts = "UPDATE user_form SET failed_attempts = 0, locked_until = NULL WHERE email = '$email'";
                    mysqli_query($conn, $reset_attempts);

                    // Redirect based on user type
                    if ($row['user_type'] == 'admin') {
                        $_SESSION['admin_name'] = $row['name'];
                        header('location:admin_page.php');
                        exit;
                    } elseif ($row['user_type'] == 'user') {
                        $_SESSION['user_name'] = $row['name'];
                        header('location:user_page.php');
                        exit;
                    }
                } else {
                    // Increment failed attempts
                    $failed_attempts++;
                    if ($failed_attempts >= 3) {
                        $lock_time = date('Y-m-d H:i:s', strtotime('+15 minutes')); // Lock account for 15 minutes
                        $update_attempts = "UPDATE user_form SET failed_attempts = $failed_attempts, locked_until = '$lock_time' WHERE email = '$email'";
                        $error[] = 'Too many failed attempts. Account is locked for 15 minutes.';
                    } else {
                        $update_attempts = "UPDATE user_form SET failed_attempts = $failed_attempts WHERE email = '$email'";
                        $error[] = 'Incorrect email or password!';
                    }
                    mysqli_query($conn, $update_attempts);
                }
            }
        }
    } else {
        $error[] = 'Email does not exist!';
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login Form</title>

   <!-- custom css file link -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Login Now</h3>
      <?php
      // Display errors if any
      if (isset($error)) {
         foreach ($error as $err) {
            echo '<span class="error-msg">' . $err . '</span>';
         }
      }
      ?>
      <p>Enter Your Email<sup>*</sup></p>
      <input type="email" name="email" required placeholder="Enter your email">
      <p>Enter Your Password<sup>*</sup></p>
      <input type="password" name="password" required placeholder="Enter your password">
      <input type="submit" name="submit" value="Login Now" class="form-btn">
      <p>Forgot Password? <a href="forgot_password.php">Click on this link</a></p>
      <p>Don't have an account? <a href="register_form.php">Register now</a></p>
   </form>

</div>

</body>
</html>
