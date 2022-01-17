<?php
require_once '../includes/database.php';
/** @var mysqli $db */


if (isset($_GET['productid'])) {
    $id = htmlentities(mysqli_escape_string($db, $_GET['productid']));
    if (isset($_COOKIE['cart'])) {
        $cookie_value = json_decode($_COOKIE['cart'], true);
        if(isset($_COOKIE['cart'][$id])) {
            $quantity = $cookie_value[$id] + 1;
        } else {
            $quantity = 1;
        }
        $cookie_value[$id] = $quantity;
        setcookie('cart', json_encode($cookie_value), time() + strtotime('30 days'), "/");
    } else {
        $quantity = 1;
        $cookie_value[$id] = $quantity;
        setcookie('cart', json_encode($cookie_value), time() + strtotime('30 days'), "/");
    }
    if(isset($_GET['page'])){
        $page = htmlentities(mysqli_escape_string($db,$_GET['page']));
        header('Location: ../'.$page.'.php');
    } else {
        header('Location: ../index.php');
    }
}