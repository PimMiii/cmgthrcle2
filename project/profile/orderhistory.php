<?php
require_once '../includes/initialize.php';
require_once '../includes/database.php';
// to set cart icon
if(isset($_COOKIE['cart'])){
    $fullcart = 1;
} else {
    $fullcart = 0;
}
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Easygoods</title>
    <link rel="stylesheet" href="../style.css" />
</head>
<body>
<nav>
    <div class="navcontent">
        <div><a href="../index.php"><img src="../icons/logo.bmp" alt="Homepagina" class="logo"></a></div>
        <div class="search"></div>
        <div class="navright">
            <div id="active"><a href="../login.php"><img src="../icons/profile.svg" alt="Mijn Proffiel" class="profile"></a></div>
            <div><a href="../cart.php"><img src="../icons/cart<?=$fullcart?>.svg" alt="Winkelwagen" class="cart"></a></div>
        </div>
    </div>
</nav>
<h1>Order History</h1>
<div class="main">
<table>
    <thead>
    <tr>
        <th></th>
        <th>#</th>
        <th>Artist</th>
        <th>Album</th>
        <th>Genre</th>
        <th>Year</th>
        <th>Tracks</th>
        <th colspan="2"></th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <td colspan="9">&copy; My Collection</td>
    </tr>
    </tfoot>
    <tbody>

    </tbody>
</table>



</div>
</body>
</html>