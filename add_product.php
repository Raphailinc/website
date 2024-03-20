<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require_once 'db_connection.php';

    $name = htmlspecialchars($_POST["name"], ENT_QUOTES, 'UTF-8');
    $description = htmlspecialchars($_POST["description"], ENT_QUOTES, 'UTF-8');
    $price = filter_var($_POST["price"], FILTER_VALIDATE_FLOAT);
    $category_id = filter_var($_POST["category_id"], FILTER_VALIDATE_INT);

    if (empty($name) || empty($description) || empty($price) || empty($category_id)) {
        die("Пожалуйста, заполните все поля.");
    }

    if ($_FILES["fileToUpload"]["error"] !== UPLOAD_ERR_OK) {
        die("Ошибка при загрузке изображения.");
    }

    $uploadDir = 'uploads/';

    $imageFileName = uniqid() . '_' . $_FILES["fileToUpload"]["name"];

    $image_path = $uploadDir . $imageFileName;

    $allowedFormats = ['jpeg', 'jpg', 'png', 'gif'];
    $maxFileSize = 5 * 1024 * 1024; // 5MB

    $fileInfo = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    $imageFormat = strtolower(pathinfo($_FILES["fileToUpload"]["name"], PATHINFO_EXTENSION));

    if (!in_array($imageFormat, $allowedFormats) || $_FILES["fileToUpload"]["size"] > $maxFileSize || $fileInfo === false) {
        die("Недопустимый формат изображения или слишком большой размер файла.");
    }

    $conn->begin_transaction();

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $image_path)) {
        $sql = "INSERT INTO products (name, description, price, category_id, image_path, user_id) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdisi", $name, $description, $price, $category_id, $image_path, $_SESSION["user_id"]);

        if ($stmt->execute()) {
            $conn->commit();
            $stmt->close();
            $conn->close();
            header("Location: products.php");
        } else {
            $conn->rollback();
            echo "Ошибка при добавлении товара: " . $stmt->error;
        }
    } else {
        $conn->rollback();
        echo "Ошибка при сохранении изображения.";
    }
} else {
    echo "Неверный метод запроса.";
}

 $conn->close();
?>
