<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST["product_id"];
    $rating = intval($_POST["rating"]);
    $comment = $_POST["comment"];

    $user_id = $_SESSION["user_id"];

    require_once 'db_connection.php';

    $sql = "INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);

	$stmt_check_product = $conn->prepare("SELECT product_id FROM products WHERE product_id = ?");
	$stmt_check_product->bind_param("i", $product_id);
	$stmt_check_product->execute();
	$stmt_check_product->store_result();

    if ($stmt_check_product->num_rows > 0) {
		$stmt_check_product->close();
		$stmt->execute();
		echo "Отзыв успешно добавлен.";
	} else {
		echo "Товар с указанным ID не существует.";
	}

    $stmt->close();
    $conn->close();
}
?>
