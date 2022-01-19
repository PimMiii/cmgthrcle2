<?php
session_start();
if(isset($_SESSION['LoggedInUser'])) {
    $login = true;
}
else {
    $login = false;
}
// to set cart icon
if(isset($_COOKIE['cart'])){
    $fullcart = 1;
} else {
    $fullcart = 0;
}

if ($login == true){
    header('Location: profile.php');
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
        $query = "SELECT email,password, id, role FROM users WHERE email='$email';";
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
                    'id' => $user['id'],
                    'role' => $user['role']
                ];

                header('Location: profile.php');
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
    <title>EasyGoods</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<nav>
    <div class="navcontent">
        <div><a href="index.php"><img src="icons/logo.bmp" alt="Homepagina" class="logo"></a></div>
        <div class="search"></div>
        <div class="navright">
            <div id="active"><a href="login.php"><img src="icons/profile.svg" alt="Mijn Proffiel" class="profile"></a></div>
            <div><a href="cart.php"><img src="icons/cart<?=$fullcart?>.svg" alt="Winkelwagen" class="cart"></a></div>
        </div>
    </div>
</nav>
<div class="main">
    <div class="quickactions">
        <div class="register">
            <h6><a href="register.php">Registreren</a></h6>
        </div>
    </div>
<form action="" method="post">
    <div class="title">
        <h2>Inloggen</h2>
    </div>
    <div>
        <label for="email">E-Mail:</label>
        <input type="text" name="email" id="email" required>
        <span class="errors"> <?=$errors['loginFailed'] ?? ''?> </span>
    </div>
    <div>
        <label for="password">Wachtwoord:</label>
        <input type="password" name="password" id="password" required>
    </div>
    <div>
        <input type="submit" name="submit" id="login" value="Login"

    </div>
</form>

</div>
</body>
</html>
