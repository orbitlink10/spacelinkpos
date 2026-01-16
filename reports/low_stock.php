<?php
include "../config/db.php";
include "../auth/auth_check.php";

$threshold = 5;

$result = mysqli_query($conn, "
    SELECT name, stock
    FROM products
    WHERE stock <= $threshold
    ORDER BY stock ASC
");
?>

<h2>Low Stock Products (≤ <?= $threshold ?>)</h2>

<table border="1" cellpadding="6">
<tr>
    <th>Product</th>
    <th>Stock</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= $row['name'] ?></td>
    <td><?= $row['stock'] ?></td>
</tr>
<?php } ?>
</table>
