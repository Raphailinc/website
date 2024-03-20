<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uploadDirectory = 'uploads/';

    require_once 'db_connection.php';

    if (isset($_POST['product_id'])) {
        $product_id = $_POST['product_id'];

        if ($_FILES['fileToUpload']['error'] === UPLOAD_ERR_OK) {
            $originalFileName = $_FILES['fileToUpload']['name'];
            $uploadedFile = $uploadDirectory . basename($originalFileName);

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $fileExtension = pathinfo($originalFileName, PATHINFO_EXTENSION);

            if (in_array($fileExtension, $allowedExtensions)) {
                $uniqueFileName = uniqid() . '.' . $fileExtension;
                $imagePathInDatabase = $uploadDirectory . $uniqueFileName;

                $stmt_check_product = $conn->prepare("SELECT product_id FROM products WHERE product_id = ?");
                $stmt_check_product->bind_param("i", $product_id);
                $stmt_check_product->execute();
                $stmt_check_product->store_result();

                if ($stmt_check_product->num_rows > 0) {
                    $stmt_check_product->close();

                    if (file_exists($imagePathInDatabase)) {
                        echo "Файл с таким именем уже существует. Пожалуйста, выберите другой файл.";
                    } else {
                        if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imagePathInDatabase)) {
                            $stmt = $conn->prepare("UPDATE products SET image_path = ? WHERE product_id = ?");
                            $stmt->bind_param("si", $imagePathInDatabase, $product_id);

                            if ($stmt->execute()) {
                                echo "Изображение успешно связано с товаром.";
                            } else {
                                echo "Ошибка при обновлении записи в базе данных: " . $stmt->error;
                            }

                            $stmt->close();
                        } else {
                            echo "Ошибка при перемещении файла.";
                        }
                    }
                } else {
                    echo "Товар с указанным ID не существует.";
                }
            } else {
                echo "Загрузка файла с недопустимым типом.";
            }
        } else {
            echo "Ошибка при загрузке файла.";
        }
    } else {
        echo "product_id не передан. Данные для создания нового товара.";
    }

    $conn->close();
}
?>
