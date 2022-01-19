<?php
session_start();
require_once 'includes/products.php';
require_once 'includes/database.php';
/** @var mysqli $db */
if (isset($_GET['orderid'])) {
    $id = htmlentities(mysqli_escape_string($db, $_GET['orderid']));
} else {
    $errors['product'] = "Sorry, er is iets fout gegaan. Dit product kan niet worden gevonden.";
}

if (isset($_SESSION['LoggedInUser'])) {
    $user ['role'] = $_SESSION['LoggedInUser']['role'];
} else {
    $user['role'] = 0;
}
// to set cart icon
if(isset($_COOKIE['cart'])){
    $fullcart = 1;
} else {
    $fullcart = 0;
}
//retrieve order from the database


?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EasyGoods</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<nav>
    <div class="navcontent">
        <div><a href="index.php"><img src="icons/logo.bmp" alt="Homepagina" class="logo"></a></div>
        <div class="search"></div>
        <div class="navright">
            <div><a href="login.php"><img src="icons/profile.svg" alt="Mijn Proffiel" class="profile"></a></div>
            <div><a href="cart.php"><img src="icons/cart<?=$fullcart?>.svg" alt="Winkelwagen" class="cart"></a></div>
        </div>
    </div>
</nav>
<div class="main">
    <div class="quickactions">
        <div class="allproducts">
            <h6><a href="products.php">&#139;Alle Producten</a></h6>
        </div>
        <div class="contact">
            <h6><a href="contact.php">Contact</a></h6>
        </div>
    </div>
    <?php { ?>
        <div class="product">
            <p>Order: <?= $id ?? '' ?></p>
            <p>Orderinfo moet hier komen.</p>

        </div>
    <?php }; ?>

</div>
</body>
</html>
