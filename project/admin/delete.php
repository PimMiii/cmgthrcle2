<?php
// delete a product

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Mijn Profiel</title>
    <link rel="stylesheet" href="../style.css"/>
</head>
<body>
<nav>
    <div class="navcontent">
        <div><a href="../index.php"><img src="../images/logo.bmp" alt="Homepagina" class="logo"></a></div>
        <div class="search"></div>
        <div class="navright">
            <div><a href="../login.php"><img src="../images/profile.svg" alt="Mijn Proffiel" class="profile"></a></div>
            <div><a href="../cart.php"><img src="../images/cart0.svg" alt="Winkelwagen" class="cart"></a></div>
        </div>
    </div>
</nav>
<div class="main">
    <div class="quickactions">
        <div class="logout">
            <h6><a href="../profile/logout.php">Uitloggen</a></h6>
        </div>
        <div class="editprofile">
            <h6><a href="../profile.php">Profiel</a></h6>
        </div>
        <div class="history">
            <h6><a href=../profile/orderhistory.php">Bestellingsgeschiedenis</a></h6>
        </div>
    </div>
</div>
</body>
</html>