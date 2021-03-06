<?php /** @noinspection SqlDialectInspection */

// to set cart icon
if(isset($_COOKIE['cart'])){
    $fullcart = 1;
} else {
    $fullcart = 0;
}
if(isset($_POST['submit'])) {
        require_once "includes/database.php";

        /** @var mysqli $db */

        $password = $_POST['password'];
        $email = mysqli_escape_string($db, $_POST['email']);

        $errors = [];

      // validate email
        if($email == ''){
            $errors['email'] ="Voer een emailadres in" ;
        }
        else{
            // sanitize email, by deleting all characters not allowed in an emailadress
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            // check if email is a valid emailadress
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['emailInvalid'] = "Voer een geldig emailadres in";
            }
        }
        // validate password
        if($password== ''){
            $errors['password'] = "Voer een wachtwoord in";
        }
        //Validate if confirmPassword field has been filled in
        elseif ($_POST['confirmPassword'] == '') {
            $errors['confirmPassword'] = "Vul nogmaals je wachtwoord in";
        }
        else{
            // check if passwords are the same
            $password = $_POST['password'];
            if ($password === $_POST['confirmPassword']){
                // hash password
                $password = password_hash($password, PASSWORD_DEFAULT);
            }
            else {
                $errors['passwordMatch'] = 'Ingevoerde wachtwoorden komen niet overeen';
            }
        }
        // send data to db
        if(empty($errors)) {
            $query = "INSERT INTO users (email, password) VALUES ('$email', '$password')";

            $result = mysqli_query($db, $query)
            or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);

            // redirect user to login page on successful registration
            if ($result) {
                header('Location: login.php');
                exit;
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
        <div class="login">
            <h6><a href="login.php">Inloggen</a></h6>
        </div>
    </div>

<form action="" method="post">
    <h2>Nieuw account registreren</h2>
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?= $email ?? '' ?>" required>
        <span class="errors"><?= $errors['email'] ?? $errors['emailInvalid'] ?? '' ?></span>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <span class="errors"><?= $errors['password'] ?? '' ?></span>
    </div>
    <div>
        <label for="confirmPassword">Confirm Password:</label>
        <input type="password" name="confirmPassword" id="confirmPassword" required>
        <span class="errors"><?= $errors['confirmPassword'] ?? $errors['passwordMatch'] ?? '' ?></span>
    </div>
    <div>
        <input type="submit" name="submit" id="submit" value="Registreren">
    </div>
</form>


</div>
</body>
</html>
