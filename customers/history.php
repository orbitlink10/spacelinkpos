<?php
include "../config/db.php";

$phone = $_GET['phone'];
$sales = mysqli_query($conn, "SELECT * FROM sales WHERE customer_phone='$phone' ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
<title>Purchase History</title>
<style>
table{width:100%;border-collapse:collapse}
td,th{border:1px solid #ccc;padding:8px}
h2{margin-bottom:10px}
</style>
</head>
<body>

<h2>Purchase History (<?= $phone ?>)</h2>

<table>
<tr><th>Date</th><th>Total</th><th>Received</th><th>Change</th><th>View</th></tr>
<?php while($s=mysqli_fetch_assoc($sales)){ ?>
<tr>
    <td><?= $s['created_at'] ?></td>
    <td><?= $s['total_amount'] ?></td>
    <td><?= $s['amount_received'] ?></td>
    <td><?= $s['change_due'] ?></td>
    <td><a href="../sales/receipt.php?sale_id=<?= $s['id'] ?>">Receipt</a></td>
</tr>
<?php } ?>
</table>

</body>
</html>
