<?php
// Настройки базы данных
$dbHost = 'mysql80.r6.websupport.sk';
$dbPort = '3314';
$dbUser = 'UkrDomZA';
$dbPass = 'Verbatim1@2';
$dbName = 'ukrdom_db';
$charset = 'utf8';

$dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=$charset";

// Проверка подключения
if ($conn->connect_error) {
    die("Ошибка подключения: " . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8'));
}
?>
