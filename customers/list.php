<?php
session_start();
include "../config/db.php";
include "../auth/auth_check.php";

$result = mysqli_query($conn, "SELECT * FROM customers ORDER BY name ASC");
?>
<!DOCTYPE html>
<html>
<head><title>Customers</title></head>
<body>
<h2>Customer List</h2>
<a href="add.php">Add New Customer</a>
<table border="1" cellpadding="8" cellspacing="0">
<tr><th>Name</th><th>Phone</th><th>Email</th><th>Created</th></tr>
<?php while($c = mysqli_fetch_assoc($result)){ ?>
<tr>
    <td><?= $c['name'] ?></td>
    <td><?= $c['phone'] ?></td>
    <td><?= $c['email'] ?></td>
    <td><?= $c['created_at'] ?></td>
</tr>
<?php } ?>
</table>
</body>
</html>
