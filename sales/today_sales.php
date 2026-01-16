<?php
session_start();
include "../config/db.php";
include "../auth/auth_check.php";

// Fetch today's sales with product & pricing
$q = mysqli_query($conn, "
    SELECT s.id AS sale_id, s.customer_name, s.customer_phone, s.created_at,
           si.product_id, si.quantity, si.price AS sell_price, si.cost_price AS buy_price,
           p.name AS product_name
    FROM sales s
    JOIN sale_items si ON s.id = si.sale_id
    JOIN products p ON si.product_id = p.id
    WHERE DATE(s.created_at) = CURDATE()
    ORDER BY s.created_at DESC
");

$sales = mysqli_fetch_all($q, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<title>Today's Sales Summary</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}
body{display:flex;height:100vh;background:#eef1f7;overflow:hidden}

/* SIDEBAR — same theme */
.sidebar{
    width:250px;background:#1f2937;color:white;padding:25px 15px;display:flex;flex-direction:column;
}
.logo{font-size:20px;font-weight:700;color:white;text-align:center;margin-bottom:30px;}
.menu a{
    padding:12px 14px;margin-bottom:10px;border-radius:6px;
    display:flex;align-items:center;gap:10px;text-decoration:none;
    color:#d1d5db;font-size:14px;transition:.3s;
}
.menu a:hover{background:#6366f1;color:white;}
.menu .active{background:#6366f1;color:white;}

/* MAIN PANEL */
.main{flex:1;padding:25px;overflow-y:auto;}
.header{font-size:20px;font-weight:600;margin-bottom:20px;}

/* CARDS & TABLE */
.card{
    background:white;padding:20px;border-radius:10px;
    box-shadow:0 6px 14px rgba(0,0,0,0.06);
    margin-bottom:20px;
}
.table{
    width:100%;border-collapse:collapse;border-radius:8px;overflow:hidden;
}
.table th{
    background:#1f2937;color:white;padding:10px;font-size:14px;
}
.table td{
    border-bottom:1px solid #e5e7eb;
    padding:10px;font-size:13px;text-align:center;
}
.summary-box{
    background:#1e3a8a;color:white;padding:20px;border-radius:10px;
    box-shadow:0 6px 14px rgba(0,0,0,0.1);
}
.back-btn{
    background:#374151;color:white;padding:8px 14px;border-radius:6px;text-decoration:none;font-size:14px;
}
.back-btn:hover{background:#111827;}
.revenue{color:#10b981;font-weight:600;}
.profit{color:#2563eb;font-weight:600;}
.no-sales{
    text-align:center;margin-top:40px;font-size:17px;color:#6b7280;
}
</style>
</head>
<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div class="logo">🛒 POS SYSTEM</div>
    <div class="menu">
        <a href="pos.php">📊 Dashboard</a>
        <a href="billing.php">💰 Make a Sale</a>
        <a href="#" class="active">📅 Today's Summary</a>
        <a href="#">👥 Customers</a>
        <a href="#">⚙ Settings</a>
    </div>
</div>

<!-- MAIN -->
<div class="main">

    <div class="header">
        📅 Today's Sales (<?= date("Y-m-d") ?>)
        <a href="pos.php" class="back-btn" style="float:right;">⬅ Back</a>
    </div>

    <?php if (empty($sales)) { ?>
        <div class="card">
            <div class="no-sales">No sales made today.</div>
        </div>
    </div>
</body>
</html>
<?php exit; } ?>

    <!-- TABLE CARD -->
    <div class="card">
        <table class="table">
            <tr>
                <th>Time</th><th>Sale #</th><th>Customer</th>
                <th>Product</th><th>Qty</th><th>Selling</th>
                <th>Cost</th><th>Revenue</th><th>Profit</th>
            </tr>

            <?php 
            $grandRevenue = 0;
            $grandProfit  = 0;
            foreach($sales as $row):
                $subtotal = $row['sell_price'] * $row['quantity'];
                $profit   = ($row['sell_price'] - $row['buy_price']) * $row['quantity'];
                $grandRevenue += $subtotal;
                $grandProfit  += $profit;
            ?>
            <tr>
                <td><?= $row['created_at'] ?></td>
                <td>#<?= $row['sale_id'] ?></td>
                <td><?= $row['customer_name'] ?> (<?= $row['customer_phone'] ?>)</td>
                <td><?= $row['product_name'] ?></td>
                <td><?= $row['quantity'] ?></td>
                <td>$<?= number_format($row['sell_price'],2) ?></td>
                <td>$<?= number_format($row['buy_price'],2) ?></td>
                <td class="revenue">$<?= number_format($subtotal,2) ?></td>
                <td class="profit">$<?= number_format($profit,2) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <!-- SUMMARY CARD -->
    <div class="summary-box">
        <h3>📊 Daily Totals</h3>
        <p>Total Revenue: <strong>$<?= number_format($grandRevenue,2) ?></strong></p>
        <p>Total Profit: <strong>$<?= number_format($grandProfit,2) ?></strong></p>
    </div>

</div>
</body>
</html>
