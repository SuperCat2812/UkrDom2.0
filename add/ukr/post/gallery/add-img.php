<?php
require 'config.php'; // Подключение к базе данных

$dir = 'images/gallery_img/';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

// Макс. размер файла (например, 5 MB)
$maxFileSize = 5 * 1024 * 1024;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    foreach ($_FILES['images']['name'] as $key => $name) {
        $tmpName = $_FILES['images']['tmp_name'][$key];
        $error = $_FILES['images']['error'][$key];
        $size = $_FILES['images']['size'][$key];

        // Проверка на ошибки загрузки
        if ($error !== UPLOAD_ERR_OK) {
            echo "Ошибка при загрузке файла '$name'. Код ошибки: $error.<br>";
            continue;
        }

        // Проверка размера
        if ($size > $maxFileSize) {
            echo "Файл '$name' превышает максимально допустимый размер.<br>";
            continue;
        }

        // Проверка изображения
        $imageInfo = @getimagesize($tmpName);
        if ($imageInfo === false) {
            echo "Файл '$name' не является изображением.<br>";
            continue;
        }

        // Проверка расширения
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        $imageFileType = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($imageFileType, $allowedExtensions)) {
            echo "Файл '$name' имеет недопустимый формат.<br>";            continue;
        }

        // Генерация уникального имени файла

        $targetFile = $dir . $name;

        // Перемещение файла
        if (move_uploaded_file($tmpName, $targetFile)) {
            echo "Файл успешно загружен: " . htmlspecialchars($name) . "<br>";

            // Запись в базу данных (используем PDO для безопасности)
            $imageData = file_get_contents($targetFile);
            $imageType = mime_content_type($targetFile);
            $description = 'Загруженный файл';
            $stmt = $conn->prepare("INSERT INTO image_gallery (image_name, image_path, description,image_data,image_type) VALUES (?, ?, ?,?,?)");
            if ($stmt) {
                $stmt->bind_param("sssss", $name,$targetFile, $description, $imageData,$imageType);
                if ($stmt->execute()) {
                    echo "Файл '$name' добавлен в базу данных.<br>";
                } else {
                    echo "Ошибка при добавлении '$name' в БД: " . $stmt->error . "<br>";
                }
                $stmt->close();
            } else {
                echo "Ошибка подготовки запроса для '$name': " . $conn->error . "<br>";
            }
        } else {
            echo "Ошибка при сохранении файла '$name'.<br>";
        }
    }
}

// Отображение загруженных изображений
$files = scandir($dir);
foreach ($files as $file) {
    if ($file !== '.' && $file !== '..') {
        $filePath = $dir . $file;
        if (is_file($filePath) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
            $safePath = htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8');
            $safeAlt = htmlspecialchars($file, ENT_QUOTES, 'UTF-8');
            echo "<img src='$safePath' alt='$safeAlt' style='width:200px; margin:10px;'>";
        }
    }
}
echo '<br><a href="admin_gallery.php">Назад</a>';
?>
