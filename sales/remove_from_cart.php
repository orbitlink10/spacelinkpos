<?php
include "../config/db.php";

$id = (int) $_GET['id'];
unset($_SESSION['cart'][$id]);

header("Location: pos.php");
exit;
