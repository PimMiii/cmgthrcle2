<?php
session_start();

require_once '../includes/database.php';
/** @var mysqli $db */

$errors = [];


//check if client is logged in as user
if (isset($_SESSION['LoggedInUser'])) {
    //create user and fill it with information from $_SESSION
    $user = [];
    $user['id'] = $_SESSION['LoggedInUser']['id'];
    $user['email'] = $_SESSION['LoggedInUser']['email'];
} else {
    //redirect client to homepage.
    header('Location: ../index.php');
}
// handle the POST data
if (isset($_POST['submit'])) {
    if ($_POST['pwverification'] != '') {
        //Retrieve passwordhash from database
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
            // validate input
            // check if email is a valid emailadress
            if (!filter_var($changed_profile['email'], FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = "Voer een geldig emailadres in";

                // post data to database
                // check if there was a profile found on initial page load
                if (isset($_SESSION['LoggedInUser']['new_user'])) {
                    // this is a new profile.
                    $query = "INSERT INTO `profiles`(
                                                        `first_name`,
                                                        `last_name`,
                                                        `street`,
                                                        `house_number`,
                                                        `postal_code`,
                                                        `city`,
                                                        `email`
                                                        )
                               VALUES(
                                                        `$changed_profile['first_name']`,
                                                        `$changed_profile['last_name']`,
                                                        `$changed_profile['street']`,
                                                        `$changed_profile['house_number']`,
                                                        `$changed_profile['postal_code']`,
                                                        `$changed_profile['city']`,
                                                        `$changed_profile['email']`
);";
                    $result = mysqli_query($db, $query)
                    or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
                    $profile_id = mysqli_insert_id($db);


                }


            } else {
                $errors['pwverification'] = "Dit wachtwoord is niet bij ons bekend.";
            }
        }
    } else {
        $errors['pwverification'] = "Voer uw wachtwoord ter verificatie.";
    }
} else {
//Retrieve profile information from database
    $id = $user['id'];
    $query = "SELECT profiles.* FROM users INNER JOIN profiles ON users.profile_id = profiles.id WHERE users.id = $id";
    $result = mysqli_query($db, $query)
    or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
    // check if database returned a profile
    if (mysqli_num_rows($result) == 0) {
        $_SESSION['LoggedInUser']['new_user'] = true;
    } else {
        $user['profile'] = mysqli_fetch_assoc($result);
    }
}

//Als er profielgegevens uit de database zijn gehaald:
//    sla deze op in een 'userprofile'-array.

//Stel een formulier op dat om de volgende gegevens vraagt:
/*
-Voornaam
-Achternaam
-Straat
-Huisnummer
-Woonplaats
-Postcode
-Email
-Wachtwoord ter verificatie
-submit knop met actie: postback
En gebruik de waarden uit het 'userprofile'-array als standaardwaarden voor de bovengenoemde velden.
*/

//Sanitise de ingevoerde gegevens onder andere door htmlentites() en mysqli_escape_string() te gebruiken.
//Valideer de gegevens, en check of wachtwoord overeenkomt met het in de database opgeslagen wachtwoord
//Bij Errors:
//    toon deze aan de gebruiker door error tekst te tonen naast het foutief ingevulde veld.
//Zijn er geen Errors:
//    Als `userprofile`-array gevuld is (dus als er al een profiel is) update deze:
//        Stel een UPDATE query op met de opgegeven gegevens.
//Als er nog geen profiel is:
//        Stel een INSERT INTO query op met de opgegeven gegevens.
//Voer query uit.
//Bij Error:
//        toon Error

//Als alles goed is gegaan:
//    redirect gebruiker naar profielpagina

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
            <input type="text" name="first_name" id="first_name" value="<?= $user['profile']['first_name'] ?? '' ?>" >
            <span class="errors"><?= $errors['first_name'] ?? '' ?></span>
        </div>
        <div>
            <label for="last_name">Achternaam: </label>
            <input type="text" name="last_name" id="last_name" value="<?= $user['profile']['last_name'] ?? '' ?>" >
            <span class="errors"><?= $errors['last_name'] ?? '' ?></span>
        </div>
    </div>
    <div>
        <h2>E-mailgegevens</h2>
        <div>
            <label for="email">E-mail adres: </label>
            <input type="email" name="email" id="email" value="<?= $user['profile']['email'] ?? $user['email'] ?? '' ?>" required>
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
            <input type="text" name="street" id="street"value="<?= $user['profile']['street'] ?? '' ?>" required>
            <span class="errors"><?= $errors['street'] ?? '' ?></span>
        </div>
        <div>
            <label for="house_number">Huisnummer: </label>
            <input type="text" name="house_number" id="house_number" value="<?= $user['profile']['house_number'] ?? '' ?>" required>
            <span class="errors"><?= $errors['house_number'] ?? '' ?></span>
        </div>
        <div>
            <label for="streetname">Postcode: </label>
            <input type="text" name="postal_code" id="postal_code" value="<?= $user['profile']['postal_code'] ?? '' ?>"required>
            <span class="errors"><?= $errors['postal_code'] ?? '' ?></span>
        </div>
        <div>
            <label for="city">Woonplaats: </label>
            <input type="text" name="city" id="city" value="<?= $user['profile']['city'] ?? '' ?>" required>
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

