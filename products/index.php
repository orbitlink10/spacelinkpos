<?php
include "../config/db.php";
include "../auth/auth_check.php";

$result = mysqli_query($conn, "
    SELECT products.*, categories.name AS category
    FROM products
    LEFT JOIN categories ON products.category_id = categories.id
");
?>

<h2>Products</h2>
<a href="add.php">➕ Add Product</a>
<br><br>

<table border="1" cellpadding="8">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Category</th>
    <th>Price</th>
    <th>Stock</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php while ($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?= $row['id'] ?></td>
    <td><?= $row['name'] ?></td>
    <td><?= $row['category'] ?? 'None' ?></td>
    <td><?= $row['price'] ?></td>
    <td><?= $row['stock'] ?></td>
    <td><?= $row['status'] ?></td>
    <td>
        <a href="edit.php?id=<?= $row['id'] ?>">✏ Edit</a> |
        <a href="delete.php?id=<?= $row['id'] ?>"
           onclick="return confirm('Delete this product?')">🗑 Delete</a>
    </td>
</tr>
<?php } ?>
</table>
