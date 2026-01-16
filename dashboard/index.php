<?php
session_start();
include "../config/db.php";
include "../auth/auth_check.php";

if($_SESSION['role'] !== 'admin'){
    echo "<h2>Access Denied</h2>";
    echo '<a href="../home.php">Back to Home</a>';
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - SpaceLink POS</title>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
<style>
    body { font-family:'Roboto', sans-serif; background:#f4f6f7; padding:20px; }
    h2 { color:#2E7D32; margin-bottom:20px; }
    .charts { display:grid; grid-template-columns: repeat(auto-fit,minmax(300px,1fr)); gap:20px; margin-bottom:40px; }
    .chart-card {
        background:#fff; padding:20px; border-radius:12px; box-shadow:0 8px 20px rgba(0,0,0,0.1);
    }
    .chart-card h3 { margin-bottom:15px; color:#2E7D32; }
    table { border-collapse: collapse; width: 100%; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 8px 20px rgba(0,0,0,0.1); }
    th, td { padding:12px; border-bottom:1px solid #eee; text-align:center; }
    th { background:#4CAF50; color:#fff; }
    a.back { display:inline-block; margin-top:20px; padding:10px 20px; background:#2E7D32; color:#fff; border-radius:6px; text-decoration:none; }
    a.back:hover { background:#388E3C; }
</style>
</head>
<body>

<h2>📊 Admin Dashboard</h2>

<div class="charts">
    <div class="chart-card">
        <h3>Sales Trend (Last 7 Days)</h3>
        <canvas id="salesTrendChart"></canvas>
    </div>
    <div class="chart-card">
        <h3>Monthly Sales</h3>
        <label>Select Year: </label>
        <select id="yearSelect">
            <?php
            $currentYear = date("Y");
            for($y=$currentYear; $y>=$currentYear-5; $y--){
                echo "<option value='$y'>$y</option>";
            }
            ?>
        </select>
        <canvas id="monthlySalesChart"></canvas>
    </div>
</div>

<h3>🏆 Best Selling Products</h3>
<table>
<tr><th>Product</th><th>Total Sold</th></tr>
<?php
$best_sellers = mysqli_query($conn, "
    SELECT p.name, SUM(si.quantity) AS total_sold
    FROM sale_items si
    JOIN products p ON si.product_id = p.id
    GROUP BY si.product_id
    ORDER BY total_sold DESC
    LIMIT 5
");
while($row = mysqli_fetch_assoc($best_sellers)){
    echo "<tr><td>".htmlspecialchars($row['name'])."</td><td>".$row['total_sold']."</td></tr>";
}
?>
</table>

<a class="back" href="../home.php">Back to Home</a>

<script>
fetch('charts_data.php?type=last7days')
.then(res=>res.json())
.then(data=>{
    new Chart(document.getElementById('salesTrendChart'),{
        type:'line',
        data:{
            labels:data.labels,
            datasets:[{
                label:'Sales ($)',
                data:data.values,
                borderColor:'rgba(76,175,80,1)',
                backgroundColor:'rgba(76,175,80,0.2)',
                fill:true,
                tension:0.4
            }]
        },
        options:{responsive:true, scales:{y:{beginAtZero:true}}}
    });
});

let monthlyChart;
function loadMonthlyChart(year){
    fetch('charts_data.php?type=monthly&year='+year)
    .then(res=>res.json())
    .then(data=>{
        if(monthlyChart) monthlyChart.destroy();
        monthlyChart = new Chart(document.getElementById('monthlySalesChart'),{
            type:'bar',
            data:{
                labels:data.labels,
                datasets:[{label:'Sales ($)', data:data.values, backgroundColor:'rgba(255,159,64,0.7)'}]
            },
            options:{responsive:true, scales:{y:{beginAtZero:true}}}
        });
    });
}
loadMonthlyChart(new Date().getFullYear());
document.getElementById('yearSelect').addEventListener('change',function(){
    loadMonthlyChart(this.value);
});
</script>

</body>
</html>
