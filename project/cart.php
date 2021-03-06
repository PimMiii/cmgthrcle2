<?php
session_start();

require_once 'includes/database.php';
/** @var mysqli $db */

if (isset($_COOKIE['çart']) && empty($_COOKIE['cart'])) {
    // delete cookie if cart is empty in cookie
    unset($_COOKIE['cart']);
    setcookie('cart', '', time()-3600, '/');
}
// to set cart icon
if (isset($_COOKIE['cart']) && !empty($_COOKIE['cart'])) {
    $fullcart = 1;
} else {
    $fullcart = 0;
}

$errors = [];
$coupon['percentage'] = '';




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
                    if($data[$pid] !=0 && is_numeric($data[$pid])) {
                    $cart[$cid] = htmlentities(mysqli_escape_string($db, $data[$pid]));
                } elseif ($data[$pid] == 0 && is_numeric($data[$pid])){
                       unset($cart[$cid]);
                    } else {
                        $errors['quantity'] = "Geen geldig aantal.";
                    }
                }
            }

        }
        // check if cart is empty
        if(!empty($cart)) {
            setcookie('cart', json_encode($cart), time() + (30 * 86400), "/");
        }
        //if cart is empty, delete cookie
        else {
            unset($_COOKIE['cart']);
            setcookie('cart', '', time()-3600 , '/');
        }

    }
    // if order has been posted, save cart and discount to session
    if (isset($_POST['order'])) {
        $order = array(
            'cart'=> $cart,
            'coupon' => $coupon,
            'id' => bin2hex(random_bytes(10))
        );
        $_SESSION['order'] = $order;
        $id = $order['id'];
        //redirect client to checkout page, using the id as GET parameter
        header('Location: cart/checkout.php?orderid='.$id);

    }
    if(!empty($cart)) {
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

        $order_total = 0;
        if (mysqli_num_rows($result) != 0) {
            $products = [];
            while ($product = mysqli_fetch_assoc($result)) {
                $product['quantity'] = $cart[$product['id']];
                if (isset($product['quantity'])) {
                    $order_total += ($product['price'] * $product['quantity']);
                }
                $products[] = $product;
            }
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
            <div id="active"><a href="cart.php"><img src="icons/cart<?= $fullcart ?>.svg" alt="Winkelwagen" class="cart"></a></div>
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
    <?php if(!empty($cart)) { ?>
    <form action="" method="post">
        <table>
            <thead>
            <tr>
                <th>Winkelwagen</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($products as $product) { ?>
                <tr>
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
                        <span class="errors"><small><?= $errors['quantity'] ?? ''?></small></span>
                    </td>
                    <td class="totalprice">
                        <?= '€' . number_format($product['price'] * $product['quantity'], 2, ",") ?>
                    </td>
                </tr>
            <?php }; ?>

            <tr>
                <td></td>
            </tr>
            <tr>
                <td colspan="3"><h3>Totaal: € <?= number_format($order_total, 2, ",") ?></h3></td>
                <td colspan="2">
                    <input type="submit" name="update" id="update" value="Update winkelwagen"></td>
            </tr>
            </tbody>
        </table>

    </form>
    <div class="cartactions">
        <form action="" method="post">
            <table>
                <thead>
                <tr>
                    <th>Kortingscode</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="5"><span class="errors"><small><?= $errors['coupon'] ?? '' ?></small></h6></span></td>
                </tr>
                <tr>

                    <td colspan="2"><input type="text" name="coupon" id="coupon"
                                           value="<?= $coupon['name'] ?? "Kortingscode" ?>"></td>
                    <td colspan="2"><input type="submit" name="checkcoupon" id="checkcoupon" value="Check Kortingscode">
                    </td>
                </tr>


                </tbody>
            </table>
        </form>
        <form action="" method="post">
            <table>
                <thead>
                <tr>
                    <th>Bestellen</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="2">Totaal Winkelwagen:</td>
                    <td colspan="1"> €</td>
                    <td colspan="2"><?= number_format($order_total, 2, ",") ?></td>
                </tr>
                <tr>
                    <td colspan="2">Korting:<?= " " . $coupon['percentage'] . "%" ?? '' ?></td>
                    <td colspan="1"> €</td>
                    <td colspan="2">Korting in bedrag</td>
                </tr>
                <tr>
                    <td colspan="2">Bestelling Totaal:</td>
                    <td colspan="1"> €</td>
                    <td colspan="2"><?= number_format($order_total, 2, ",") ?> </td>

                </tr>
                <tr>

                    <td colspan="5"><input type="submit" name="order" id="order" value="Bestellen"</td>
                </tr>


                </tbody>
            </table>
        </form>
        <?php }else{ ?>
           <div class="product">

               <a href="index.php"><h3>winkelwagen is leeg, ga terug naar Home</h3></a>

           </div>
        <?php }; ?>


    </div>
</div>
</body>
</html>
