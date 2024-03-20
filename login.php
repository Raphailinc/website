<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];

    if (empty($username) || empty($password)) {
        echo "Пожалуйста, заполните все поля.";
        exit();
    }

    require_once 'db_connection.php';

    $query = "SELECT user_id, password FROM users WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];
        if (password_verify($password, $hashedPassword)) {
            session_start();

            $_SESSION["user_id"] = $row['user_id'];
            session_regenerate_id();
            header("Location: welcome.html");
            exit();
        } else {
            echo "Неверный пароль";
        }
    } else {
        echo "Пользователь не найден.";
    }

    $stmt->close();
    $conn->close();
}
?>
