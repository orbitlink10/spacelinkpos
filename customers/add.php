<?php
session_start();
include "../config/db.php";
include "../auth/auth_check.php";

$message = "";
if(isset($_POST['submit'])){
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $address = $_POST['address'];

    mysqli_query($conn, "INSERT INTO customers (name, phone, email, address) 
                         VALUES ('$name','$phone','$email','$address')");
    $message = "Customer added successfully!";
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Customer</title></head>
<body>
<h2>Add Customer</h2>

<?php if($message) echo "<p style='color:green;'>$message</p>" ?>

<form method="POST">
<input name="name" placeholder="Customer Name" required><br><br>
<input name="phone" placeholder="Phone"><br><br>
<input name="email" placeholder="Email"><br><br>
<textarea name="address" placeholder="Address"></textarea><br><br>
<button type="submit" name="submit">Submit</button>
</form>

</body>
</html>
