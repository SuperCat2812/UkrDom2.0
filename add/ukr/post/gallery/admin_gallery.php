<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Post</title>
    <link rel="stylesheet" href="./styles/create.post.css">
</head>
<body>
<form action="add-img.php" method="POST" enctype="multipart/form-data">
    <label for="images">Выберите изображения:</label>
    <input type="file" name="images[]" id="images" multiple>
    <button type="submit">Загрузить</button>
</form>

<div class="gallery">
    <?php
    require "config.php";

    $uploadsFolder = 'images/gallery_img';
    if (!is_dir($uploadsFolder)) {
        mkdir($uploadsFolder, 0755, true);
    }

    $files = scandir($uploadsFolder);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;

        $filePath = $uploadsFolder . '/' . $file;

        if (is_file($filePath) && preg_match('/\.(jpg|jpeg|png|gif)$/i', $file)) {
            // Проверка, есть ли уже в базе
            $stmt = $conn->prepare("SELECT COUNT(*) FROM image_gallery WHERE image_path = ?");
            $stmt->bind_param("s", $filePath);
            $stmt->execute();
            $stmt->bind_result($count);
            $stmt->fetch();
            $stmt->close();

            if ($count == 0) {
                $imageName = basename($file);
                $imageType = mime_content_type($filePath);
                $imageData = file_get_contents($filePath);
                $description = "Изображение из папки";
                $stmt = $conn->prepare("INSERT INTO image_gallery (image_name, image_path, description,image_data,image_type) VALUES (?, ?, ?,?,?)");
                $stmt->bind_param("sssss", $imageName,$filePath, $description, $imageData,$imageType);
                if ($stmt->execute()) {
                    echo "<p>Добавлено в БД: " . htmlspecialchars($file, ENT_QUOTES, 'UTF-8') . "</p>";
                } else {
                    echo "<p>Ошибка добавления '$file'</p>";
                }
                $stmt->close();
            }
            if($file!=$count) {
// Восстановление недостающих файлов из БД
                $stmt = $conn->prepare("SELECT image_name, image_path, image_data, image_type FROM image_gallery");
                $stmt->execute();
                $stmt->bind_result($imageName, $imagePath, $imageData, $imageType);

                while ($stmt->fetch()) {
                    if (!file_exists($imagePath)) {
                        $ext = pathinfo($imageName, PATHINFO_EXTENSION);
                        $savePath = $imagePath;

                        // Убедимся, что директория существует
                        $dir = dirname($savePath);
                        if (!is_dir($dir)) {
                            mkdir($dir, 0755, true);
                        }

                        // Запись бинарных данных в файл
                        if (file_put_contents($savePath, $imageData)) {
                            echo "<p>Восстановлен отсутствующий файл: $imageName</p>";
                        } else {
                            echo "<p style='color:red;'>Ошибка при восстановлении: $imageName</p>";
                        }
                    }
                }
                $stmt->close();
            }

            // Отображение изображения
            $safePath = htmlspecialchars($filePath, ENT_QUOTES, 'UTF-8');
            echo "<img src='$safePath' alt='img' style='width:200px; margin:10px;'>";
        }
    }

    $conn->close();
    ?>
</div>
</body>
</html>
