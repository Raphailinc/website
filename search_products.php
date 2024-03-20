<?php
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $search = isset($_GET["search"]) ? $_GET["search"] : '';
    $category = isset($_GET["category"]) ? $_GET["category"] : '';

    if (empty($search)) {
        http_response_code(400);
        echo json_encode(["error" => "Введите поисковый запрос"]);
        exit;
    }

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    require_once 'db_connection.php';

    $sql = "SELECT * FROM products WHERE name LIKE ?";

    if (!empty($category)) {
        $sql .= " AND category_id = ?";
    }

    $stmt = $conn->prepare($sql);
    $searchParam = "%" . $search . "%";

    if (!empty($category)) {
        $stmt->bind_param("si", $searchParam, $category);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        $products = [];

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }

        echo json_encode($products);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Ошибка при выполнении запроса: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
}
}
