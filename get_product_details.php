<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["error" => "Вы не авторизованы"]);
    exit();
}

require_once 'db_connection.php';

if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    $sql = "SELECT * FROM products WHERE product_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $product_id, $_SESSION["user_id"]);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            echo json_encode($product);
        } else {
            echo json_encode(array("error" => "Товар не найден."));
        }
    } else {
        echo json_encode(array("error" => "Ошибка выполнения запроса: " . $stmt->error));
    }

    $stmt->close();
}

$conn->close();
?>