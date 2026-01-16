<?php
session_start();
include "../config/db.php";
include "../auth/auth_check.php";

// ADMIN ONLY CHECK
if($_SESSION['role'] !== 'admin'){
    header("Location: ../home.php");
    exit;
}

// GET OUT OF STOCK PRODUCTS
$result = mysqli_query($conn, "
    SELECT * FROM products 
    WHERE stock <= 0
    ORDER BY name ASC
");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Out Of Stock Products</title>
    <style>
        body{font-family:sans-serif;background:#f6f7fb;padding:20px;}
        table{
            width:100%;border-collapse:collapse;background:white;
            border-radius:10px;overflow:hidden;box-shadow:0 10px 25px rgba(0,0,0,0.07);
        }
        th,td{
            padding:12px;
            border-bottom:1px solid #eee;
        }
        th{
            background:#7c3aed;color:white;text-align:left;
        }
        tr:hover{background:#f3e8ff;}
        .empty{text-align:center;padding:20px;color:#999;}
        a.back{
            display:inline-block;margin-top:15px;background:#15162b;
            color:white;padding:10px 18px;border-radius:8px;
            text-decoration:none;
        }
        a.back:hover{background:#0f1020;}
    </style>
</head>
<body>

<h2>❌ Out Of Stock Products</h2>

<table>
    <tr>
        <th>Name</th>
        <th>Barcode</th>
        <th>Supplier</th>
        <th>Selling Price</th>
        <th>Date Recorded</th>
    </tr>

    <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)){ ?>
            <tr>
                <td><?= $row['name'] ?></td>
                <td><?= $row['barcode'] ?></td>
                <td><?= $row['supplier'] ?></td>
                <td>$<?= number_format($row['price'],2) ?></td>
                <td><?= $row['date_recorded'] ?></td>
            </tr>
        <?php } ?>
    <?php else: ?>
        <tr><td colspan="5" class="empty">🎉 All products are in stock!</td></tr>
    <?php endif; ?>
</table>

<a href="../sales/pos.php" class="back">← Back to POS</a>

</body>
</html>
