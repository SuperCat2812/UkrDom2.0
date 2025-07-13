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

$editing = false;
$id = $name = $about = $start_date = $end_date = "";
$imagePreview = "";

if (isset($_GET['edit_id'])) {
    $editing = true;
    $editId = (int)$_GET['edit_id'];

    $stmt = $pdo->prepare("SELECT * FROM Post WHERE id = ?");
    $stmt->execute([$editId]);
    $post = $stmt->fetch();

    if ($post) {
        $id = $post['id'];
        $name = $post['name_post'];
        $about = $post['about_post'];
        $start_date = $post['start_data'];
        $end_date = $post['end_data'];

        if (!empty($post['image_data'])) {
            $imgType = htmlspecialchars($post['image_type']);
            $imgBase64 = base64_encode($post['image_data']);
            $imagePreview = "<img src='data:$imgType;base64,$imgBase64' style='max-width: 200px;' id='image_preview' alt='Image preview'><br>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title><?= $editing ? "Edit Post" : "Create Post" ?></title>
    <link rel="stylesheet" href="./styles/create.post.css" />
</head>
<body>
<div>
    <form class="form" method="POST" action="create_post.php" enctype="multipart/form-data">
        <?php if ($editing): ?>
            <input type="hidden" name="action" value="update" />
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>" />
        <?php else: ?>
            <input type="hidden" name="action" value="create" />
        <?php endif; ?>

        <label class="label-form" for="name">Name</label>
        <input type="text" id="name" name="name" value="<?= htmlspecialchars($name) ?>" required>

        <label class="label-form" for="about">About</label>
        <textarea id="about" name="about" rows="4" cols="50" required><?= htmlspecialchars($about) ?></textarea>

        <label class="label-form" for="start_date">Start_date</label>
        <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" required>

        <label class="label-form" for="end_date">End_date</label>
        <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" required>

        <label class="label-form" for="image">Image</label>
        <?= $imagePreview ?>
        <input type="file" id="image" name="image" accept="image/*" />

        <div class="button-hol">
            <button type="submit" class="Create" name="submit" value="create" <?= $editing ? 'style="display:none;"' : '' ?>>Create_post</button>
            <button type="submit" class="Update" name="submit" value="update" <?= $editing ? '' : 'style="display:none;"' ?>>Update_post</button>
        </div>
    </form>
    <div>
        <h1>Posts List</h1>
        <ul id="postsList">
            <?php
            $stmt = $pdo->query("SELECT id, name_post, about_post, start_data, end_data, image_data, image_type FROM Post ORDER BY uploaded_at DESC");
            while ($row = $stmt->fetch()) {
                $imgSrc = "";
                if (!empty($row['image_data']) && !empty($row['image_type'])) {
                    $imgSrc = "data:" . htmlspecialchars($row['image_type']) . ";base64," . base64_encode($row['image_data']);
                }
                echo "<div>";
                echo "<li>";
                if ($imgSrc) {
                    echo "<img src='$imgSrc' class='gallery_img' loading='lazy' alt='Post image'><br>";
                }
                echo "<strong id='post_name_" . (int)$row['id'] . "'>" . htmlspecialchars($row['name_post']) . "</strong><br>";
                echo "<span id='post_about_" . (int)$row['id'] . "'>About: " . htmlspecialchars($row['about_post']) . "</span><br>";
                echo "<span id='post_start_" . (int)$row['id'] . "'>Start: " . htmlspecialchars($row['start_data']) . "</span><br>";
                echo "<span id='post_end_" . (int)$row['id'] . "'>End: " . htmlspecialchars($row['end_data']) . "</span><br>";
                echo "</li>";
                echo "<form method='GET' style='margin-top:8px;'>";
                echo "<input type='hidden' name='edit_id' value='" . (int)$row['id'] . "'>";
                echo "<button type='submit' class='Update'>Update</button>";
                echo "</form>";
                echo "</div>";
            }
            ?>
        </ul>
    </div>
</div>
</body>
</html>