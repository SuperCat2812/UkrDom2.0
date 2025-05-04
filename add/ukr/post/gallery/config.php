<?php
// Настройки базы данных
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'ukrdom_img';

// Подключение к базе данных
$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8'));
}
?>
