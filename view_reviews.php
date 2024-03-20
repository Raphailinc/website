<?php
session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit();
}

require_once 'db_connection.php';

if (isset($_GET["product_id"]) && is_numeric($_GET["product_id"])) {
    $product_id = intval($_GET["product_id"]);

    $user_id = $_SESSION["user_id"];
    $sql = "SELECT * FROM reviews WHERE product_id = ? AND user_id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $product_id, $user_id);
    $stmt->execute();

    $result = $stmt->get_result();

    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Отзывы</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f2f2f2;
                margin: 0;
                padding: 20px;
            }
            .container {
                max-width: 800px;
                margin: 0 auto;
                background-color: #fff;
                padding: 20px;
                border-radius: 8px;
                box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            }
            h2 {
                color: #333;
                text-align: center;
            }
            .review {
                margin-bottom: 20px;
                border-bottom: 1px solid #ccc;
                padding-bottom: 20px;
            }
            .rating {
                color: #ff9900;
                font-size: 18px;
                font-weight: bold;
            }
            .comment {
                color: #666;
            }
            .empty {
                text-align: center;
                color: #999;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <h2>Ваши отзывы</h2>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                ?>
                <div class="review">
                    <span class="rating">Оценка: <?php echo $row["rating"]; ?></span><br>
                    <span class="comment">Отзыв: <?php echo $row["comment"]; ?></span>
                </div>
                <?php
            }
        } else {
            ?>
            <p class="empty">Нет доступных отзывов для этого товара.</p>
            <?php
        }
        ?>
    </div>
    </body>
    </html>
    <?php

    $stmt->close();
} else {
    echo "product_id не указан в запросе.";
}

$conn->close();
?>
