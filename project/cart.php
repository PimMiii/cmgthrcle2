<?php

require_once 'includes/database.php';
/** @var mysqli $db */

// to set cart icon
if (isset($_COOKIE['cart'])) {
    $fullcart = 1;
} else {
    $fullcart = 0;
}




if (isset($_COOKIE['cart'])) {
    $cart = json_decode($_COOKIE['cart'], true);
    if (isset($_POST['update'])) {
    $cart = json_decode($_COOKIE['cart'], true);
    $cart_ids = array_keys($cart);
    $data = $_POST;
    $posted_ids = array_keys($data);
    // take the update key out of the array, so only the id's remain
    unset($posted_ids['update']);
    // check for every posted id, if it matches an id in cart, if so update the value in cart
    foreach ($posted_ids as $pid) {
        foreach ($cart_ids as $cid) {
            $check_cid = "id" . $cid . "_quantity";
            if ($check_cid == $pid) {
                $cart[$cid] = htmlentities(mysqli_escape_string($db, $data[$pid]));
            }
        }

    }
    setcookie('cart', json_encode($cart), time() + (30 * 86400), "/");
}
    $product_ids = array_keys($cart);
    $query = "SELECT `id`, `name`, `price`, `visible` FROM `products` WHERE ";
    $i = 0;
    foreach ($product_ids as $id) {
        $query = $query . "`id`= '" . htmlentities(mysqli_escape_string($db, $id)) . "' OR ";
        $i += 1;
    }
    $query = preg_replace("/ OR $/", ';', $query);
    $result = mysqli_query($db, $query)
    or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
    if (mysqli_num_rows($result) != 0) {
        $products = [];
        while ($product = mysqli_fetch_assoc($result)) {
            $product['quantity'] = $cart[$product['id']];
            $products[] = $product;
        }
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
    <title>Winkelwagen</title>
    <link rel="stylesheet" href="style.css"/>
</head>
<body>
<nav>
    <div class="navcontent">
        <div><a href="index.php"><img src="icons/logo.bmp" alt="Homepagina" class="logo"></a></div>
        <div class="search"></div>
        <div class="navright">
            <div><a href="login.php"><img src="icons/profile.svg" alt="Mijn Proffiel" class="profile"></a></div>
            <div><a href="cart.php"><img src="icons/cart<?= $fullcart ?>.svg" alt="Winkelwagen" class="cart"></a></div>
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
    <form action="" method="post">
        <table>
            <thead>
            <h2>Winkelwagen</h2>
            </thead>
            <tbody>
            <?php foreach ($products as $product) { ?>
                <tr>
                    <td class="thumbnail"><?php // insert thumbnail here ?></td>
                    <td class="productname">
                        <?= $product['name'] ?>
                    </td>
                    <td>

                    </td>
                    <td class="price">
                        <?= '€' . number_format($product['price'], 2, ",") ?>
                    </td>
                    <td class="quantity">
                        <input type="number" step="1" min="0" max="99" name="<?= 'id' . $product['id'] . '_quantity' ?>"
                               value="<?= $product['quantity'] ?>" required>
                    </td>
                    <td class="totalprice">
                        <?= '€' . number_format($product['price'] * $product['quantity'], 2, ",") ?>
                    </td>
                </tr>
            <?php }; ?>
            </tbody>
        </table>
        <input type="submit" name="update" id="update" value="Update winkelwagen">
    </form>
    <form>
        <h3>Totaal: €</h3>
        <input type="text" name="coupon" id="coupon" value="Kortingscode">
        <input type="submit" name="checkcoupon" id="checkcoupon" value="Check Kortingscode"
    </form>
    <div class="order">
        <a href="cart/order"><h6>Bestellen</h6></a>
    </div>

</div>
</body>
</html>
