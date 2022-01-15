<?php
require_once 'database.php';
/** @var mysqli $db */

function getAllProducts($db, string $page, int $user_role = 0)
{
    //returns all entries from the `products` table in the database
    if ($user_role == 1 && $page == 'admin') {
        $query = "SELECT * FROM `products`";
    } elseif ($user_role == 0 && $page == 'admin') {
        exit();
    } else {
        $query = "SELECT * FROM `products` WHERE `visible` = 1";
    }
    $result = mysqli_query($db, $query)
    or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
    if (mysqli_num_rows($result) != 0) {
        $products = [];
        while ($product = mysqli_fetch_assoc($result)) {
            $products[] = $product;
        }
        return $products;
    } else {
        $products = [];
        return $products;
    }
}

function getProduct($db, string $id, int $user_role = 0)
{
    // returns array with errors and the product requested through given id
    $errors = [];
    $product = [];
    // check if id is only numbers [0-9]
    if (!preg_match("/[^0-9]/", $id)) {
        $query = "SELECT * FROM `products` WHERE `id`= '$id'";
        $result = mysqli_query($db, $query)
        or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
        //check if only a single product was returned
        if (mysqli_num_rows($result) != 1) {
            $errors['product'] = "Sorry, er is iets fout gegaan. Dit product kan niet worden gevonden.";
        } else {
            $product = mysqli_fetch_assoc($result);
            //check if user has permissions to view this product
            if ($product['visible'] == 0 && $user_role != 1) {
                $errors['product'] = "Sorry, er is iets fout gegaan. Dit product kan niet worden gevonden.";
            }
        }
    } else {
        $errors['product'] = "Sorry, er is iets fout gegaan. Dit product kan niet worden gevonden.";
    }

    return array('errors' => $errors, 'product' => $product);

}