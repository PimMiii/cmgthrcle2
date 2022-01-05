<?php /** @noinspection SqlDialectInspection */
session_start();

require_once 'includes/database.php';
/** @var mysqli $db */

// check if there is a logged-in user
if(isset($_SESSION['LoggedInUser'])) {
    //check if user is admin, if true redirect to admin portal
    if($_SESSION['LoggedInUser']['role']==1) {
        header('Location: admin.php');
    }
    $id = $_SESSION['LoggedInUser']['id'];
    $query = "SELECT profiles.* FROM users INNER JOIN profiles ON users.profile_id = profiles.id WHERE users.id = $id";
    $result = mysqli_query($db, $query)
    or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
    if(mysqli_num_rows($result) < 1){
        header('Location: profile/edit.php');
    }
    else {
        $profile = mysqli_fetch_assoc($result);
    }
}
//if no logged-in user redirect client to login
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
</head>
<body>
<h1>Mijn Profiel</h1>
<div>
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

<p><a href="logout.php">Uitloggen</a></p>
<p><a href="index.php">Home</a></p>
<p><a href="products.php">Producten</a></p>
<p><a href="orderhistory.php">Order history</a></p>


</body>
</html>