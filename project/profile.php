<?php
session_start();
// check if there is a logged-in user
if(isset($_SESSION['LoggedInUser'])) {
    //check if user is admin, if true redirect to admin portal
    if($_SESSION['LoggedInUser']['role']==1) {
        header('Location: admin.php');
    }

/*
 * needed query to grab the right profile through the given users.id
 *
 * SELECT profiles.*
 * FROM users
 * INNER JOIN profiles ON users.profile_id = profiles.id
 * WHERE users.id = given_id;
 */


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

<p><a href="logout.php">Uitloggen</a></p>
<p><a href="index.php">Home</a></p>
<p><a href="products.php">Producten</a></p>
<p><a href="orderhistory.php">Order history</a></p>


</body>
</html>