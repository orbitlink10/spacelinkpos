<?php
include "../config/db.php";
include "../auth/auth_check.php";

if (empty($_SESSION['cart'])) {
    header("Location: pos.php");
    exit;
}

mysqli_begin_transaction($conn);

try {
    $user_id = $_SESSION['user_id'];
    $total = 0;

    foreach ($_SESSION['cart'] as $item) {
        $total += $item['price'] * $item['qty'];
    }

    // Save sale
    $stmt = mysqli_prepare($conn,
        "INSERT INTO sales (user_id, total_amount)
         VALUES (?, ?)"
    );
    mysqli_stmt_bind_param($stmt, "id", $user_id, $total);
    mysqli_stmt_execute($stmt);

    $sale_id = mysqli_insert_id($conn);

    // Save items + reduce stock
    foreach ($_SESSION['cart'] as $item) {

        mysqli_query($conn, "
            INSERT INTO sale_items (sale_id, product_id, quantity, price)
            VALUES ($sale_id, {$item['id']}, {$item['qty']}, {$item['price']})
        ");

        mysqli_query($conn, "
            UPDATE products
            SET stock = stock - {$item['qty']}
            WHERE id = {$item['id']}
        ");

        mysqli_query($conn, "
            INSERT INTO stock_movements (product_id, change_quantity, reason)
            VALUES ({$item['id']}, -{$item['qty']}, 'sale')
        ");
    }

    mysqli_commit($conn);

    $_SESSION['cart'] = [];
    header("Location: receipt.php?id=$sale_id");
    exit;

} catch (Exception $e) {
    mysqli_rollback($conn);
    echo "Checkout failed";
}
