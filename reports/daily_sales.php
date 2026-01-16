<?php
session_start();
include "../config/db.php";
include "../auth/auth_check.php";

if($_SESSION['role'] !== 'admin'){
    echo "Access Denied";
    exit;
}

$result = mysqli_query($conn,"SELECT s.id, s.total_amount, s.created_at, GROUP_CONCAT(p.name,' x',si.quantity) as items
FROM sales s
JOIN sale_items si ON s.id = si.sale_id
JOIN products p ON si.product_id = p.id
GROUP BY s.id
ORDER BY s.created_at DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Daily Sales</title></head>
<body>
<h2>Daily Sales Report</h2>
<table border="1" cellpadding="5">
<tr><th>Sale ID</th><th>Items</th><th>Total</th><th>Date</th></tr>
<?php while($row=mysqli_fetch_assoc($result)){ ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['items']) ?></td>
<td>$<?= number_format($row['total_amount'],2) ?></td>
<td><?= $row['created_at'] ?></td>
</tr>
<?php } ?>
</table>
<a href="../home.php">Back to Home</a>
</body>
</html>
