<?php
include "../config/db.php";

$term = isset($_GET['term']) ? $_GET['term'] : '';

$sql = mysqli_query($conn,
    "SELECT id, name, price FROM products 
     WHERE name LIKE '%$term%'
     ORDER BY name ASC 
     LIMIT 10"
);

$data = [];
while($row = mysqli_fetch_assoc($sql)){
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
