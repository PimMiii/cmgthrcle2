<?php
session_start();
require_once '../includes/products.php';
require_once '../includes/database.php';
/** @var mysqli $db */
if(isset($_COOKIE['cart'])){
    $fullcart = 1;
} else {
    $fullcart = 0;
}
if (isset($_GET['productid'])) {
    $id = htmlentities(mysqli_escape_string($db, $_GET['productid']));
    if (isset($_SESSION['LoggedInUser'])) {
        $user_role = $_SESSION['LoggedInUser']['role'];
        if ($user_role == 1) {
            //retrieve product data from db
            $data = getProduct($db, $id, $user_role);
            $errors = $data['errors'];
            $product = $data['product'];
            // check current visibility
            if ($product['visible'] == 0) {
                $toggle_visibility = 1;
            } elseif ($product['visible'] == 1) {
                $toggle_visibility = 0;
            } else {
                $errors['id'] = "Er is iets misgegaan.";
                $errors['class'] = 'errors';
            }
            // update visibility in db
            if (empty($errors)) {
                $query = "UPDATE `products` SET `visible`='$toggle_visibility' WHERE `id` = '" . $product['id'] . "';";
                $result = mysqli_query($db, $query)
                or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
            }
            // redirect back to admin portal
            header('Location: ../admin.php');
        } else {
            header('Location: ../index.php');
        }

    } else {
        header('Location: ../index.php');
    }

} else {
//if client is not logged as admin redirect to homepage
    header('Location: ../index.php');
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
    <link rel="stylesheet" href="../style.css"/>
</head>
<body>
<nav>
    <div class="navcontent">
        <div><a href="../index.php"><img src="../icons/logo.bmp" alt="Homepagina" class="logo"></a></div>
        <div class="search"></div>
        <div class="navright">
            <div><a href="../login.php"><img src="../icons/profile.svg" alt="Mijn Proffiel" class="profile"></a></div>
            <div><a href="../cart.php"><img src="../icons/cart0.svg" alt="Winkelwagen" class="cart"></a></div>
        </div>
    </div>
</nav>
<div class="main">
    <div class="quickactions">
        <div class="logout">
            <h6><a href="../profile/logout.php">Uitloggen</a></h6>
        </div>
        <div class="addproduct">
            <h6><a href="add.php">Product toevoegen</a></h6>
        </div>
        <div class="activeorders">
            <h6><a href="orders.php">Openstaande bestellingen</a></h6>
        </div>
    </div>

    <div class="<?= $errors['class'] ?? '' ?>">
        <h2><?= $errors['id'] ?? "Als je deze pagina ziet is er iets misgegaan!" ?></h2>
    </div>

</div>
</body>
</html>
