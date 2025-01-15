<?php

@include 'config.php';
require 'vendor/autoload.php'; // Include PHPMailer

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['submit'])) {

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $password = $_POST['password'];
   $cpassword = $_POST['cpassword'];
   $user_type = $_POST['user_type'];
   $verification = 0; // Default to not verified

   if (strlen($password) < 8) {
      $error[] = 'Password must be at least 8 characters long!';
   } elseif ($password !== $cpassword) {
      $error[] = 'Passwords do not match!';
   } else {
      $pass = md5($password);

      $select = "SELECT * FROM user_form WHERE email = '$email'";
      $result = mysqli_query($conn, $select);

      if (mysqli_num_rows($result) > 0) {
         $error[] = 'User already exists!';
      } else {
         $insert = "INSERT INTO user_form(name, email, password, user_type, verification) 
                    VALUES('$name', '$email', '$pass', '$user_type', '$verification')";
         if (mysqli_query($conn, $insert)) {
            // Send Verification Email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Set your email server
                $mail->SMTPAuth = true;
                $mail->Username = 'woomay.sarathee@student.aiu.edu.my'; // Replace with your email
                $mail->Password = 'urikrwucaccdzxle'; // Replace with app password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;
        
                $mail->setFrom('woomay.sarathee@gmail.com', 'Verification');
                $mail->addAddress($email, $name);
        
                $mail->isHTML(true);
                $mail->Subject = 'Verify Your Email Address';
                $mail->Body = "Hi $name,<br><br>Thank you for registering. Please verify your email address by clicking the link below:<br>
                <a href='http://localhost/login%20system/login%20system/verify.php?email=$email'>Verify Email</a><br><br>Thank you!";
        
                $mail->send();
        
                // Inform the user
                echo "<div class='success-msg'>Verification email has been sent to <strong>$email</strong>. Please check your inbox.</div>";
                echo "<a href='login_form.php' class='form-btn'>Proceed to Login</a>";
            } catch (Exception $e) {
                $error[] = 'Email could not be sent. Mailer Error: ' . $mail->ErrorInfo;
            }
        } else {
            $error[] = 'Failed to register. Please try again.';
        }
        
      }
   }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register Form</title>

   <!-- custom css file link -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<div class="form-container">

   <form action="" method="post">
      <h3>Register Now</h3>
      <?php
      // Display errors if any
      if (isset($error)) {
         foreach ($error as $err) {
            echo '<span class="error-msg">' . $err . '</span>';
         }
      }
      ?>
      <p>Your Name<sup>*</sup></p>
      <input type="text" name="name" required placeholder="Enter your name">
      <p>Your Email<sup>*</sup></p>
      <input type="email" name="email" required placeholder="Enter your email">
      <p>Password<sup>*</sup></p>
      <input type="password" name="password" required placeholder="Enter your password" minlength="8">
      <p>Confirm Password<sup>*</sup></p>
      <input type="password" name="cpassword" required placeholder="Confirm your password" minlength="8">
      <p>User Type<sup>*</sup></p>
      <select name="user_type">
         <option value="user">User</option>
         <option value="admin">Admin</option>
      </select>
      <input type="submit" name="submit" value="Register Now" class="form-btn">
      <p>Already have an account? <a href="login_form.php">Login now</a></p>
   </form>

</div>

</body>
</html>
