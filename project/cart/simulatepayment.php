<?php
session_start();

require_once '../includes/database.php';
/** @var mysqli $db */

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

if(isset($_GET['order'])) {
    $order_id= htmlentities(mysqli_escape_string($db, $_GET['order']));
}

header('refresh:5;url=../order.php?orderid='.$order_id);
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>EasyGoods</title>
    <link rel="stylesheet" href="../style.css"/>
</head>
<body>
<nav>
    <div class="navcontent">
        <div><a href="../index.php"><img src="../icons/logo.bmp" alt="Homepagina" class="logo"></a></div>
        <div class="search"></div>
        <div class="navright">
            <div><a href="../login.php"><img src="../icons/profile.svg" alt="Mijn Proffiel" class="profile"></a></div>
            <div><a href="../cart.php"><img src="../icons/cart<?=$fullcart?>.svg" alt="Winkelwagen" class="cart"></a></div>
        </div>
    </div>
</nav>
<div class="main">
    <div class="quickactions">
        <div class="allproducts">
            <h6><a href="../products.php">&#139;Alle Producten</a></h6>
        </div>
        <div class="contact">
            <h6><a href="../contact.php">Contact</a></h6>
        </div>
    </div>
    <div class="product">
        <p>Simulating payment....</p>
        <p>order: <?= strtoupper($order_id) ?? ''?></p>


    </div>
</div>
</body>
</html>