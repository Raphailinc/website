<?php
require_once 'db_connection.php';

$sql = "SELECT categories.name as category, COUNT(*) as count 
        FROM products 
        INNER JOIN categories ON products.category_id = categories.category_id 
        GROUP BY products.category_id";
$result = $conn->query($sql);

$chartData = array();
while ($row = $result->fetch_assoc()) {
    $chartData[$row['category']] = $row['count'];
}

header('Content-Type: application/json');
echo json_encode($chartData);
?>
