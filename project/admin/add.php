<?php /** @noinspection DuplicatedCode */
session_start();

require_once '../includes/database.php';
require_once '../includes/products.php';
require_once '../includes/validation.php';
/** @var mysqli $db */

$errors = [];

if(isset($_COOKIE['cart'])){
    $fullcart = 1;
} else {
    $fullcart = 0;
}
// check if client is logged in as user
if (isset($_SESSION['LoggedInUser'])) {
    //check if loggen in user is admin
    $user_role = $_SESSION['LoggedInUser']['role'];
    if ($user_role == 1){
            if(isset($_POST['submit'])) {
                $data = array(
                    'name' => htmlentities(mysqli_escape_string($db, $_POST['name'])),
                    'description' => htmlentities($_POST['description']),
                    'price' => number_format(mysqli_escape_string($db, $_POST['price']), 4, "."),
                    'visible' => 0
                );
                //check if checkbox has been checked
                if (isset($_POST['visible'])) {
                    $data['visible'] = 1;
                }
                // validate data
                $validated_data = validateProduct($data, $errors);
                $data = $validated_data['validated_data'];
                $errors = $validated_data['errors'];

                // update product in database if there are no errors
                if (empty($errors)) {
                    $query = "INSERT INTO `products`(`name`, `description`, `price`, `visible`) VALUES ('" . $data['name'] . "','" . $data['description'] . "','" . $data['price'] . "','" . $data['visible'] . "');";
                    $result = mysqli_query($db, $query)
                    or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);

                    //redirect client back to adminportal
                    header('Location: ../admin.php');
                }
            }





    }else{header('Location: ../index.php');}
} else {header('Location: ../index.php');}
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
    <div>
        <form action="" method="post">
            <div>

                <h2>Productgegevens</h2>
                <div>
                    <label for="name">Productnaam: </label>
                    <input type="text" name="name" id="name"
                           value="<?= $data['name'] ?? 'Nieuw Product' ?>"
                           required>
                    <span class="errors"><?= $errors['name'] ?? '' ?></span>
                </div>
                <div>
                    <label for="price">Verkoopprijs: € </label>
                    <input type="number" name="price" id="price"
                           min="0.00" step="0.01"
                           value="<?= $data['price'] ?? '0.00' ?>"
                           required>
                    <span class="errors"><?= $errors['price'] ?? '' ?></span>
                </div>
                <div>
                    <label for="description">Productnaam: </label>
                    <span class="errors"><?= $errors['description'] ?? '' ?></span>
                    <textarea name="description" id="description"
                              required><?= $data['description'] ?? 'Nieuwe Beschrijving' ?></textarea>
                </div>
            </div>
            <div>
                <h2>Zichtbaarheid</h2>
                <div>
                    <input type="checkbox" id="visible" name="visible">
                    <label for="visible">Maak product zichtbaar voor klanten</label>
                </div>
            </div>
            <div>
                <input type="submit" name="submit" id="submit" value="Product toevoegen">
            </div>
        </form>
    </div>
</div>
</body>
</html>