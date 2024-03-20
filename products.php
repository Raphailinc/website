<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Мои товары</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
            max-width: 800px;
            margin: auto;
        }

        .product-card {
            margin-bottom: 40px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            overflow: hidden;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .product-card img {
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            max-width: 100%;
            height: auto;
        }

        .product-card-body {
            padding: 20px;
        }

        .product-title {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        .product-buttons {
            text-align: center;
        }

        .product-buttons button {
            margin: 5px;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .product-buttons button:hover {
            background-color: #4CAF50;
            color: #fff;
        }

        .error-message {
            color: red;
            text-align: center;
        }

        @media (max-width: 576px) {
            .product-card {
                margin-bottom: 20px;
            }

            .product-card-body {
                padding: 15px;
            }

            .product-title {
                font-size: 1.2rem;
            }
        }

        .loader {
            border: 4px solid #f3f3f3;
            border-radius: 50%;
            border-top: 4px solid #3498db;
            width: 40px;
            height: 40px;
            animation: spin 2s linear infinite;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 999;
            display: none;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="loader"></div>
    <?php
    session_start();
    require_once 'db_connection.php';

    if (isset($_SESSION["user_id"])) {
        if (isset($_GET['product_id']) && is_numeric($_GET['product_id'])) {
            $product_id = $_GET['product_id'];

            if (!preg_match('/^[0-9]+$/', $product_id)) {
                echo "<p class='error-message'>Некорректный идентификатор товара.</p>";
            } else {
                $sql = "SELECT * FROM products WHERE product_id = ? AND user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $product_id, $_SESSION["user_id"]);

                if ($stmt->execute()) {
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        $product = $result->fetch_assoc();
                        echo "<div class='product-card'>";
                        echo "<img src='{$product['image_path']}' alt='Product Image'>";
                        echo "<div class='product-card-body'>";
                        echo "<h1 class='product-title'>" . htmlspecialchars($product['name']) . "</h1>";
                        echo "<div class='product-buttons'>";
                        echo "<button class='btn btn-primary edit-btn' onclick=\"window.location='edit_product.html?product_id={$product['product_id']}'\">Редактировать товар</button>";
                        echo "<button class='btn btn-success review-btn' onclick=\"window.location='add_review.html?product_id={$product['product_id']}'\">Добавить отзыв</button>";
                        echo "<button class='btn btn-info view-btn' onclick=\"window.location='view_reviews.php?product_id={$product['product_id']}'\">Просмотреть отзывы</button>";
                        echo "<button class='btn btn-danger delete-btn' onclick=\"deleteProduct({$product['product_id']})\">Удалить товар</button>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    } else {
                        echo "<p class='error-message'>Товар не найден.</p>";
                    }
                } else {
                    echo "<p class='error-message'>Ошибка выполнения запроса: " . $stmt->error . "</p>";
                }

                $stmt->close();
            }
        } else {
            $sql = "SELECT * FROM products WHERE user_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $_SESSION["user_id"]);

            if ($stmt->execute()) {
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo "<h1 class='text-center mb-4'>Список товаров</h1>";
                    while ($product = $result->fetch_assoc()) {
                        echo "<div class='product-card'>";
                        echo "<img src='{$product['image_path']}' alt='Product Image'>";
                        echo "<div class='product-card-body'>";
                        echo "<h2 class='product-title'>" . htmlspecialchars($product['name']) . "</h2>";
                        echo "<div class='product-buttons'>";
                        echo "<button class='btn btn-primary view-details-btn' onclick=\"window.location='products.php?product_id={$product['product_id']}'\">Подробнее</button>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>Нет доступных товаров.</p>";
                }
            } else {
                echo "<p class='error-message'>Ошибка выполнения запроса: " . $stmt->error . "</p>";
            }

            $stmt->close();
        }

        $conn->close();
    } else {
        echo "<p>Пожалуйста, <a href='login.html'>войдите</a>, чтобы просматривать информацию о товарах.</p>";
        exit();
    }
    ?>
</div>

<script>
    function deleteProduct(productId) {
        if (confirm("Вы уверены, что хотите удалить этот товар?")) {
            fetch(`delete_product.php?product_id=${productId}`)
                .then(response => response.text())
                .then(message => {
                    alert(message);
                    window.location.reload();
                })
                .catch(error => {
                    console.error('Error during delete:', error);
                });
        }
    }
</script>
</body>
</html>