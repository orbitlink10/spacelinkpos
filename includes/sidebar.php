<?php
$outStock = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE quantity <= 0")
)['total'];

$inStock = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE quantity > 0")
)['total'];
?>
