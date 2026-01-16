<?php
include "../config/db.php";
include "../auth/auth_check.php";

$id = $_GET['id'];

$product = mysqli_query($conn, "SELECT * FROM products WHERE id = $id");
$data = mysqli_fetch_assoc($product);

$categories = mysqli_query($conn, "SELECT * FROM categories");

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $category_id = $_POST['category_id'];
    $barcode = $_POST['barcode'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $status = $_POST['status'];

    $stmt = mysqli_prepare($conn, "
        UPDATE products
        SET category_id=?, name=?, barcode=?, price=?, stock=?, status=?
        WHERE id=?
    ");

    mysqli_stmt_bind_param(
        $stmt,
        "issdiss",
        $category_id,
        $name,
        $barcode,
        $price,
        $stock,
        $status,
        $id
    );

    mysqli_stmt_execute($stmt);

    header("Location: index.php");
    exit;
}
?>

<h2>Edit Product</h2>

<form method="POST">
    <input type="text" name="name" value="<?= $data['name'] ?>" required><br><br>

    <select name="category_id">
        <option value="">-- Select Category --</option>
        <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
            <option value="<?= $cat['id'] ?>"
                <?= $cat['id'] == $data['category_id'] ? 'selected' : '' ?>>
                <?= $cat['name'] ?>
            </option>
        <?php } ?>
    </select><br><br>

    <input type="text" name="barcode" value="<?= $data['barcode'] ?>"><br><br>
    <input type="number" step="0.01" name="price" value="<?= $data['price'] ?>" required><br><br>
    <input type="number" name="stock" value="<?= $data['stock'] ?>" required><br><br>

    <select name="status">
        <option value="active" <?= $data['status']=='active'?'selected':'' ?>>Active</option>
        <option value="inactive" <?= $data['status']=='inactive'?'selected':'' ?>>Inactive</option>
    </select><br><br>

    <button type="submit">Update Product</button>
</form>
