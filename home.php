<?php
session_start();
include "config/db.php";
include "auth/auth_check.php";

$username = $_SESSION['username'];
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SpaceLink POS - Home</title>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    * { margin:0; padding:0; box-sizing:border-box; font-family:'Roboto', sans-serif; }

    body {
        background: #f4f6f7;
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 20px;
    }

    header {
        width: 100%;
        text-align: center;
        margin-bottom: 40px;
    }

    header h2 {
        color: #2E7D32;
        font-size: 32px;
        margin-bottom: 5px;
    }

    .username {
        color: #555;
        font-size: 18px;
    }

    .menu {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        width: 100%;
        max-width: 900px;
    }

    .menu a {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 25px;
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        color: #2E7D32;
        text-decoration: none;
        font-size: 18px;
        font-weight: 500;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .menu a:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 25px rgba(0,0,0,0.15);
    }

    .menu a span {
        font-size: 36px;
        margin-bottom: 10px;
    }

    .logout-btn {
        margin-top: 40px;
        padding: 12px 25px;
        background: #E53935;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 16px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .logout-btn:hover { background: #c62828; }
</style>
</head>
<body>

<header>
    <h2>Welcome, <?= htmlspecialchars($username) ?></h2>
    <div class="username">(<?= htmlspecialchars($role) ?>)</div>
</header>

<div class="menu">
    <!-- POS is available for everyone -->
    <a href="sales/pos.php">
        <span>🛒</span>
        POS Kenya
    </a>

    <?php if($role==='admin'): ?>
    <a href="dashboard/index.php">
        <span>📊</span>
        Dashboard
    </a>
    <a href="admin/products.php">
        <span>📦</span>
        Products
    </a>
    <a href="reports/daily_sales.php">
        <span>📄</span>
        Daily Sales
    </a>
    <a href="reports/best_sellers.php">
        <span>🏆</span>
        Best Sellers
    </a>
    <?php endif; ?>
</div>

<form method="POST" action="logout.php">
    <button class="logout-btn" type="submit">Logout</button>
</form>

</body>
</html>
