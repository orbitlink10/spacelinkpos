<?php
include "../config/db.php";

if (!isset($_GET['sale_id'])) {
    die("Invalid request.");
}

$sale_id = intval($_GET['sale_id']);

/* Fetch sale */
$saleQ = mysqli_query($conn, "SELECT * FROM sales WHERE id=$sale_id LIMIT 1");
if (mysqli_num_rows($saleQ) == 0) {
    die("Sale not found!");
}
$sale = mysqli_fetch_assoc($saleQ);

/* Fetch sale items */
$itemsQ = mysqli_query($conn,
    "SELECT si.*, p.name 
     FROM sale_items si 
     JOIN products p ON si.product_id = p.id
     WHERE si.sale_id = $sale_id"
);

?>
<!DOCTYPE html>
<html>
<head>
<title>Receipt #<?= $sale_id ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<style>
body{font-family:monospace;font-size:14px;padding:10px;}
h3{text-align:center;margin-bottom:5px;}
.center{text-align:center;}
.right{text-align:right;}
.line{border-top:1px dashed #000;margin:8px 0;}
.button{
    padding:10px;
    width:100%;
    margin-top:10px;
    border:none;
    background:black;
    color:white;
    cursor:pointer;
    font-size:14px;
}
a.button{
    text-decoration:none;
    display:block;
    text-align:center;
    padding:10px;
    background:#444;
    color:white;
    width:100%;
    margin-top:10px;
    border:none;
}
</style>
</head>
<body>

<h3><u>STORE RECEIPT</u></h3>

<div class="center">
Sale #: <?= $sale_id ?><br>
Date: <?= $sale['created_at'] ?><br><br>

<b>Customer:</b><br>
<?= $sale['customer_name'] ?><br>
<?= $sale['customer_phone'] ?>
</div>

<div class="line"></div>

<?php while($item = mysqli_fetch_assoc($itemsQ)) { ?>
<?= $item['name'] ?> (x <?= $item['quantity'] ?>) @ <?= number_format($item['price'],2) ?><br>
<div class="right"><?= number_format($item['total'],2) ?></div>
<?php } ?>

<div class="line"></div>

<b>Total:</b> <?= number_format($sale['total_amount'],2) ?><br>
<b>Received:</b> <?= number_format($sale['amount_received'],2) ?><br>
<b>Change:</b> <?= number_format($sale['change_due'],2) ?><br>

<div class="line"></div>

<div class="center">Thank you for shopping!</div>

<!-- PRINT BUTTON -->
<button class="button" onclick="window.print()">Print Receipt</button>

<!-- RETURN TO DASHBOARD BUTTON -->
<a class="button" href="pos.php">⬅ Return to Dashboard</a>

<!-- VIEW HISTORY BUTTON -->
<?php if(!empty($sale['customer_phone'])): ?>
<a class="button" href="../customers/history.php?phone=<?= $sale['customer_phone'] ?>">
    View Purchase History
</a>
<?php endif; ?>

</body>
</html>
