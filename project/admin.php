<?php
session_start();

if(isset($_SESSION['LoggedInUser'])){
    //check if user has admin role
    if($_SESSION['LoggedInUser']['role']!=1){
        // redirect client to homepage
        header('Location: index.php');
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
    <title>Admin</title>
</head>
<body>
<h1>Adminportaal</h1>

<p>Welkom admin</p>
<p><a href="logout.php">Uitloggen</a></p>
<p><a href="index.php">Home</a></p>
</body>
</html>
