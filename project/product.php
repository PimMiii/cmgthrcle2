<?php
session_start();
require_once 'includes/products.php';
require_once 'includes/database.php';
/** @var mysqli $db */
if (isset($_GET['productid'])) {
    $id = htmlentities(mysqli_escape_string($db, $_GET['productid']));
} else {
    $errors['product'] = "Sorry, er is iets fout gegaan. Dit product kan niet worden gevonden.";
}

if (isset($_SESSION['LoggedInUser'])) {
    $user ['role'] = $_SESSION['LoggedInUser']['role'];
} else {
    $user['role'] = 0;
}
//retrieve all products from the database
$products = getProduct($db, $id, $user['role']);
$errors = $products['errors'];
if (empty($errors)) {
    $product = $products['product'];
}
else {
    $product = array(
            'name' => $errors['product'],
        'description' => '',
        'price' => 0.0000
    );
    $class = 'errors';
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Home</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<nav>

    <div><a href="index.php"><img src="images/logo.bmp" alt="Homepagina" class="logo"></a></div>
    <div><a href="login.php"><img src="images/profile.svg" alt="Mijn Proffiel" class="profile"></a></div>
    <div><a href="cart.php"><img src="images/cart0.svg" alt="Winkelwagen" class="cart"></a></div>


</nav>
<div class="main">
    <div class="allproducts"><h6><a href="products.php">&#139;Alle Producten</a></h6></div>
    <?php { ?>
        <div class="product">
            <div class="thumbnail">
                <?php // insert thumbnail here ?>
            </div>
            <div class="<?= $class ?? "productname" ?>">
                <h2><?= $product['name'] ?></h2>
            </div>
            <div class="productdescription">
                <p><?= $product['description'] ?></p>

            </div>
            <div class="productactions">
                <div class="<?= $class ?? "addtocart"?>">
                    <a href=""><img src="images/cartAdd.svg" alt="stop product in winkelwagen" class="addtocart"></a>
                </div>
                <div class="price">
                    <h3><?= '€' . number_format($product['price'], 2, ",") ?></h3>
                </div>

            </div>
        </div>
    <?php }; ?>
    <div><h6><a href="contact.php">Contact</a></h6></div>
</div>
</body>
</html>