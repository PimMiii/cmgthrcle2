<?php
    if(isset($_POST['submit'])) {
        require_once "includes/database.php";
        /** @var mysqli $db */

        $password = $_POST['password'];
        $email = mysqli_escape_string($db, $_POST['email']);

      // validate email
        if($email == ''){
            $errors['email'] ="Voer een e-mailadres in" ;
        }
        else{
            $email = mysqli_escape_string($db, $_POST['email']);
            // sanitize email, by deleting all characters not allowed in an emailadress
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            // check if email is a valid emailadress
            if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "$email is a valid emailadress";
            }
            else {

                $errors['emailInvalid'] = "Voer een geldig e-mailadres in";
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

            if ($result) {
                header('Location: login.php');
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
    <title>Register</title>
</head>
<body>
<h1> Register Now!</h1>

<form action="" method="post">
    <div>
        <label for="email">Email:</label>
        <input type="email" name="email" id="email" value="<?= $email ?? '' ?>" required>
        <span class="errors"><?= $errors['email'] || $errors['emailInvalid'] ?? '' ?></span>
    </div>
    <div>
        <label for="password">Password:</label>
        <input type="password" name="password" id="password" required>
        <span class="errors"><?= $errors['password'] ?? NULL ?></span>
    </div>
    <div>
        <label for="confirmPassword">Confirm Password:</label>
        <input type="password" name="confirmPassword" id="confirmPassword" required>
        <span class="errors"><?= $errors['confirmPassword'] || $errors['passwordMatch'] ?? '' ?></span>
    </div>
    <div>
        <input type="submit" name="submit" id="submit">
    </div>
</form>

<p><a href="index.php">Home</a></p>
<p><a href="login.php">Login</a></p>
</body>
</html>
