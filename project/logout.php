<?php
session_start();

// delete session, logging out user
session_unset();
session_destroy();

// send client to the homepage
header('Location: index.php');