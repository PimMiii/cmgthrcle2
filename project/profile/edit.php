<?php
session_start();

require_once '../includes/database.php';
/** @var mysqli $db */

$errors = [];


// check if client is logged in as user
if (isset($_SESSION['LoggedInUser'])) {
    //create user and fill it with information from $_SESSION
    $user = [];
    $user['id'] = $_SESSION['LoggedInUser']['id'];
    $user['email'] = $_SESSION['LoggedInUser']['email'];
} else {
    // redirect client to homepage.
    header('Location: ../index.php');
}
// handle the POST data
if (isset($_POST['submit'])) {
    if ($_POST['pwverification'] != '') {
        // retrieve passwordhash from database
        $id = $user['id'];
        $query = "SELECT password FROM users WHERE id='$id';";
        $result = mysqli_query($db, $query)
        or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
        $hashed_pw = mysqli_fetch_assoc($result);
        // verify password
        $password = $_POST['pwverification'];
        if (password_verify($password, $hashed_pw['password'])) {
            // sanitize input
            $changed_profile = array(
                'first_name' => mysqli_escape_string($db, $_POST['first_name']),
                'last_name' => mysqli_escape_string($db, $_POST['last_name']),
                'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
                'street' => mysqli_escape_string($db, $_POST['street']),
                'house_number' => mysqli_escape_string($db, $_POST['house_number']),
                'postal_code' => mysqli_escape_string($db, $_POST['postal_code']),
                'city' => mysqli_escape_string($db, $_POST['city'])
            );
            //check if fields are empty
            if ($changed_profile['first_name'] == '') {
                $errors['first_name'] = "Voer uw voornaam in";
            }
            if ($changed_profile['last_name'] == '') {
                $errors['last_name'] = "Voer uw achternaam in";
            }
            if ($changed_profile['street'] == '') {
                $errors['street'] = "Voer de straat waar u woont in";
            }
            if ($changed_profile['house_number'] == '') {
                $errors['house_number'] = "Voer uw huisnummer in";
            }
            if ($changed_profile['postal_code'] == '') {
                $errors['postal_code'] = "Voer uw postcode in";
            }
            if ($changed_profile['city'] == '') {
                $errors['city'] = "Voer uw woonplaats in";
            }
            // validate user input

            // first names can be a maximum of 50 characters.
            // to help user trim whitespace from beginning and end, and capitalize first letter.
            $changed_profile['first_name'] = ucfirst(trim($changed_profile['first_name']));
            if (strlen($changed_profile['first_name']) > 50) {
                $errors['first_name'] = "Voornaam te lang, maximaal 50 tekens.";
            }
            // last names can be a maximum of 75 characters.
            // to help user trim whitespace from beginning and end
            $changed_profile['last_name'] = trim($changed_profile['last_name']);
            if (strlen($changed_profile['last_name']) > 75) {
                $errors['last_name'] = "Achternaam te lang, maximaal 75 tekens.";
            }
            // check if email is in a valid emailadress format
            if (!filter_var($changed_profile['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Voer een geldig emailadres in";
            }
            // street names can be a maximum of 75 characters
            // to help user trim whitespace from beginning and end, and capitalize first letter
            $changed_profile['street'] = trim($changed_profile['street']);
            if (strlen($changed_profile['street']) > 75) {
                $errors['street'] = "Straatnaam te lang, maximaal 75 tekens.";
            }
            // house numbers can be a maximum of 7 characters
            // and must start with, and include, a number
            // to help the user trim whitespaces from the beginning and end
            $pattern_has_number = "/[0-9]/";
            $pattern_begin_with_number = "/^[0-9]/";

            $changed_profile['house_number'] = trim($changed_profile['house_number']);
            if (strlen($changed_profile['house_number']) > 7) {
                $errors['house_number'] = "Huisnummer te lang, maximaal 7 tekens.";
            }
            if (!preg_match($pattern_has_number, $changed_profile['house_number'])) {
                $errors['house_number'] = "Huisnummer moet tenminste één nummer bevatten.";
            }
            if (!preg_match($pattern_begin_with_number, $changed_profile['house_number'])) {
                $errors['house_number'] = "Huisnummer moet beginnen met een nummer.";
            }
            // postal code must match pattern '1234AB'
            // therefore it can be a maximum of 6 characters (implicitly enforced)
            // to help user strip (white)spaces and make letters uppercase.
            $pattern = "/^[0-9][0-9][0-9][0-9][a-z][a-z]$/i";
            $changed_profile['postal_code'] = strtoupper(str_replace(' ', '', $changed_profile['postal_code']));
            if (!preg_match($pattern, $changed_profile['postal_code'])) {
                $errors['postal_code'] = "Voer een geldige postcode in. bijv. 1234AB";
            }
            // city names can be a maximum of 60 characters.
            // to help user strip whitespaces from beginning and end
            $changed_profile['city'] = trim($changed_profile['city']);
            if (strlen($changed_profile['city']) > 75) {
                $errors['city'] = "Woonplaats te lang, maximaal 60 tekens.";
            }

            // if there are no errors, post data to database
            if (empty($errors)) {
                // check if there was a profile found on initial page load
                if (isset($_SESSION['LoggedInUser']['new_user'])) {
                    // this is a new profile.
                    $query =
                        "INSERT INTO `profiles`(
                       `first_name`,
                       `last_name`,
                       `street`,
                       `house_number`,
                       `postal_code`,
                       `city`,
                       `email`
                       ) VALUES(
                        '" . $changed_profile['first_name'] . "',
                        '" . $changed_profile['last_name'] . "',
                        '" . $changed_profile['street'] . "',
                        '" . $changed_profile['house_number'] . "',
                        '" . $changed_profile['postal_code'] . "',
                        '" . $changed_profile['city'] . "',
                        '" . $changed_profile['email'] . "'
                        );";

                    // add it to the database
                    $result = mysqli_query($db, $query)
                    or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
                    // update user with profile id
                    $profile_id = mysqli_insert_id($db);
                    // update user in db
                    if (isset($_POST['make_login'])) {
                        $query = "UPDATE `users` SET `profile_id` = '$profile_id', `email` = '". $changed_profile['email'] ."' WHERE `id` = '$id';";
                    } else {
                        $query = "UPDATE `users` SET `profile_id` = '$profile_id' WHERE `id` = '$id';";
                    }
                    $result = mysqli_query($db, $query)
                    or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);

                    $_SESSION['LoggedInUser']['new_user'] = null;
                    // add profile_id to $_SESSION and $user
                    $_SESSION['LoggedInUser']['profile_id'] = $profile_id;
                    $user['profile_id'] = $_SESSION['LoggedInUser']['profile_id'];

                    //redirect client back to profile page now with the changes they made
                    header('Location ../profile.php');

                } else {
                    // there's an existing profile to update
                    $query =
                        "UPDATE `profiles`
                        SET
                       `first_name` = '" . $changed_profile['first_name'] . "',
                       `last_name` = '" . $changed_profile['last_name'] . "',
                       `street` = '" . $changed_profile['street'] . "',
                       `house_number` = '" . $changed_profile['house_number'] . "',
                       `postal_code` = '" . $changed_profile['postal_code'] . "',
                       `city` = '" . $changed_profile['city'] . "',
                       `email` = '" . $changed_profile['email'] . "' 
                       WHERE `id` = '". $_SESSION['LoggedInUser']['profile_id'] ."';";
                    // update profile
                    $result = mysqli_query($db, $query)
                    or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
                    // update login-email if requested by user
                    if (isset($_POST['make_login'])) {
                        $query = "UPDATE `users` SET `email` = '" . $changed_profile['email'] . "' WHERE `id` = '$id';";
                        $result = mysqli_query($db, $query)
                        or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
                    }

                    //redirect client back to profile page now with the changes they made
                    header('Location ../profile.php');
                }

            }


        } else {
            $errors['pwverification'] = "Dit wachtwoord is niet bij ons bekend.";
        }

    } else {
        $errors['pwverification'] = "Voer uw wachtwoord ter verificatie.";
    }
} else {
// retrieve profile information from database
    $id = $user['id'];
    $query = "SELECT profiles.* FROM users INNER JOIN profiles ON users.profile_id = profiles.id WHERE users.id = $id";
    $result = mysqli_query($db, $query)
    or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
    // check if database returned a profile
    if (mysqli_num_rows($result) == 0) {
        $_SESSION['LoggedInUser']['new_user'] = true;
    } else {
        $user['profile'] = mysqli_fetch_assoc($result);
        $_SESSION['LoggedInUser']['profile_id'] = $user['profile']['id'];
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
    <title>Mijn Profiel</title>
</head>
<body>
<h1>Profiel Aanpassen</h1>

<form action="" method="post">
    <div>
        <h2>Naamgegevens</h2>
        <div>
            <label for="first_name">Voornaam: </label>
            <input type="text" name="first_name" id="first_name"
                   value="<?= $changed_profile['first_name'] ?? $user['profile']['first_name'] ?? '' ?>" required>
            <span class="errors"><?= $errors['first_name'] ?? '' ?></span>
        </div>
        <div>
            <label for="last_name">Achternaam: </label>
            <input type="text" name="last_name" id="last_name"
                   value="<?= $changed_profile['last_name'] ?? $user['profile']['last_name'] ?? '' ?>" required>
            <span class="errors"><?= $errors['last_name'] ?? '' ?></span>
        </div>
    </div>
    <div>
        <h2>E-mailgegevens</h2>
        <div>
            <label for="email">E-mail adres: </label>
            <input type="email" name="email" id="email"
                   value="<?= $changed_profile['email'] ?? $user['profile']['email'] ?? $user['email'] ?? '' ?>"
                   required>
            <span class="errors"><?= $errors['email'] ?? '' ?></span>
        </div>
        <div>
            <input type="checkbox" id="make_login" name="make_login">
            <label for="make_login">Maak dit het emailadres waar ik mee inlog</label>
        </div>
    </div>
    <div>
        <h2>Adresgegevens</h2>
        <div>
            <label for="street">Straat: </label>
            <input type="text" name="street" id="street"
                   value="<?= $changed_profile['street'] ?? $user['profile']['street'] ?? '' ?>" required>
            <span class="errors"><?= $errors['street'] ?? '' ?></span>
        </div>
        <div>
            <label for="house_number">Huisnummer: </label>
            <input type="text" name="house_number" id="house_number"
                   value="<?= $changed_profile['house_number'] ?? $user['profile']['house_number'] ?? '' ?>" required>
            <span class="errors"><?= $errors['house_number'] ?? '' ?></span>
        </div>
        <div>
            <label for="postal_code">Postcode: </label>
            <input type="text" name="postal_code" id="postal_code"
                   value="<?= $changed_profile['postal_code'] ?? $user['profile']['postal_code'] ?? '' ?>"
                   required>
            <span class="errors"><?= $errors['postal_code'] ?? '' ?></span>
        </div>
        <div>
            <label for="city">Woonplaats: </label>
            <input type="text" name="city" id="city"
                   value="<?= $changed_profile['city'] ?? $user['profile']['city'] ?? '' ?>" required>
            <span class="errors"><?= $errors['city'] ?? '' ?></span>
        </div>
    </div>
    <div>
        <div>
            <h2> Verificatie</h2>
            <div>
                <label for="pwverification">Voer uw wachtwoord in: </label>
                <input type="password" name="pwverification" id="pwverification" required>
                <span class="errors"><?= $errors['pwverification'] ?? '' ?></span>
            </div>
            <input type="submit" name="submit" id="submit" value="Registreren">
        </div>
    </div>
</form>

<p><a href="../logout.php">Uitloggen</a></p>
<p><a href="../index.php">Home</a></p>
<p><a href="../products.php">Producten</a></p>
<p><a href="../orderhistory.php">Order history</a></p>
</body>
</html>