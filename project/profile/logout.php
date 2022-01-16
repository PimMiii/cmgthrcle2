<?php
session_start();

// delete session, logging out user
session_unset();
session_destroy();

// redirect client to the homepage
header('Location: ../index.php');