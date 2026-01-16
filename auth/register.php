<?php
session_start();
require_once "../config/db.php";

$error = "";
$success = "";

if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm  = trim($_POST['confirm']);

    if ($username === "" || $password === "" || $confirm === "") {
        $error = "All fields are required.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check if username exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username=? LIMIT 1");
        if (!$stmt) {
            $error = "Something went wrong. Please try again later.";
        } else {
            $stmt->bind_param("s", $username);
            if (!$stmt->execute()) {
                $error = "Something went wrong. Please try again later.";
            } else {
                $stmt->store_result();
                if ($stmt->num_rows > 0) {
                    $error = "Username already taken.";
                } else {
                    $hash = password_hash($password, PASSWORD_DEFAULT);
                    $role = "cashier"; // default role

                    $insert = $conn->prepare("INSERT INTO users(username, password, role) VALUES(?,?,?)");
                    if (!$insert) {
                        $error = "Something went wrong. Please try again later.";
                    } else {
                        $insert->bind_param("sss", $username, $hash, $role);
                        if ($insert->execute()) {
                            $success = "Registration successful! You can now login.";
                            header("refresh:2;url=login.php");
                        } else {
                            $error = "Something went wrong!";
                        }
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Register — SpaceLink POS</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;background:#f3f4f6;height:100vh;display:flex;justify-content:center;align-items:center;}
.container{background:white;width:350px;padding:25px;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.08);}
h2{text-align:center;margin-bottom:15px;}
input{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px;margin:8px 0;}
button{width:100%;border:none;padding:10px;margin-top:10px;border-radius:6px;background:#4f46e5;color:white;font-weight:500;cursor:pointer;}
button:hover{background:#4338ca;}
.error{text-align:center;color:#dc2626;margin-bottom:8px;font-size:14px;}
.success{text-align:center;color:#10b981;margin-bottom:8px;font-size:14px;}
a{text-decoration:none;color:#4f46e5;font-size:14px;display:block;text-align:center;margin-top:10px;}
</style>
</head>
<body>

<div class="container">
    <h2>Create Account</h2>

    <?php if($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Choose username" autocomplete="off">
        <input type="password" name="password" placeholder="Choose password">
        <input type="password" name="confirm" placeholder="Confirm password">
        <button name="register">Register</button>
    </form>

    <a href="login.php">Already have an account? Login</a>
    <a href="../index.php">← Back to Home</a>
</div>

</body>
</html>
