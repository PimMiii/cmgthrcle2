<?php

require_once 'database.php';


function getUserProfile(int $user_id, $db)
{
    $id = $user_id;
    $query = "SELECT profiles.* FROM users INNER JOIN profiles ON users.profile_id = profiles.id WHERE users.id = $id";
    $result = mysqli_query($db, $query)
    or die('DB ERROR: ' . mysqli_error($db) . " with query: " . $query);
// check if database returned a profile
    if (mysqli_num_rows($result) == 0) {
        $_SESSION['LoggedInUser']['new_user'] = true;
    } else {
        $userprofile = mysqli_fetch_assoc($result);
        $_SESSION['LoggedInUser']['profile_id'] = $userprofile['id'];
    }
    return $userprofile;
}