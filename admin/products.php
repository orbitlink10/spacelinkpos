<?php
session_start();
include "../config/db.php";
include "../auth/auth_check.php";

if ($_SESSION['role'] !== 'admin') {
    echo "<h2>Access Denied</h2>";
    exit;
}

// Load categories for dropdown
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

// Add product submit
if (isset($_POST['add'])) {
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $barcode = !empty($_POST['barcode']) ? $_POST['barcode'] : "BC".time();
    $supplier = $_POST['supplier'];
    $price = $_POST['price'];
    $cost = $_POST['cost_price'];
    $stock = $_POST['stock'];
    $date = $_POST['date'];

    mysqli_query($conn, "
        INSERT INTO products(category_id,name,barcode,supplier,price,cost_price,stock,date_recorded)
        VALUES('$category_id','$name','$barcode','$supplier','$price','$cost','$stock','$date')
    ");

    $msg = "Product Added Successfully!";
}

// List products
$products = mysqli_query($conn,"
SELECT p.*, c.name as category_name FROM products p
LEFT JOIN categories c ON p.category_id = c.id
ORDER BY p.id DESC
");

?>
<!DOCTYPE html>
<html>
<head>
<title>Product Management</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f3f4f8;display:flex;height:100vh;overflow:hidden}

/* Sidebar */
.sidebar{
    width:240px;background:#15162b;color:white;
    display:flex;flex-direction:column;padding:20px;
}
.sidebar h2{font-size:22px;margin-bottom:30px;}
.menu a{
    text-decoration:none;color:#cbd5e1;padding:12px;
    display:block;margin-bottom:6px;border-radius:6px;
}
.menu a:hover, .active{background:#7c3aed;color:white}

/* Submenu */
.menu-item a{display:flex;justify-content:space-between;align-items:center;}
.submenu{
    max-height:0;overflow:hidden;margin-left:10px;
    transition:max-height .3s ease;
}
.submenu a{
    font-size:14px;padding:8px;margin-bottom:4px;
}
.submenu.open{max-height:500px}

/* Main */
.main{
    flex:1;padding:24px;overflow-y:auto;
}
.header{
    font-size:22px;font-weight:600;margin-bottom:18px;
}
.card{
    background:white;border-radius:12px;padding:20px;
    box-shadow:0 5px 20px rgba(0,0,0,0.08);margin-bottom:20px;
}
input,select{
    padding:10px;border-radius:6px;border:1px solid #d1d5db;width:100%;margin-bottom:8px;
}
button{
    padding:10px 14px;border:none;border-radius:6px;cursor:pointer;
}
.btn-primary{background:#7c3aed;color:white;width:100%;}
.btn-primary:hover{background:#5b21b6;}

/* Table */
table{
    width:100%;border-collapse:collapse;background:white;border-radius:8px;overflow:hidden;
}
th,td{
    border-bottom:1px solid #e5e7eb;padding:10px;text-align:left;font-size:14px;
}
th{background:#111827;color:white;}
.badge{background:#ef4444;color:white;padding:2px 8px;border-radius:4px;font-size:12px;}
</style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h2>Point Of Sale</h2>
    <div class="menu">
        <a href="../sales/pos.php">Admin Dashboard</a>

        <div class="menu-item">
            <a href="javascript:void(0)" onclick="toggleStock()">
                📦 Stock <span id="arrow">▾</span>
            </a>
            <div class="submenu" id="submenu">
                <a href="add_stock.php">➕ Add Stock</a>
                <a href="products.php" class="active">📋 Products</a>
            </div>
        </div>

        <a href="../sales/billing.php">Make a Sale</a>
        <a href="../sales/today_sales.php">Today's Summary</a>
    </div>
</div>

<!-- Main -->
<div class="main">
    <div class="header">📦 Product Management</div>

    <?php if(!empty($msg)): ?>
    <div style="background:#dcfce7;padding:10px;border-left:4px solid #22c55e;margin-bottom:15px;color:#166534;">
        <?= $msg ?>
    </div>
    <?php endif; ?>

    <!-- Add Product Form -->
    <div class="card">
        <h3 style="margin-bottom:10px;">➕ Add Product</h3>
        <form method="POST">
            <select name="category_id" required>
                <option value="">Select Category</option>
                <?php while($c=mysqli_fetch_assoc($categories)){ ?>
                    <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                <?php } ?>
            </select>

            <input name="name" placeholder="Product Name" required>
            <input name="barcode" placeholder="Barcode (optional)">
            <input name="supplier" placeholder="Supplier" required>
            <input type="number" step="0.01" name="price" placeholder="Selling Price" required>
            <input type="number" step="0.01" name="cost_price" placeholder="Cost Price" required>
            <input type="number" name="stock" placeholder="Stock Quantity" required>
            <input type="date" name="date" value="<?=date('Y-m-d')?>" required>

            <button class="btn-primary" name="add">Add Product</button>
        </form>
    </div>

    <!-- Product List -->
    <div class="card">
        <h3 style="margin-bottom:10px;">📋 Product List</h3>
        <table>
            <tr>
                <th>#</th><th>Name</th><th>Category</th><th>Price</th><th>Cost</th><th>Stock</th>
            </tr>

            <?php while($p=mysqli_fetch_assoc($products)){ ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><?= $p['name'] ?></td>
                <td><?= $p['category_name'] ?></td>
                <td>$<?= number_format($p['price'],2) ?></td>
                <td>$<?= number_format($p['cost_price'],2) ?></td>
                <td>
                    <?= $p['stock'] ?> 
                    <?= $p['stock'] <= 3 ? "<span class='badge'>LOW</span>" : "" ?>
                </td>
            </tr>
            <?php } ?>

        </table>
    </div>
</div>

<script>
function toggleStock(){
    let sub = document.getElementById('submenu');
    let arrow = document.getElementById('arrow');
    sub.classList.toggle('open');
    arrow.innerHTML = sub.classList.contains('open') ? "▴" : "▾";
}
</script>
</body>
</html>
