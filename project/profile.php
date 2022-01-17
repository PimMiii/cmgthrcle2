<?php /** @noinspection SqlDialectInspection */
session_start();

require_once 'includes/database.php';
/** @var mysqli $db */

// check if there is a logged-in user
if (isset($_SESSION['LoggedInUser'])) {
    //check if user is admin, if true redirect to admin portal
    if ($_SESSION['LoggedInUser']['role'] == 1) {
        header('Location: admin.php');
    }
    $id = $_SESSION['LoggedInUser']['id'];
    $query = "SELECT profiles.* FROM users INNER JOIN profiles ON users.profile_id = profiles.id WHERE users.id = $id";
    $result = mysqli_query($db, $query)
    or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
    if (mysqli_num_rows($result) < 1) {
        header('Location: profile/edit.php');
    } else {
        $profile = mysqli_fetch_assoc($result);
    }
} //if no logged-in user redirect client to login
else {
    header('Location: login.php');
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Profile</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<nav>
    <div class="navcontent">
        <div><a href="index.php"><img src="icons/logo.bmp" alt="Homepagina" class="logo"></a></div>
        <div class="search"></div>
        <div class="navright">
            <div><a href="login.php"><img src="icons/profile.svg" alt="Mijn Proffiel" class="profile"></a></div>
            <div><a href="cart.php"><img src="icons/cart0.svg" alt="Winkelwagen" class="cart"></a></div>
        </div>
    </div>
</nav>
<div class="main">
    <div class="quickactions">

        <div class="logout">
            <h6><a href="profile/logout.php">Uitloggen</a></h6>
        </div>
        <div class="editprofile">
            <h6><a href="profile/edit.php">Profiel aanpassen</a></h6>
        </div>
        <div class="history">
            <h6><a href="profile/orderhistory.php">Bestellingsgeschiedenis</a></h6>
        </div>
    </div>

        <div class="userprofile">
            <table style="width: 100%">
                <tbody>
                <tr>
                    <td colspan="2">Naam</td>
                    <td><?= $profile['first_name'] . " " . $profile['last_name'] ?></td>
                </tr>
                <tr>
                    <td colspan="2">Adres</td>
                    <td><?= $profile['street'] . " " . $profile['house_number'] ?></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td><?= $profile['postal_code'] ?></td>
                </tr>
                <tr>
                    <td colspan="2"></td>
                    <td><?= $profile['city'] ?></td>
                </tr>

                </tbody>
            </table>
        </div>


    </div>
</body>
</html>