<?php
session_start();
require_once 'includes/database.php';
/** @var mysqli $db */
require_once 'includes/products.php';


if(isset($_SESSION['LoggedInUser'])) {
    //check if user has admin role
    if ($_SESSION['LoggedInUser']['role'] = 1) {

        $user['role'] = $_SESSION['LoggedInUser']['role'];
        //retrieve all products from the database
        $products = getAllProducts($db, 'admin', $user['role']);

    } else {
        header('Location: index.php');
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
    <title>Admin</title>
    <link rel="stylesheet" href="style.css" />
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
        <div class="addproduct">
            <h6><a href="admin/add.php">Product toevoegen</a></h6>
        </div>
        <div class="activeorders">
            <h6><a href="admin/orders.php">Openstaande bestellingen</a></h6>
        </div>
    </div>

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
                <p><?= substr($product['description'], 0, 250)?>... <a href="product.php?productid=<?= $product['id']?>"><small>meer weergeven&#155;</small></a></p>

            </div>

            <div class="productactions">
                <div class="productdelete">
                    <a href="admin/delete.php?productid=<?= $product['id'] ?>"><img src="icons/delete.svg" alt="Product verwijderen"></a>
                </div>
                <div class="productvisibility">
                    <a href="admin/visibility.php?productid=<?= $product['id'] ?>"><img src="images/visibility<?=$product['visible']?>.svg" alt="Product zichtbaarheid aanpassen"></a>
                </div>
                <div class="productedit">
                <a href="admin/edit.php?productid=<?= $product['id'] ?>"><img src="icons/edit.svg" alt="Product aanpassen"></a>
                </div>
                <div class="price">
                    <h3><?='€'.number_format($product['price'], 2, ",")?></h3>
                </div>


            </div>
            </div>

    <?php }; ?>
</div>
</body>
</html>
