<?php
session_start();
require_once "../config/db.php";

$error = "";

// Already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ../sales/pos.php");
    exit;
}

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === "" || $password === "") {
        $error = "Please enter username and password.";
    } else {
        $stmt = $conn->prepare("SELECT id, username, password, role FROM users WHERE username=? LIMIT 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['last_activity'] = time();

                header("Location: ../sales/pos.php");
                exit;
            }
        }

        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login — SpaceLink POS</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;background:#f3f4f6;height:100vh;display:flex;justify-content:center;align-items:center;}
.container{background:white;width:350px;padding:25px;border-radius:10px;box-shadow:0 4px 20px rgba(0,0,0,0.08);}
h2{text-align:center;margin-bottom:15px;}
input{width:100%;padding:10px;border:1px solid #d1d5db;border-radius:6px;margin:8px 0;}
button{width:100%;border:none;padding:10px;margin-top:10px;border-radius:6px;background:#4f46e5;color:white;font-weight:500;cursor:pointer;}
button:hover{background:#4338ca;}
.error{text-align:center;color:#dc2626;margin-bottom:8px;font-size:14px;}
a{text-decoration:none;color:#4f46e5;font-size:14px;display:block;text-align:center;margin-top:10px;}
</style>
</head>
<body>

<div class="container">
    <h2>Login</h2>

    <?php if($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Username" autocomplete="off" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="login">Login</button>
    </form>

    <a href="register.php">Don't have an account? Register</a>
    <a href="../index.php">← Back to Home</a>
</div>

</body>
</html>
