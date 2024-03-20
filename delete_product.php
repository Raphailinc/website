<?php
require_once 'db_connection.php';

session_start();
if (!isset($_SESSION["user_id"])) {
    echo "Ошибка: Пользователь не авторизован.";
    exit();
}

if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
    $product_id = $_GET['product_id'];

    $sql = "SELECT * FROM products WHERE product_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $product_id, $_SESSION["user_id"]);

    if ($stmt->execute()) {
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $deleteSql = "DELETE FROM products WHERE product_id = ?";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bind_param("i", $product_id);

            if ($deleteStmt->execute()) {
                echo "Товар успешно удален.";
            } else {
                echo "Ошибка удаления товара: " . $deleteStmt->error;
            }

            $deleteStmt->close();
        } else {
            echo "Товар не найден или у вас нет прав на удаление.";
        }
    } else {
        echo "Ошибка выполнения запроса: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Некорректный идентификатор товара.";
}

$conn->close();
?>
