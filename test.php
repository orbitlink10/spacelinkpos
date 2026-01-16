<?php
session_start();
include "../config/db.php";
include "../auth/auth_check.php";

/* ===========================
   STOCK COUNTS
=========================== */
$outStock = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE stock <= 0")
)['total'];

$inStock = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE stock > 0")
)['total'];

/* ===========================
   SALES STATISTICS
=========================== */

/* TODAY SALES */
$todaySales = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT SUM(total_amount) AS total 
        FROM sales 
        WHERE DATE(created_at) = CURDATE()
    ")
)['total'] ?? 0;

/* WEEK SALES */
$weekSales = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT SUM(total_amount) AS total 
        FROM sales 
        WHERE YEARWEEK(created_at, 1) = YEARWEEK(CURDATE(), 1)
    ")
)['total'] ?? 0;

/* MONTH SALES */
$monthSales = mysqli_fetch_assoc(
    mysqli_query($conn, "
        SELECT SUM(total_amount) AS total 
        FROM sales 
        WHERE YEAR(created_at) = YEAR(CURDATE()) 
        AND MONTH(created_at) = MONTH(CURDATE())
    ")
)['total'] ?? 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>POS Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{background:#f3f4f8;display:flex;height:100vh;overflow:hidden}

/* ===== SIDEBAR ===== */
.sidebar{
    width:240px;
    background:#15162b;
    color:#fff;
    display:flex;
    flex-direction:column;
    padding:20px;
}
.sidebar h2{
    font-size:22px;
    margin-bottom:30px;
}
.menu a{
    display:flex;
    align-items:center;
    padding:12px;
    margin-bottom:10px;
    color:#ccc;
    text-decoration:none;
    border-radius:8px;
    transition:.3s;
}
.menu a.active,
.menu a:hover{
    background:#7c3aed;
    color:#fff;
}

/* ===== STOCK SUBMENU ===== */
.menu-item a{
    display:flex;
    justify-content:space-between;
}
.submenu{
    max-height:0;
    overflow:hidden;
    margin-left:15px;
    transition:max-height .4s ease;
}
.submenu.open{max-height:500px;}
.submenu a{
    font-size:14px;
    color:#ccc;
}
.submenu a:hover{background:#2b2d5c;color:#fff}
.submenu-active{background:#7c3aed!important;color:#fff}

/* BADGES */
.badge{
    background:#4f46e5;
    padding:3px 8px;
    border-radius:20px;
    font-size:12px;
    color:white;
    margin-left:auto;
}
.badge.danger{background:#ef4444}

/* ===== MAIN CONTENT ===== */
.main{
    flex:1;
    padding:25px;
    overflow-y:auto;
}
.header{
    font-size:22px;
    font-weight:600;
    margin-bottom:20px;
}

/* ===== DASHBOARD GRID ===== */
.dashboard{
    display:grid;
    grid-template-columns:3fr 1.2fr;
    gap:20px;
}

/* ===== CARDS ===== */
.card{
    background:#fff;
    border-radius:16px;
    padding:20px;
    box-shadow:0 10px 30px rgba(0,0,0,0.05);
}
.card-header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:15px;
}
.card-header h3{font-size:18px}

/* ===== ACTION BUTTONS ===== */
.actions button{
    background:#fff;
    border:1px solid #ddd;
    padding:6px 12px;
    margin-left:5px;
    border-radius:6px;
    cursor:pointer;
}
.actions button:hover{background:#f0f0f0}

/* ===== STATS ===== */
.stats{
    display:flex;
    flex-direction:column;
    gap:15px;
}
.stat{
    padding:20px;
    border-radius:16px;
    text-align:center;
}
.purple{background:#7c3aed;color:#fff}
.green{background:#fff;color:#16a34a}
.orange{background:#fff;color:#f97316}
.blue{background:#fff;color:#7c3aed}

.stat h1{font-size:34px}
.stat p{font-size:14px}
</style>
</head>
<body>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">
    <h2>Point Of Sale</h2>

    <div class="menu">
        <a href="pos.php" class="<?= basename($_SERVER['PHP_SELF']) == 'pos.php' ? 'active' : '' ?>">
            Admin Dashboard
        </a>

        <!-- STOCK MENU -->
        <div class="menu-item">
            <a href="javascript:void(0)" onclick="toggleStockMenu()">
                📦 Stock <span id="stockArrow">▾</span>
            </a>

            <div class="submenu" id="stockSubMenu">
                <a href="../admin/add_stock.php">➕ Add Stock</a>

                <a href="../admin/products.php">
                    📋 Current Stock
                    <span class="badge"><?= $inStock ?></span>
                </a>

                <a href="../admin/out_of_stock.php">
                    ❌ Out Of Stock
                    <span class="badge danger"><?= $outStock ?></span>
                </a>
            </div>
        </div>

        <a href="billing.php" class="<?= basename($_SERVER['PHP_SELF']) == 'billing.php' ? 'active' : '' ?>">
            Billing
        </a>

        <a href="#">Customers</a>
        <a href="#">Discount</a>
        <a href="#">Settings</a>
    </div>
</div>

<!-- ===== MAIN ===== -->
<div class="main">
    <div class="header">Monthly Sales</div>

    <!-- ===== TODAY SALES SUMMARY LINK ===== -->
    <div style="margin-bottom:20px;">
        <a href="today_sales.php" 
           style="background:#7c3aed;color:white;padding:10px 14px;
                  border-radius:6px;text-decoration:none;display:inline-block;">
            📄 View Today's Sales Summary
        </a>
    </div>

    <div class="dashboard">
        <div class="card">
            <div class="card-header">
                <h3>Monthly Sales</h3>
                <div class="actions">
                    <button onclick="location.reload()">Refresh</button>
                </div>
            </div>

            <div style="font-size:20px;font-weight:600;color:#333;">
                <?= date("F Y") ?>: 
                <span style="color:#16a34a;">$<?= number_format($monthSales,2) ?></span>
            </div>
        </div>

        <div class="stats">
            <div class="stat purple">
                <h1><?= $outStock ?></h1>
                <p>Out Of Stock Products</p>
            </div>

            <div class="stat green">
                <h1>$<?= number_format($monthSales,2) ?></h1>
                <p>This Month Sales</p>
            </div>

            <div class="stat orange">
                <h1>$<?= number_format($weekSales,2) ?></h1>
                <p>This Week Sales</p>
            </div>

            <div class="stat blue">
                <h1>$<?= number_format($todaySales,2) ?></h1>
                <p>Today Sales</p>
            </div>
        </div>
    </div>
</div>

<script>
function toggleStockMenu(){
    const menu = document.getElementById('stockSubMenu');
    const arrow = document.getElementById('stockArrow');
    menu.classList.toggle('open');
    arrow.innerHTML = menu.classList.contains('open') ? "▴" : "▾";
}

/* AUTO OPEN ACTIVE SUBMENUS */
const currentPage = window.location.pathname;
document.querySelectorAll('#stockSubMenu a').forEach(link=>{
    if(currentPage.includes(link.getAttribute('href'))){
        link.classList.add('submenu-active');
        document.getElementById('stockSubMenu').classList.add('open');
        document.getElementById('stockArrow').innerHTML="▴";
    }
});
</script>

</body>
</html>
