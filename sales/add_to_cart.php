<?php
include "../config/db.php";

$id = (int) $_POST['product_id'];

$product = mysqli_query($conn, "
    SELECT id, name, price, stock
    FROM products
    WHERE id = $id
");

$p = mysqli_fetch_assoc($product);

// Check if already in cart
if (isset($_SESSION['cart'][$id])) {
    if ($_SESSION['cart'][$id]['qty'] < $p['stock']) {
        $_SESSION['cart'][$id]['qty']++;
    }
} else {
    $_SESSION['cart'][$id] = [
        'id' => $p['id'],
        'name' => $p['name'],
        'price' => $p['price'],
        'qty' => 1
    ];
}

header("Location: pos.php");
exit;
