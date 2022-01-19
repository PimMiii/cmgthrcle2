<?php
session_start();
require_once '../includes/database.php';
require_once '../includes/products.php';
require_once '../includes/profiles.php';
require_once '../includes/validation.php';
/** @var mysqli $db */


// to set cart icon
if (isset($_COOKIE['cart'])) {
    $fullcart = 1;
} else {
    $fullcart = 0;
}


$errors = [];

if (isset($_GET['orderid'])) {
    $cart_id = htmlentities(mysqli_escape_string($db, $_GET['orderid']));
    if (isset($_SESSION['LoggedInUser'])) {
        $user = $_SESSION['LoggedInUser'];
        $user['profile'] = getUserProfile($user['id'], $db);
    }
    if (isset($_POST['checkout'])) {
        if (isset($_SESSION['ProcessedOrder'])) {
            $processed_order = $_SESSION['ProcessedOrder'];
            $posted_profile = array(
                'first_name' => htmlentities(mysqli_escape_string($db, $_POST['first_name'])),
                'last_name' => htmlentities(mysqli_escape_string($db, $_POST['last_name'])),
                'email' => filter_var($_POST['email'], FILTER_SANITIZE_EMAIL),
                'street' => htmlentities(mysqli_escape_string($db, $_POST['street'])),
                'house_number' => htmlentities(mysqli_escape_string($db, $_POST['house_number'])),
                'postal_code' => htmlentities(mysqli_escape_string($db, $_POST['postal_code'])),
                'city' => htmlentities(mysqli_escape_string($db, $_POST['city'])));
            $validated_data = validateProfile($posted_profile, $errors);
            $posted_profile = $validated_data['validated_data'];
            $posted_profile['id'] =
            $errors[] = $validated_data['errors'];

            // compare posted_profile against known profile from user , and see if only the id is missing
            if (isset($user['profile']) && array_key_exists('id',array_diff($user['profile'], $posted_profile))) {
                $processed_order['user_id'] = $user['id'];
                $processed_order['profile_id'] = $user['profile']['id'];
            } elseif (isset($user['profile'])) {
                $user['profile'] = $posted_profile;
                $processed_order['user_id'] = $user['id'];
            } else {
                $user['profile'] = $posted_profile;
                $processed_order['user_id'] = null;
            }
            // check if profile_id is set.
            if (!isset($processed_order['profile_id'])) {
                $query = "INSERT INTO `profiles`(`first_name`, `last_name`, `street`, `house_number`,
                                                `postal_code`, `city`, `email`)
                            VALUES (
                                    '" . $user['profile']['first_name'] . "',
                                    '" . $user['profile']['last_name'] . "',
                                    '" . $user['profile']['street'] . "',
                                    '" . $user['profile']['house_number'] . "',
                                    '" . $user['profile']['postal_code'] . "',
                                    '" . $user['profile']['city'] . "',
                                    '" . $user['profile']['email'] . "');";
                $result = mysqli_query($db, $query)
                or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
                $processed_order['profile_id'] = mysqli_insert_id($db);
            }
            // profile_id should now be set
            if (isset($processed_order['profile_id'])) {
                //encode cart to JSON
                $cart = json_encode($processed_order['cart']);
                $order_total = $processed_order['total'];
                $order_paid = 0;
                $user_id = $processed_order['user_id'];
                $profile_id = $processed_order['profile_id'];
                // post order to db
                $query = "INSERT INTO `orders`(`cart`, `total_price`, `order_paid`, `user_id`, `profile_id`) 
                            VALUES ('$cart','$order_total','$order_paid','$user_id','$profile_id');";
                $result = mysqli_query($db, $query)
                or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
                //retrieve assigned id, to write into the lookup table with the hashed id
                $order_id = mysqli_insert_id($db);
                // create a hashed id
                $input = $processed_order['order_id'].time().$order_id;
                $hashed_id = hash('sha256', $input);
                $hashed_id = 'EG'.date('y', time()).$hashed_id;
                // write to db
                $query = "INSERT INTO `orderid_lookup`(`order_id`, `hashed_id`)
                            VALUES ('$order_id','$hashed_id');";
                $result = mysqli_query($db, $query)
                or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
                // discard the cart cookie, now that order is placed
                unset($_COOKIE['cart']);
                setcookie('cart', '', time()-3600 , '/');
                //redirect client to (simluated)payment page using the hashed id
                header('Location: simulatepayment.php?order='.$hashed_id);



            }

        }
    }
    if (isset($_SESSION['order'])) {
        $order = $_SESSION['order'];

    } else {
        header('Location: ../cart.php');
    }
    if ($cart_id == $order['id']) {
        $cart = $order['cart'];
        $coupon = $order['coupon'];
        $order_id = $order['id'];
        $order_total = 0;
        $product_ids = array_keys($cart);

        foreach ($product_ids as $product_id) {
            //fetch product for all product_ids
            $data = getProduct($db, $product_id, $user['role'] ?? 0);
            $errors[] = $data['errors'];
            $product = $data['product'];
            $product['quantity'] = $cart[$product_id];
            // calculate total price by multiplying product price by quantity
            $order_total += ($product['price'] * $product['quantity']);
            $products[] = $product;
        }
        if (!empty($coupon['percentage'])) {
            $discount = $coupon['percentage'] / 100;
            $order_total = $order_total / $discount;
        }
        $processed_order = array(
            'cart' => $cart,
            'coupon' => $coupon,
            'order_id' => $order_id,
            'total' => $order_total
        );
        $_SESSION['ProcessedOrder'] = $processed_order;
    } else {
        header('Location: ../cart.php');
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
    <link rel="stylesheet" href="../style.css"/>
</head>
<body>
<nav>
    <div class="navcontent">
        <div><a href="../index.php"><img src="../icons/logo.bmp" alt="Homepagina" class="logo"></a></div>
        <div class="search"></div>
        <div class="navright">
            <div><a href="../login.php"><img src="../icons/profile.svg" alt="Mijn Proffiel" class="profile"></a></div>
            <div id="active"><a href="../cart.php"><img src="../icons/cart<?= $fullcart ?>.svg" alt="Winkelwagen"
                                                        class="cart"></a></div>
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
        <table>
            <thead>
            <tr>
                <th colspan="2">Product</th>
                <th> Productprijs</th>
                <th> Aantal</th>
                <th> Prijs</th>
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
                        <?= $product['quantity'] ?>
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
                <td colspan="3"><h3>Totaal: € <?= number_format($order_total, 2, ",", ".") ?></h3></td>


            </tr>
            </tbody>
        </table>
    </div>
    <div>
        <form action="" method="post">
            <div>
                <h2>Verzendgegevens</h2>
                <div><a href="../login.php"><h3>Heeft u een account log dan hier in.</h3></a></div>

                <h2>Naamgegevens</h2>
                <div>
                    <label for="first_name">Voornaam: </label>
                    <input type="text" name="first_name" id="first_name"
                           value="<?= $posted_profile ['first_name'] ?? $user['profile']['first_name'] ?? '' ?>"
                           required>
                    <span class="errors"><?= $errors['first_name'] ?? '' ?></span>
                </div>
                <div>
                    <label for="last_name">Achternaam: </label>
                    <input type="text" name="last_name" id="last_name"
                           value="<?= $posted_profile ['last_name'] ?? $user['profile']['last_name'] ?? '' ?>"
                           required>
                    <span class="errors"><?= $errors['last_name'] ?? '' ?></span>
                </div>
            </div>
            <div>
                <h2>E-mailgegevens</h2>
                <div>
                    <label for="email">E-mail adres: </label>
                    <input type="email" name="email" id="email"
                           value="<?= $posted_profile ['email'] ?? $user['profile']['email'] ?? $user['email'] ?? '' ?>"
                           required>
                    <span class="errors"><?= $errors['email'] ?? '' ?></span>
                </div>
            </div>
            <div>
                <h2>Adresgegevens</h2>
                <div>
                    <label for="street">Straat: </label>
                    <input type="text" name="street" id="street"
                           value="<?= $posted_profile ['street'] ?? $user['profile']['street'] ?? '' ?>" required>
                    <span class="errors"><?= $errors['street'] ?? '' ?></span>
                </div>
                <div>
                    <label for="house_number">Huisnummer: </label>
                    <input type="text" name="house_number" id="house_number"
                           value="<?= $posted_profile ['house_number'] ?? $user['profile']['house_number'] ?? '' ?>"
                           required>
                    <span class="errors"><?= $errors['house_number'] ?? '' ?></span>
                </div>
                <div>
                    <label for="postal_code">Postcode: </label>
                    <input type="text" name="postal_code" id="postal_code"
                           value="<?= $posted_profile ['postal_code'] ?? $user['profile']['postal_code'] ?? '' ?>"
                           required>
                    <span class="errors"><?= $errors['postal_code'] ?? '' ?></span>
                </div>
                <div>
                    <label for="city">Woonplaats: </label>
                    <input type="text" name="city" id="city"
                           value="<?= $posted_profile ['city'] ?? $user['profile']['city'] ?? '' ?>" required>
                    <span class="errors"><?= $errors['city'] ?? '' ?></span>
                </div>
            </div>
            <div>
                <input type="hidden" name="profileid" id="profileid" value="<?= $user['profile']['id'] ?? '' ?>">
                <input type="submit" name="checkout" id="checkout" value="Bestellen">


            </div>

        </form>
    </div>
</div>
