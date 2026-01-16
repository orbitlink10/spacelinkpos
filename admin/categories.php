<?php
session_start();
include "../config/db.php";
include "../auth/auth_check.php";

// Role check (optional)
if($_SESSION['role'] !== 'admin'){
    echo "<h2>Access Denied</h2>";
    exit;
}

// Add Category
if(isset($_POST['add_category'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$name')");
    $msg = "Category added!";
}

// Edit Category
if(isset($_POST['edit_category'])){
    $id = $_POST['id'];
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    mysqli_query($conn, "UPDATE categories SET name='$name' WHERE id=$id");
    $msg = "Category updated!";
}

// Delete Category
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
    $msg = "Category deleted!";
}

// Fetch List
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Categories</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body{font-family:Poppins,Arial;background:#f3f4f6;padding:20px}
h2{color:#111827;margin-bottom:10px}
.msg{background:#d1fae5;color:#065f46;padding:8px;border-radius:6px;margin-bottom:10px}
table{width:100%;border-collapse:collapse;background:white;border-radius:6px;overflow:hidden;margin-top:10px}
td,th{padding:10px;border-bottom:1px solid #e5e7eb;text-align:center}
th{background:#111827;color:white}
input{padding:10px;width:100%;border-radius:6px;border:1px solid #d1d5db;margin-bottom:8px}
button{background:#111827;color:white;border:none;padding:8px 16px;border-radius:6px;cursor:pointer}
button:hover{opacity:.85}
a.btn-del{background:#dc2626;color:white;padding:6px 10px;border-radius:6px;text-decoration:none}
a.btn-del:hover{background:#b91c1c}
a.back{background:#4f46e5;color:white;padding:8px 12px;border-radius:6px;text-decoration:none;margin-bottom:10px;display:inline-block}
</style>
</head>
<body>

<a href="../sales/pos.php" class="back">⬅ Back to Dashboard</a>

<h2>📂 Category Management</h2>

<?php if(isset($msg)) echo "<div class='msg'>$msg</div>"; ?>

<!-- Add Category Form -->
<form method="POST" style="max-width:400px;">
    <input type="text" name="name" placeholder="Enter category name..." required>
    <button type="submit" name="add_category">➕ Add Category</button>
</form>

<hr style="margin:15px 0;">

<h3>Existing Categories</h3>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Actions</th>
</tr>

<?php while($c=mysqli_fetch_assoc($categories)){ ?>
<tr>
    <td><?= $c['id'] ?></td>
    <td><?= $c['name'] ?></td>
    <td>
        <form method="POST" style="display:inline">
            <input type="hidden" name="id" value="<?= $c['id'] ?>">
            <input type="text" name="name" value="<?= $c['name'] ?>" required style="width:200px">
            <button name="edit_category">Save</button>
        </form>
        <a href="?delete=<?= $c['id'] ?>" class="btn-del" onclick="return confirm('Delete category?')">Delete</a>
    </td>
</tr>
<?php } ?>

</table>

</body>
</html>
