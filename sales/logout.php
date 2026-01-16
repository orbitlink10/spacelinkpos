<?php
session_start();

// Unset all session variables
$_SESSION = [];

// Destroy session
session_destroy();

// Redirect to home page
header("Location: http://localhost/spacelink/index.php");
exit;
