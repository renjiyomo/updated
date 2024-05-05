<?php

@include 'tracking_db.php';

session_start();


if (isset($_POST['submit'])) {

    $names = mysqli_real_escape_string($conn, $_POST['names']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = $_POST['password'];
    $cpass = $_POST['cpassword'];

    $select = " SELECT * FROM users WHERE email = '$email' && password = '$pass' ";

    $result = mysqli_query($conn, $select);

    if (mysqli_num_rows($result) > 0) {
        $error[] = 'User already exists!';
    } else {
        if ($pass != $cpass) {
            $error[] = 'Passwords do not match!';
        } else {
            $insert = "INSERT INTO users(names, email, password) VALUES('$names','$email', '$pass')";
            mysqli_query($conn, $insert);
            header('location: SignIn/signin.php');
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
    <title>CSD Register</title>
    <link rel="stylesheet" href="signup.css">

</head>

<body>

    <div class="form-container">

        <form action="" method="post">
            <h3>Sign Up</h3>
            <?php
            if (isset($error)) {
                foreach ($error as $error) {
                    echo '<span class="error-msg">' . $error . '</span>';
                };
            };
            ?>
            <input type="text" name="names" required placeholder="Enter your name">
            <input type="email" name="email" required placeholder="Enter your email">
            <input type="password" name="password" required placeholder="Enter your password">
            <input type="password" name="cpassword" required placeholder="Confirm your password">
            <input type="submit" name="submit" value="Register" class="form-btn">
            <p>Already have an account? <a href="SignIn/signin.php">Login now</a></p>
        </form>

    </div>

</body>

</html>