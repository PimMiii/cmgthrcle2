<?php
session_start();

require_once '../includes/database.php';
require_once '../includes/products.php';
require_once '../includes/validation.php';
/** @var mysqli $db */

$errors = [];
// check if client is logged in as user
if (isset($_SESSION['LoggedInUser'])) {
    //check if loggen in user is admin
    $user_role = $_SESSION['LoggedInUser']['role'];
    if ($user_role == 1) {
        if (isset($_GET['productid'])) {
            $id = htmlentities(mysqli_escape_string($db, $_GET['productid']));
            $product_data = getProduct($db, $id, $user_role);
            $product = $product_data['product'];
            $errors = $product_data['errors'];

            if (isset($_GET['confirmation'])) {
                // retrieve the semi random string from the GET
                $confirmation_get = mysqli_escape_string($db, $_GET['confirmation']);
                // retrieve the control number and the confirmation string from the SEASSION
                $confirmation = $_SESSION['DeleteConfirmation'];
                // check if both strings match
                if ($confirmation_get == $confirmation['confirmation_str']) {
                    // validate the confirmation string using the control number and id
                    $pattern = "/^" . ($id * $confirmation['control']) . "/";
                    if (preg_match($pattern, $confirmation_get)) {
                        $query = "DELETE FROM `products` WHERE `id`='$id';";
                        $result = mysqli_query($db, $query)
                        or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);

                        unset($_SESSION['DeleteConfirmation']);
                        header('Location: ../admin.php');
                    } else {
                        $errors['confirmation'] = "Er ging iets mis in de verificatie, probeer later opnieuw.";
                    }

                } else {
                    $errors['confirmation'] = "Er ging iets mis in de verificatie, probeer later opnieuw.";
                }


            }
            // to protect against accidental deletion create a random confirmation value to send in the GET
            $control_number = random_int(0, 99);
            $random_characters = bin2hex(random_bytes(5));
            $confirmation_str = ($id * $control_number) . $random_characters;
            // save the control number and the created string in the session
            $confirm = array('control' => $control_number, 'confirmation_str' => $confirmation_str);
            $_SESSION['DeleteConfirmation'] = $confirm;

        } else {
            header('Location: ../admin.php');
        }
    } else {
        header('Location: ../index.php');
    }
} else {
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
    <title>Mijn Profiel</title>
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
    <?php if (!empty($errors)) { ?>
        <div class="errors"><h2> <?= $errors['confirmation'] ?></h2></div>
    <?php } ?>
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
                <div class="confirmdeletion">
                    <a href="delete.php?confirmation=<?= $confirmation_str ?>&productid=<?= $product['id'] ?>"><img
                                src="../icons/confirmdelete.svg" alt="Ja, Verwijder product."></a>
                </div>
                <div class="errors">
                    <h3>Weet je zeker dat je dit product wilt verwijderen?!</h3>
                </div>

            </div>
        </div>
    <?php }; ?>
</div>
</body>
</html>