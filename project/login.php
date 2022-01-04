<?php
session_status();
if(isset($_SESSION['LoggedInUser'])) {
    $login = true;
}
else {
    $login = false;
}

if(isset($_POST['submit'])) {
    require_once "includes/database.php";
    /** @var mysqli $db */

    $errors = [];

    $email = mysqli_escape_string($db, $_POST['email']);
    $password = $_POST['password'];

    // check if email is given
    if ($email == '') {
        $errors['email'] = "Voer een e-mailadres in";
    }

    // check if password is given
       if ($password == ''){
        $errors['password'] = "Voer een wachtwoord in";
    }

    if (empty($errors)){
        // use email to get the records from DB
        $query = "SELECT id,email,password FROM users WHERE email='$email';";
        $result = mysqli_query($db, $query)
        or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
        // check if a record has been found
        if(mysqli_num_rows($result) == 1){
            $user = mysqli_fetch_assoc($result);
            // verify password
            if (password_verify($password, $user['password'])) {
                $login = true;

                //set LoggedInUser in session
                $_SESSION['LoggedInUser'] = [
                    'email' => $user['email'],
                    'id' => $user['id']
                ];
            }
            else {
                $errors['loginFailed'] = 'De combinatie van email en wachtwoord is bij ons niet bekend';
            }

        } else {
            $errors['loginFailed'] = 'De combinatie van email en wachtwoord is bij ons niet bekend';
        }

    }



}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Login</title>
</head>
<body>
<h1>Login</h1>
<form action="" method="post">
    <div>
        <label for="email">E-Mail:</label>
        <input type="text" name="email" id="email" required>
    </div>
    <div>
        <label for="password">Wachtwoord:</label>
        <input type="password" name="password" id="password" required>
    </div>
    <div>
        <input type="submit" name="submit" id="login" value="Login">
    </div>
</form>
<p><a href="index.php">Home</a></p>
<p><a href="register.php">Register</a></p>
<p><a href="profile.php"> Profile</a></p>
</body>
</html>
