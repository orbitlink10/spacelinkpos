<?php
session_start();
include "config/db.php";

// Handle form submission
$message = '';
if (isset($_POST['register'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);

    // Validation
    if (empty($name) || empty($phone)) {
        $message = "Name and Phone are required!";
    } else {
        mysqli_query($conn, "
            INSERT INTO customers (name, phone, email, address, created_at)
            VALUES ('$name', '$phone', '$email', '$address', NOW())
        ");
        $message = "Customer registered successfully!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Customer Registration</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}

body{
    background:#f7f8fa;
    padding:30px;
    display:flex;
    justify-content:center;
    align-items:flex-start;
}

.card{
    background:white;
    width:450px;
    padding:25px;
    border-radius:12px;
    box-shadow:0 6px 20px rgba(0,0,0,0.08);
}

.card h2{
    text-align:center;
    margin-bottom:20px;
    font-weight:600;
}

label{
    display:block;
    margin:8px 0 5px;
    font-weight:500;
}

input,textarea{
    width:100%;
    padding:10px;
    border:1px solid #cfcfcf;
    border-radius:6px;
    font-size:14px;
}

textarea{
    resize:none;
    height:80px;
}

button{
    width:100%;
    padding:12px;
    background:#4f46e5;
    color:white;
    border:none;
    margin-top:15px;
    border-radius:6px;
    font-size:15px;
    cursor:pointer;
}

button:hover{
    background:#4338ca;
}

.message{
    background:#e6ffed;
    color:#065f46;
    padding:10px;
    text-align:center;
    border-radius:6px;
    margin-bottom:10px;
    border-left:4px solid #10b981;
}

.back-btn{
    display:block;
    margin-top:15px;
    text-align:center;
    background:#374151;
    padding:10px;
    border-radius:6px;
    color:white;
    text-decoration:none;
}

.back-btn:hover{
    background:#111827;
}
</style>
</head>
<body>

<div class="card">
<h2>Customer Registration</h2>

<?php if($message): ?>
    <div class="message"><?= $message ?></div>
<?php endif; ?>

<form method="POST">
    <label>Full Name *</label>
    <input type="text" name="name" required>

    <label>Phone Number *</label>
    <input type="text" name="phone" required>

    <label>Email</label>
    <input type="email" name="email">

    <label>Address</label>
    <textarea name="address"></textarea>

    <button type="submit" name="register">Register Customer</button>
</form>

<a href="index.php" class="back-btn">← Back to Home</a>
</div>

</body>
</html>
