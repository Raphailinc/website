<?php
require_once 'db_connection.php';

$query = "SELECT category_id, name FROM categories";
$result = $conn->query($query);

$categories = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $categories[] = [
            'category_id' => $row['category_id'],
            'name' => $row['name']
        ];
    }
}

$conn->close();

echo json_encode($categories);
?>
