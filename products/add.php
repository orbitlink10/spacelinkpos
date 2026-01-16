<?php
include "../config/db.php";
include "../auth/auth_check.php";

$categories = mysqli_query($conn, "SELECT * FROM categories");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $barcode = $_POST['barcode'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];

    $stmt = mysqli_prepare($conn, "
        INSERT INTO products (category_id, name, barcode, price, stock)
        VALUES (?, ?, ?, ?, ?)
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "issdi",
        $category_id,
        $name,
        $barcode,
        $price,
        $stock
    );

    mysqli_stmt_execute($stmt);

    header("Location: index.php");
    exit;
}
?>

<h2>Add Product</h2>

<form method="POST">
    <input type="text" name="name" placeholder="Product name" required><br><br>

    <select name="category_id">
        <option value="">-- Select Category --</option>
        <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
            <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
        <?php } ?>
    </select><br><br>

    <input type="text" name="barcode" placeholder="Barcode"><br><br>
    <input type="number" step="0.01" name="price" placeholder="Price" required><br><br>
    <input type="number" name="stock" placeholder="Stock" required><br><br>

    <button type="submit">Save Product</button>
</form>
