<?php

@include 'config.php';

if (isset($_GET['email'])) {
    $email = mysqli_real_escape_string($conn, $_GET['email']);
    $query = "SELECT * FROM user_form WHERE email = '$email' AND verification = 0";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $update = "UPDATE user_form SET verification = 1 WHERE email = '$email'";
        if (mysqli_query($conn, $update)) {
            echo "<h3>Email verified successfully!</h3>";
            echo "<p><a href='login_form.php'>Click here to login</a></p>";
        } else {
            echo "<h3>Verification failed. Please try again.</h3>";
        }
    } else {
        echo "<h3>Email is already verified or does not exist.</h3>";
    }
} else {
    echo "<h3>Invalid request.</h3>";
}
?>
