<?php
session_start();
include "../config/db.php";
include "../auth/auth_check.php";

// Reset & init cart
if (isset($_GET['reset_cart'])) { unset($_SESSION['cart']); header("Location: billing.php"); exit; }
if (!isset($_SESSION['cart'])) { $_SESSION['cart'] = []; }

/* ============================
   ADD PRODUCT TO CART
============================ */
function addToCart($id, $conn) {
    $q = mysqli_query($conn, "SELECT * FROM products WHERE id='$id' LIMIT 1");
    if (mysqli_num_rows($q) === 1) {
        $p = mysqli_fetch_assoc($q);
        $cost = isset($p['cost_price']) ? floatval($p['cost_price']) : 0;
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['qty'] += 1;
        } else {
            $_SESSION['cart'][$id] = [
                'name'=>$p['name'],
                'price'=>floatval($p['price']),
                'cost'=>$cost,
                'qty'=>1
            ];
        }
    }
}

/* Add via click */
if (isset($_GET['add'])) addToCart($_GET['add'], $conn);

/* Add via barcode */
if (isset($_POST['barcode'])) {
    $barcode = mysqli_real_escape_string($conn, $_POST['barcode']);
    $q = mysqli_query($conn, "SELECT id FROM products WHERE barcode='$barcode' LIMIT 1");
    if (mysqli_num_rows($q)) {
        $p = mysqli_fetch_assoc($q);
        addToCart($p['id'], $conn);
    }
}

/* Update quantity */
if (isset($_POST['update_qty_id'])) {
    $pid = $_POST['update_qty_id'];
    $qty = max(1, intval($_POST['update_qty_val']));
    $_SESSION['cart'][$pid]['qty'] = $qty;
}

/* Remove item */
if (isset($_GET['remove'])) unset($_SESSION['cart'][$_GET['remove']]);

/* ============================
   COMPLETE SALE
============================ */
if (isset($_POST['complete_sale'])) {
    $total_amount = floatval($_POST['total_amount']);
    $amount_received = floatval($_POST['amount_received']);
    $customer_name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $customer_phone = mysqli_real_escape_string($conn, $_POST['customer_phone']);
    $user_id = $_SESSION['user_id'];
    $change = $amount_received - $total_amount;

    mysqli_query($conn,"
        INSERT INTO sales (user_id,total_amount,amount_received,change_due,customer_name,customer_phone)
        VALUES ($user_id,$total_amount,$amount_received,$change,'$customer_name','$customer_phone')
    ");

    $sale_id = mysqli_insert_id($conn);

    foreach ($_SESSION['cart'] as $pid => $item) {
        $qty = intval($item['qty']);
        $sell_price = floatval($item['price']);
        $cost_price = floatval($item['cost']);
        $line_total = $qty * $sell_price;

        mysqli_query($conn,"
            INSERT INTO sale_items (sale_id,product_id,quantity,price,cost_price,total)
            VALUES ($sale_id,$pid,$qty,$sell_price,$cost_price,$line_total)
        ");

        mysqli_query($conn,"UPDATE products SET stock = stock - $qty WHERE id = $pid");
    }

    $_SESSION['cart'] = [];
    header("Location: receipt.php?sale_id=".$sale_id);
    exit;
}

// Fetch products
$products = mysqli_query($conn, "SELECT * FROM products ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Make a Sale | POS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{display:flex;height:100vh;background:#eef1f7;overflow:hidden}

/* SIDEBAR (same as dashboard) */
.sidebar{
    width:250px;background:#1f2937;color:white;padding:25px 15px;display:flex;flex-direction:column;
}
.logo{font-size:20px;font-weight:700;color:white;text-align:center;margin-bottom:30px;}
.menu a{
    padding:12px 14px;margin-bottom:10px;border-radius:6px;
    display:flex;align-items:center;gap:10px;text-decoration:none;
    color:#d1d5db;font-size:14px;transition:.3s;
}
.menu a:hover,.menu .active{background:#6366f1;color:white;}

/* MAIN PANEL */
.main{flex:1;padding:25px;overflow-y:auto;}
.header{font-size:20px;font-weight:600;margin-bottom:15px;}

/* LAYOUT */
.container{display:flex;gap:20px;height:calc(100vh - 70px);}
.left{flex:1;overflow-y:auto;}
.right{width:380px;background:white;border-left:2px solid #e5e7eb;padding:20px;overflow-y:auto;border-radius:8px;}

.card{background:white;padding:15px;border-radius:10px;box-shadow:0 4px 12px rgba(0,0,0,0.06);margin-bottom:18px;}
.card-title{font-size:16px;font-weight:600;margin-bottom:10px;color:#111827;}

input,button{
    padding:10px;font-size:14px;border-radius:6px;border:1px solid #d1d5db;width:100%;margin-bottom:8px;
}
button{cursor:pointer;}

.add-btn{
    background:#10b981;border:none;color:white;padding:6px 10px;border-radius:6px;font-size:13px;
}
.add-btn:hover{background:#059669;}

.remove-btn{
    background:#ef4444;border:none;color:white;padding:6px 10px;border-radius:6px;font-size:13px;
}
.remove-btn:hover{background:#dc2626;}

.qty-input{width:50px;text-align:center;border:1px solid #ccc;border-radius:4px;}

table{width:100%;border-collapse:collapse;border-radius:8px;overflow:hidden;margin-top:10px;background:white;}
th,td{padding:10px;border-bottom:1px solid #e5e7eb;font-size:14px;text-align:center;}
th{background:#1f2937;color:white;}
hr{margin:10px 0;border:1px solid #e5e7eb;}

.checkout-btn{
    background:#6366f1;color:white;padding:12px;border:none;border-radius:6px;font-size:15px;width:100%;
}
.checkout-btn:hover{background:#4f46e5;}

.dashboard-btn{
    background:#374151;color:white;text-decoration:none;padding:8px 14px;border-radius:6px;
    font-size:13px;display:inline-block;margin-bottom:20px;
}
.dashboard-btn:hover{background:#111827;}
</style>

<script>
function filterProducts(){
    let q=document.getElementById("searchBox").value.toLowerCase();
    document.querySelectorAll(".product-row").forEach(row=>{
        row.style.display=row.dataset.name.toLowerCase().includes(q)?'':'none';
    });
}
</script>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">🛒 POS SYSTEM</div>
    <div class="menu">
        <a href="pos.php">📊 Dashboard</a>
        <a href="billing.php" class="active">💰 Make a Sale</a>
        <a href="#">👥 Customers</a>
        <a href="#">🏷 Discounts</a>
        <a href="#">⚙ Settings</a>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="main">
    <div class="header">Make a Sale</div>

    <div class="container">

        <!-- LEFT: PRODUCTS -->
        <div class="left">
            <a href="pos.php" class="dashboard-btn">⟵ Back to Dashboard</a>

            <div class="card">
                <div class="card-title">Search Product</div>
                <input type="text" id="searchBox" onkeyup="filterProducts()" placeholder="Search product name...">
                <form method="POST">
                    <input type="text" name="barcode" placeholder="Scan barcode...">
                </form>
            </div>

            <div class="card">
                <div class="card-title">Product List</div>
                <table>
                    <tr><th>Name</th><th>Price</th><th>Stock</th><th></th></tr>
                    <?php while($p=mysqli_fetch_assoc($products)){ ?>
                    <tr class="product-row" data-name="<?= $p['name'] ?>">
                        <td><?= $p['name'] ?></td>
                        <td>$<?= number_format($p['price'],2) ?></td>
                        <td><?= $p['stock'] ?></td>
                        <td><a class="add-btn" href="billing.php?add=<?= $p['id'] ?>">Add</a></td>
                    </tr>
                    <?php } ?>
                </table>
            </div>
        </div>

        <!-- RIGHT: CART -->
        <div class="right">
            <div class="card-title">Cart</div>

            <table>
                <tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th><th></th></tr>
                <?php $grand=0; foreach($_SESSION['cart'] as $id=>$item){
                    $line=$item['qty']*$item['price']; $grand+=$line;
                ?>
                <tr>
                    <td><?= $item['name'] ?></td>
                    <td>
                        <form method="POST" style="display:flex;gap:4px;justify-content:center;">
                            <input type="hidden" name="update_qty_id" value="<?= $id ?>">
                            <input type="number" name="update_qty_val" value="<?= $item['qty'] ?>" min="1" class="qty-input">
                            <button style="padding:4px 6px;">✔</button>
                        </form>
                    </td>
                    <td>$<?= number_format($item['price'],2) ?></td>
                    <td>$<?= number_format($line,2) ?></td>
                    <td><a class="remove-btn" href="billing.php?remove=<?= $id ?>">X</a></td>
                </tr>
                <?php } ?>
            </table>

            <hr>
            <h3>Total: $<?= number_format($grand,2) ?></h3>

            <form method="POST">
                <input type="hidden" name="total_amount" value="<?= $grand ?>">
                <input type="text" name="customer_name" placeholder="Customer Name" required>
                <input type="text" name="customer_phone" placeholder="Customer Phone (optional)">
                <input type="number" step="0.01" name="amount_received" placeholder="Amount Received" required>
                <button class="checkout-btn" name="complete_sale" type="submit">Complete Sale</button>
            </form>

        </div>
    </div>
</div>

</body>
</html>
