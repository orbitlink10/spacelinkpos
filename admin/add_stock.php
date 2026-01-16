<?php
session_start();
include "../config/db.php";
include "../auth/auth_check.php";

// ADMIN ONLY
if ($_SESSION['role'] !== 'admin') {
    header("Location: ../home.php");
    exit;
}

// --- ADD CATEGORY HANDLER ---
if (isset($_POST['add_category'])) {
    $cat_name = trim($_POST['cat_name']);
    if ($cat_name !== "") {
        mysqli_query($conn, "INSERT INTO categories(name) VALUES('$cat_name')");
    }
    header("Location: add_stock.php");
    exit;
}

// Fetch products and categories
$products = mysqli_query($conn, "SELECT id, name, price FROM products ORDER BY name ASC");
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");

// --- STOCK UPDATE HANDLER ---
$message = '';
if(isset($_POST['submit'])){
    $barcode = mysqli_real_escape_string($conn, $_POST['barcode']);
    $cost_price = mysqli_real_escape_string($conn, $_POST['cost_price']);
    $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
    $selling_price = mysqli_real_escape_string($conn, $_POST['selling_price']);
    $quantity = mysqli_real_escape_string($conn, $_POST['quantity']);

    if($product_id !== ""){
        mysqli_query($conn,"UPDATE products SET stock = stock + $quantity, price='$selling_price' WHERE id='$product_id'");
        $message = "Stock updated successfully!";
        echo "<script>
            setTimeout(function(){
                window.location.href='../sales/pos.php';
            }, 1500);
        </script>";
    } else {
        $message = "Please select a valid product!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Add Stock</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{display:flex;height:100vh;overflow:hidden;background:#f3f4f8}

/* SIDEBAR */
.sidebar{
    width:240px;background:#15162b;color:white;padding:20px;display:flex;flex-direction:column;
}
.sidebar h2{font-size:22px;margin-bottom:30px;}
.menu a{
    padding:12px;border-radius:8px;margin-bottom:10px;color:#ccc;text-decoration:none;display:block;
}
.menu a.active,.menu a:hover{background:#7c3aed;color:white}

/* MAIN */
.main{flex:1;padding:25px;overflow-y:auto;}
.card{
    background:white;padding:25px;border-radius:12px;
    max-width:700px;margin:auto;
    box-shadow:0 4px 16px rgba(0,0,0,0.08);
}
h2{font-size:22px;font-weight:600;margin-bottom:18px;}
label{display:block;margin-bottom:6px;font-weight:500;}
input,select{
    width:100%;padding:10px;border-radius:8px;border:1px solid #ccc;font-size:15px;margin-bottom:12px;
}
input:focus,select:focus{border-color:#7c3aed;outline:none;}
.btn{
    padding:10px 18px;border:none;border-radius:8px;font-size:15px;cursor:pointer;font-weight:500;
}
.btn-submit{background:#7c3aed;color:white;}
.btn-submit:hover{background:#5b21b6;}
.btn-reset{background:#e5e7eb;color:#111;}
.btn-reset:hover{background:#d1d5db;}
.btn-add-cat{background:#10b981;color:white;margin-bottom:10px;}
.btn-add-cat:hover{background:#0f8f6d;}
.message{
    background:#ecfdf5;color:#065f46;border-left:4px solid #10b981;
    padding:10px 12px;border-radius:6px;font-weight:500;margin-bottom:12px;
}
.modal-bg{
    position:fixed;top:0;left:0;width:100%;height:100%;
    background:rgba(0,0,0,0.55);display:none;justify-content:center;align-items:center;
}
.modal{
    background:white;padding:20px;border-radius:10px;width:350px;
    box-shadow:0 4px 15px rgba(0,0,0,0.2);
}
.modal h3{margin-bottom:12px;text-align:center;font-size:18px;}
.close-btn{
    background:#ef4444;color:white;padding:8px 12px;border:none;border-radius:6px;cursor:pointer;
}
.close-btn:hover{background:#c81e1e;}
</style>

<script>
function openCatModal(){
    document.getElementById('modal-bg').style.display = 'flex';
}
function closeCatModal(){
    document.getElementById('modal-bg').style.display = 'none';
}
</script>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>Admin Panel</h2>
    <div class="menu">
        <a href="../sales/pos.php">Dashboard</a>
        <a href="../admin/products.php">Products</a>
        <a href="../admin/add_stock.php" class="active">Add Stock</a>
        <a href="../admin/out_of_stock.php">Out of Stock</a>
    </div>
</div>

<div class="main">
    <div class="card">
        <h2>➕ Add Stock</h2>

        <?php if($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>

        <!-- CATEGORY BUTTON -->
        <button type="button" class="btn btn-add-cat" onclick="openCatModal()">+ Add Category</button>

        <form method="POST">
            <label>Barcode *</label>
            <input type="text" name="barcode" required placeholder="Scan or enter barcode">

            <label>Cost Price *</label>
            <input type="number" step="0.01" name="cost_price" required placeholder="0.00">

            <label>Product *</label>
            <select name="product_id" required>
                <option value="">Select product...</option>
                <?php while($p = mysqli_fetch_assoc($products)){ ?>
                    <option value="<?= $p['id']?>"><?= $p['name']?></option>
                <?php } ?>
            </select>

            <label>Selling Price *</label>
            <input type="number" step="0.01" name="selling_price" required>

            <label>Quantity *</label>
            <input type="number" name="quantity" required placeholder="Enter quantity">

            <button type="submit" name="submit" class="btn btn-submit">✔ Submit</button>
            <button type="reset" class="btn btn-reset">Reset</button>
        </form>
    </div>
</div>

<!-- CATEGORY MODAL -->
<div id="modal-bg" class="modal-bg">
    <div class="modal">
        <h3>Add New Category</h3>
        <form method="POST">
            <input type="text" name="cat_name" placeholder="Category name..." required>
            <button type="submit" name="add_category" class="btn btn-submit" style="width:100%;margin-top:10px;">Add Category</button>
        </form>
        <button class="close-btn" onclick="closeCatModal()" style="width:100%;margin-top:10px;">Close</button>
    </div>
</div>

</body>
</html>
