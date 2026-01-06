<?php
// 1. Initialize the session
session_start();

// 2. Unset all of the session variables
$_SESSION = array();

// 3. Destroy the session
session_destroy();

// 4. Redirect to the Login Page
header("Location: login.php");
exit;
?>