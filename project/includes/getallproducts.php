<?php
require_once 'database.php';
/** @var mysqli $db*/

function getAllProducts ($db, string $page, int $user_role = 0){
    //returns all entries from the `products` table in the database.
    if($user_role == 1 && $page == 'admin') {
        $query = "SELECT * FROM `products`";
    }
    elseif ($user_role == 0 && $page == 'admin'){
        exit();
    }
    else {
        $query = "SELECT * FROM `products` WHERE `visible` = 1";
    }
    $result = mysqli_query($db, $query)
    or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
    if(mysqli_num_rows($result) != 0) {
        $products = [];
        while($product = mysqli_fetch_assoc($result)) {
            $products[] = $product;
        }
        return $products;
    }
    else {
        $products = [];
        return $products;
    }
}