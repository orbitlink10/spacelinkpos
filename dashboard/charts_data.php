<?php
include "../config/db.php";

$type = $_GET['type'] ?? '';

header('Content-Type: application/json');

// ----------------------
// SALES TREND LAST 7 DAYS
// ----------------------
if($type==='last7days'){
    $labels = [];
    $values = [];

    for($i=6; $i>=0; $i--){
        $date = date('Y-m-d', strtotime("-$i days"));
        $labels[] = date('M d', strtotime($date));
        $res = mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM sales WHERE DATE(created_at)='$date'");
        $row = mysqli_fetch_assoc($res);
        $values[] = floatval($row['total'] ?? 0);
    }

    echo json_encode(['labels'=>$labels,'values'=>$values]);
}

// ----------------------
// MONTHLY SALES
// ----------------------
if($type==='monthly'){
    $year = intval($_GET['year'] ?? date('Y'));
    $labels = [];
    $values = array_fill(1,12,0);

    $res = mysqli_query($conn, "
        SELECT MONTH(created_at) AS month, SUM(total_amount) AS total
        FROM sales
        WHERE YEAR(created_at)=$year
        GROUP BY MONTH(created_at)
    ");

    while($row = mysqli_fetch_assoc($res)){
        $values[(int)$row['month']] = floatval($row['total']);
    }

    for($i=1;$i<=12;$i++){
        $labels[] = date('F', mktime(0,0,0,$i,1));
    }
    $values = array_values($values);

    echo json_encode(['labels'=>$labels,'values'=>$values]);
}
