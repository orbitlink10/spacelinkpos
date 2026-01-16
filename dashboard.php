<?php
include "config/db.php";
include "auth/auth_check.php";
?>

<h1>POS Dashboard</h1>

<p>Welcome! Role: <?= $_SESSION['role']; ?></p>

<a href="auth/logout.php">Logout</a>
