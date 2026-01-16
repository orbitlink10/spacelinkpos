<?php
include "../config/db.php";
include "../auth/auth_check.php";

$result = mysqli_query($conn, "
    SELECT 
        SUM(price * stock) AS inventory_value
    FROM products
    WHERE status='active'
");

$data = mysqli_fetch_assoc($result);
?>

<h2>Inventory Value</h2>

<p>Total Inventory Value: 
   <strong><?= number_format($data['inventory_value'], 2) ?></strong>
</p>
