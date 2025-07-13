<?php
include_once __DIR__ . '/gallery/config.php';

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$action = $_POST['action'] ?? '';
$name = $_POST['name'] ?? '';
$about = $_POST['about'] ?? '';
$start_date = $_POST['start_date'] ?? '';
$end_date = $_POST['end_date'] ?? '';
$id = $_POST['id'] ?? null;

$imageData = null;
$imageType = null;

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $imageData = file_get_contents($_FILES['image']['tmp_name']);
    $imageType = $_FILES['image']['type'];
}

if ($action === 'create') {
    $sql = "INSERT INTO Post (name_post, about_post, start_data, end_data, image_data, image_type, uploaded_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        $name,
        $about,
        $start_date,
        $end_date,
        $imageData,
        $imageType
    ]);
} elseif ($action === 'update' && $id !== null) {
    if ($imageData !== null) {
        $sql = "UPDATE Post SET name_post = ?, about_post = ?, start_data = ?, end_data = ?, image_data = ?, image_type = ?, uploaded_at = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $name,
            $about,
            $start_date,
            $end_date,
            $imageData,
            $imageType,
            $id
        ]);
    } else {
        $sql = "UPDATE Post SET name_post = ?, about_post = ?, start_data = ?, end_data = ?, uploaded_at = NOW() WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $name,
            $about,
            $start_date,
            $end_date,
            $id
        ]);
    }
}

header("Location: admin_post.php");
exit;
