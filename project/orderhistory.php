<?php
require_once 'includes/initialize.php';
require_once 'includes/database.php';
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Order History</title>
</head>
<body>
<h1>Order History</h1>

<table>
    <thead>
    <tr>
        <th></th>
        <th>#</th>
        <th>Artist</th>
        <th>Album</th>
        <th>Genre</th>
        <th>Year</th>
        <th>Tracks</th>
        <th colspan="2"></th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        <td colspan="9">&copy; My Collection</td>
    </tr>
    </tfoot>
    <tbody>
    <?php foreach ( as ) { ?>
        <tr>
            <td class="image"><img src="images" alt=""/></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td><a href="detail.php">Details</a></td>
        </tr>
    <?php } ?>
    </tbody>
</table>


<p><a href="index.php">Home</a></p>
<p><a href="profile.php">Profile</a></p>
</body>
</html>