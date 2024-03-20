<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST["product_id"];
    $name = $_POST["name"];
    $description = $_POST["description"];
    $price = $_POST["price"];

    require_once 'db_connection.php';

    $sql = "UPDATE products SET name = ?, description = ?, price = ? WHERE product_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $name, $description, $price, $product_id);

    if (empty($name) || empty($description) || !is_numeric($price)) {
        echo "Пожалуйста, заполните все поля корректно.";
    } else {
        if ($_FILES["image"]["error"] === UPLOAD_ERR_OK) {
            $image_path = "uploads/" . basename($_FILES["image"]["name"]);
            move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
        } else {
            $sql = "SELECT image_path FROM products WHERE product_id = ?";
            $stmt_image = $conn->prepare($sql);
            $stmt_image->bind_param("i", $product_id);
            $stmt_image->execute();
            $stmt_image->bind_result($image_path);
            $stmt_image->fetch();
            $stmt_image->close();
        }

        if ($stmt->execute()) {
            echo "Товар успешно обновлен.";
        } else {
            echo "Ошибка: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>
