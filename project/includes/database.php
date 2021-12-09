<?php

//Define DB credentials
const DB_HOST = "localhost";
const DB_USER = "root";
const DB_PASS = "";
const DB_NAME = "orders_test";

try {
    //New DB connection
    $db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME );

} catch (Exception $e) {
    //Set error
    $error = "Oops, try to fix your error please: " .
        $e->getMessage() . " on line " . $e->getLine() . " of " .
        $e->getFile();


}
