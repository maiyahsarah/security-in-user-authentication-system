<?php
@include 'config.php';
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $select = "SELECT * FROM user_form WHERE email = '$email'";
    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $token = bin2hex(random_bytes(50)); // Generate a unique token
        $expires = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour

        // Store the token in the database
        $update = "UPDATE user_form SET reset_token='$token', token_expires='$expires' WHERE email='$email'";
        mysqli_query($conn, $update);

        // Send password reset email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'woomay.sarathee@student.aiu.edu.my'; // Replace with your email
            $mail->Password = 'urikrwucaccdzxle';  // Replace with your email password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('woomay.sarathee@student.aiu.edu.my', 'Password Reset');
            $mail->addAddress($email);

            $mail->isHTML(true);
            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Hi,<br><br>You requested to reset your password. Click the link below to reset it:<br>
            <a href='http://localhost/login%20system/login%20system/reset_password.php?token=$token'>Reset Password</a><br><br>
            This link will expire in 1 hour.<br><br>Thank you!";

            $mail->send();
            echo 'Password reset email has been sent!';
        } catch (Exception $e) {
            echo 'Error sending email: ' . $mail->ErrorInfo;
        }
    } else {
        echo '<div class="error-msg">Email not found!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<div class="form-container">
    <form action="" method="post">
        <h3>Forgot Password</h3>
        <p>Enter your email address to reset your password.</p>
        <input type="email" name="email" required placeholder="Enter your email">
        <input type="submit" name="submit" value="Send Reset Link" class="form-btn">
    </form>
</div>
</body>
</html>
