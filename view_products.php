<?php
require_once 'db_connection.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$per_page = 10;

$categoryFilter = filter_input(INPUT_GET, 'categoryFilter', FILTER_VALIDATE_INT);

$offset = ($page - 1) * $per_page;

$sql = "SELECT * FROM products";
if ($categoryFilter !== false && $categoryFilter > 0) {
    $sql .= " WHERE category_id = ?";
}
$sql .= " LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if ($categoryFilter !== false && $categoryFilter > 0) {
    $stmt->bind_param("iii", $categoryFilter, $per_page, $offset);
} else {
    $stmt->bind_param("ii", $per_page, $offset);
}

$stmt->execute();

$result = $stmt->get_result();

$data = array();

if ($result->num_rows > 0) {
    while ($product = $result->fetch_assoc()) {
        $data[] = array(
            'product_id' => $product['product_id'],
            'name' => htmlspecialchars($product['name']),
            'description' => htmlspecialchars($product['description']),
            'price' => $product['price'],
            'image_path' => htmlspecialchars($product['image_path'])
        );
    }
} else {
    $data[] = "Нет доступных товаров.";
}

$stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($data);
