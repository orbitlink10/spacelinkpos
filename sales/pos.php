<?php
session_start();
include "../config/db.php";
include "../auth/auth_check.php";

// ===========================
// SESSION TIMEOUT (15 Minutes)
// ===========================
$timeout_duration = 900;

if (isset($_SESSION['LAST_ACTIVITY']) &&
   (time() - $_SESSION['LAST_ACTIVITY']) > $timeout_duration) {

    session_unset();
    session_destroy();
    header("Location: ../index.php?timeout=1");
    exit;
}
$_SESSION['LAST_ACTIVITY'] = time();

// ===========================
// STOCK COUNTS
// ===========================
$outStock = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE stock <= 0")
)['total'];

$inStock = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM products WHERE stock > 0")
)['total'];

// ===========================
// SALES STATISTICS
// ===========================
$todaySales = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM sales WHERE DATE(created_at)=CURDATE()")
)['total'] ?? 0;

$weekSales = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM sales WHERE YEARWEEK(created_at,1)=YEARWEEK(CURDATE(),1)")
)['total'] ?? 0;

$monthSales = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM sales WHERE YEAR(created_at)=YEAR(CURDATE()) AND MONTH(created_at)=MONTH(CURDATE())")
)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>POS Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{display:flex;height:100vh;background:#eef1f7;overflow:hidden}

/* SIDEBAR */
.sidebar{
    width:250px;
    background:#1f2937;
    color:white;
    display:flex;
    flex-direction:column;
    padding:25px 15px;
}
.sidebar .logo{
    text-align:center;
    font-size:20px;
    font-weight:700;
    margin-bottom:30px;
    color:#fff;
}
.menu a{
    padding:12px 14px;
    display:flex;
    align-items:center;
    gap:10px;
    font-size:14px;
    text-decoration:none;
    color:#d1d5db;
    border-radius:6px;
    margin-bottom:10px;
    transition:.3s;
}
.menu a:hover,
.menu a.active{
    background:#6366f1;
    color:white;
}

/* SUBMENU */
.menu-item a{justify-content:space-between;}
.submenu{max-height:0;overflow:hidden;margin-left:20px;transition:max-height .4s ease;}
.submenu.open{max-height:300px;}
.submenu a{font-size:13px;margin-bottom:8px;color:#d1d5db}
.submenu a:hover{background:#4b4fe2;color:#fff}
.badge{
    padding:2px 6px;
    background:#4f46e5;
    border-radius:4px;
    font-size:11px;
    margin-left:5px;
    color:white;
}
.badge.danger{background:#ef4444}

/* MAIN AREA */
.main{
    flex:1;
    padding:30px;
    overflow-y:auto;
}
.header{font-size:22px;font-weight:600;margin-bottom:10px}

/* TODAY LINK */
.summary-link{
    background:#4f46e5;
    padding:10px 14px;
    border-radius:6px;
    color:white;
    text-decoration:none;
    display:inline-block;
    font-size:14px;
    margin:10px 0 20px 0;
}

/* DASHBOARD GRID */
.dashboard{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:25px;
}

/* CARD */
.card{
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 15px rgba(0,0,0,0.05);
}
.card h3{font-size:18px;margin-bottom:10px;font-weight:600}

/* STATS */
.stats{
    display:flex;
    flex-direction:column;
    gap:15px;
}
.stat{
    background:white;
    padding:18px;
    border-radius:12px;
    text-align:center;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
}
.stat h1{font-size:26px;margin-bottom:5px}
.stat p{font-size:12px;color:#fff}

/* COLORS */
.bg-purple{background:#6366f1!important;color:#fff!important}
.bg-green{background:#10b981!important;color:white}
.bg-orange{background:#f59e0b!important;color:white}
.bg-blue{background:#3b82f6!important;color:white}

/* LOGOUT BUTTON */
.logout-btn{
    margin-top:auto;
    background:#dc2626;
    padding:10px 14px;
    border-radius:6px;
    text-align:center;
    color:white;
    text-decoration:none;
    font-size:14px;
}
.logout-btn:hover{background:#b91c1c}
</style>

<script>
function toggleStockMenu(){
    const menu=document.getElementById('stockSubMenu');
    const arrow=document.getElementById('stockArrow');
    menu.classList.toggle('open');
    arrow.innerHTML=menu.classList.contains('open')?"▴":"▾";
}

function confirmLogout(){
    return confirm("Are you sure you want to logout?");
}
</script>

</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">🛒 POS SYSTEM</div>

    <div class="menu">
        <a href="pos.php" class="<?= basename($_SERVER['PHP_SELF'])=='pos.php'?'active':'' ?>">📊 Dashboard</a>

        <div class="menu-item">
            <a href="javascript:void(0)" onclick="toggleStockMenu()">
                📦 Stock <span id="stockArrow">▾</span>
            </a>
            <div class="submenu" id="stockSubMenu">
                <a href="../admin/add_stock.php">➕ Add Stock</a>
                <a href="../admin/products.php">📋 Current Stock <span class="badge"><?=$inStock?></span></a>
                <a href="../admin/out_of_stock.php">❌ Out Of Stock <span class="badge danger"><?=$outStock?></span></a>
            </div>
        </div>

        <a href="billing.php" class="<?= basename($_SERVER['PHP_SELF'])=='billing.php'?'active':'' ?>">💰 Make a Sale</a>

        <!-- NEW Products link -->
        <a href="../admin/products.php">📦 Products</a>
    </div>

    <a class="logout-btn" href="logout.php" onclick="return confirmLogout()">🚪 Logout</a>
</div>

<!-- MAIN -->
<div class="main">
    <div class="header">Monthly Sales Overview</div>

    <a href="today_sales.php" class="summary-link">📄 View Today's Sales Summary</a>

    <div class="dashboard">
        <!-- SALES CARD -->
        <div class="card">
            <h3><?= date("F Y") ?> Sales</h3>
            <div style="font-size:22px;font-weight:700;color:#10b981;">
                $<?= number_format($monthSales,2) ?>
            </div>
        </div>

        <!-- STATS RIGHT -->
        <div class="stats">
            <div class="stat bg-purple">
                <h1><?= $outStock ?></h1>
                <p>Out Of Stock</p>
            </div>
            <div class="stat bg-green">
                <h1>$<?= number_format($monthSales,2) ?></h1>
                <p>This Month</p>
            </div>
            <div class="stat bg-orange">
                <h1>$<?= number_format($weekSales,2) ?></h1>
                <p>This Week</p>
            </div>
            <div class="stat bg-blue">
                <h1>$<?= number_format($todaySales,2) ?></h1>
                <p>Today</p>
            </div>
        </div>
    </div>
</div>

</body>
</html>
