<?php
include "../config/db.php";
include "../auth/auth_check.php";

$id = $_GET['id'];

$stmt = mysqli_prepare($conn, "DELETE FROM products WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);

header("Location: index.php");
exit;
