<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SpaceLink POS System</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- Google Font -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<style>
*{margin:0;padding:0;box-sizing:border-box;font-family:'Poppins',sans-serif;}

body{
    background:#f7f8fa;
    color:#111827;
    display:flex;
    flex-direction:column;
    min-height:100vh;
}

header{
    background:white;
    padding:20px 40px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 2px 10px rgba(0,0,0,0.05);
}
header .logo{
    font-size:22px;
    font-weight:700;
    color:#4f46e5;
}
header .btn-group a{
    text-decoration:none;
    padding:10px 18px;
    border-radius:6px;
    font-size:14px;
    margin-left:10px;
}
.btn-login{
    background:#4f46e5;
    color:white;
}
.btn-login:hover{background:#4338ca;}
.btn-register{
    background:white;
    border:1px solid #4f46e5;
    color:#4f46e5;
}
.btn-register:hover{
    background:#eef2ff;
}

/* HERO SECTION */
.hero{
    text-align:center;
    padding:80px 20px;
}
.hero h1{
    font-size:36px;
    font-weight:700;
    margin-bottom:15px;
}
.hero p{
    font-size:16px;
    max-width:650px;
    margin:0 auto;
    color:#6b7280;
}

/* PRICING SECTION */
.pricing{
    padding:60px 20px;
}
.pricing-title{
    text-align:center;
    font-size:26px;
    font-weight:600;
    margin-bottom:40px;
}
.pricing-cards{
    display:flex;
    justify-content:center;
    gap:25px;
    flex-wrap:wrap;
}
.card{
    background:white;
    width:300px;
    border-radius:12px;
    padding:25px;
    box-shadow:0 5px 30px rgba(0,0,0,0.06);
    transition:.3s;
}
.card:hover{
    transform:translateY(-6px);
}
.card h3{
    font-size:22px;
    font-weight:600;
    margin-bottom:10px;
}
.price{
    font-size:30px;
    font-weight:700;
    margin:10px 0;
    color:#4f46e5;
}
.card ul{
    list-style:none;
    margin:20px 0;
}
.card ul li{
    margin:8px 0;
    color:#374151;
    font-size:14px;
}
.card button{
    width:100%;
    padding:10px 0;
    border:none;
    border-radius:6px;
    background:#4f46e5;
    color:white;
    font-size:14px;
    cursor:pointer;
}
.card button:hover{
    background:#4338ca;
}

/* FOOTER */
footer{
    margin-top:auto;
    background:#111827;
    color:white;
    text-align:center;
    padding:16px 10px;
    font-size:13px;
}
</style>
</head>
<body>

<header>
    <div class="logo">🚀 SpaceLink POS</div>
    <div class="btn-group">
        <a href="auth/login.php" class="btn-login">Login</a>
        <a href="auth/register.php" class="btn-register">Register</a>
    </div>
</header>

<section class="hero">
    <h1>Manage Your Business Effortlessly</h1>
    <p>
        SpaceLink POS helps you monitor sales, manage inventory, and grow your business professionally.
        Accessible anywhere, easy to use, and optimized for performance.
    </p>
</section>

<section class="pricing">
    <div class="pricing-title">Choose Your Subscription</div>
    <div class="pricing-cards">

        <div class="card">
            <h3>Starter</h3>
            <div class="price">$9.99/mo</div>
            <ul>
                <li>✔ 1 User</li>
                <li>✔ Basic Inventory</li>
                <li>✔ Monthly Reports</li>
                <li>✔ Email Support</li>
            </ul>
            <button>Subscribe</button>
        </div>

        <div class="card">
            <h3>Business</h3>
            <div class="price">$29.99/mo</div>
            <ul>
                <li>✔ 5 Users</li>
                <li>✔ Advanced Inventory</li>
                <li>✔ Weekly Reports</li>
                <li>✔ Priority Support</li>
            </ul>
            <button>Subscribe</button>
        </div>

        <div class="card">
            <h3>Enterprise</h3>
            <div class="price">$59.99/mo</div>
            <ul>
                <li>✔ Unlimited Users</li>
                <li>✔ Full Inventory Control</li>
                <li>✔ Sales Analytics</li>
                <li>✔ Dedicated Support</li>
            </ul>
            <button>Subscribe</button>
        </div>

    </div>
</section>

<footer>
    © <?= date("Y") ?> SpaceLink POS — All Rights Reserved
</footer>

</body>
</html>
