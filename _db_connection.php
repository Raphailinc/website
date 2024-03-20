<?php
$host = "localhost";
$user = ""; // Замените на имя пользователя базы данных
$pswd = ""; // Замените на пароль пользователя базы данных
$database = "web_catalog";

try {
    $conn = new mysqli($host, $user, $pswd, $database);
    $conn->set_charset("utf8");
} catch (Exception $e) {
    die("Ошибка соединения с базой данных: " . $e->getMessage());
}
?>