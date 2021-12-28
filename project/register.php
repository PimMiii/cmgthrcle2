<?php
    if(isset($_POST['submit'])) {
        require_once "includes/database.php";
        /** @var mysqli $db */
        $errors = [];
      // validate email
        $email = mysqli_escape_string($db, $_POST['email']);
        // sanitize email, by deleting all characters not allowed in an emailadress
        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
        // check if email is a valid emailadress
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "$email is a valid emailadress";
        }
        else {
            echo "$email is not a valid emailadress";
        }
      // check if passwords are the same
      // hash password
      // send to database
    }
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Register</title>
</head>
<body>
<h1> Register Now!</h1>

<form action="" method="post">
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" required>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
    </div>
    <div>
        <label for="confirmPassword">Confirm Password:</label>
        <input type="password" name="confirmPassword" id="confirmPassword" required>
    </div>
    <div>
        <input type="submit" name="submit" id="submit">
    </div>
</form>

<p><a href="index.php">Home</a></p>
<p><a href="login.php">Login</a></p>
</body>
</html>
