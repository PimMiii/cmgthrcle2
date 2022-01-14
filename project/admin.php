<?php
session_start();
require_once 'includes/database.php';
/** @var mysqli $db */
require_once 'includes/getallproducts.php';


if(isset($_SESSION['LoggedInUser'])){
    //check if user has admin role
    if($_SESSION['LoggedInUser']['role']!=1){
        // redirect client to Homepage
        header('Location: index.php');
    }
    $user[ 'role'] = $_SESSION['LoggedInUser']['role'];
    //retrieve all products from the database
    $products = getAllProducts($db, 'admin', $user['role']);
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<nav>
    <div><a href="index.php"><img src="images/logo.bmp" alt="Homepage" class="logo"></a></div>
    <div><a href="logout.php">Uitloggen</a></div>
</nav>
<div class="main">
<h1>Adminportaal</h1>

<p>Welkom admin</p>

    <?php foreach ($products as $product) { ?>
        <div class="product">
            <div class="thumbnail">
                <?php // insert thumbnail here ?>
            </div>
            <div class="productname">
                <h2><?= $product['name'] ?></h2>
            </div>
            <div class="productdescription">
                <p><?= $product['description'] ?></p>
            </div>
            <div class="price">
                <h3><?='€'.number_format($product['price'], 2, ",")?></h3>
            </div>
            <div class="addtocart">
                <?php // add to cart link here ?>
            </div>
        </div>
    <?php }; ?>
</div>
</body>
</html>
