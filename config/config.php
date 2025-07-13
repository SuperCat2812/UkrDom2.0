<?php
// Настройки базы данных
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'ukrdom_img';
$charset = 'utf8';

//$dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName;charset=$charset";
$dsn = "mysql:host=$dbHost;dbname=$dbName;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // выбрасывать ошибки
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // выборка как ассоциативный массив
    PDO::ATTR_EMULATE_PREPARES   => false,                  // отключить эмуляцию prepared statements
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (\PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}