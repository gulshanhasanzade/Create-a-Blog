<?php
session_start();
require '../../config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Blog ID is not set. Please go back and select a blog to edit.";
    exit;
}

$blog_id = $_GET['id'];

$stmt = $connection->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->execute([$blog_id]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    echo "Blog not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? null;
    $description = $_POST['description'] ?? null;
    $profile = $blog['profile']; // Mövcud şəkil adı burada saxlanır

    // Şəkil yükləmə prosesi
    if (isset($_FILES['profile']) && $_FILES['profile']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['profile']['tmp_name'];
        $fileName = $_FILES['profile']['name'];
        $fileSize = $_FILES['profile']['size'];
        $fileType = $_FILES['profile']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $uploadFileDir = '../../uploads/blog_images/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $profile = $newFileName; // Yeni şəkil adı
            } else {
                echo "There was an error moving the uploaded file.";
                exit();
            }
        } else {
            echo "Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions);
            exit();
        }
    }

    if ($title && $description) {
        $stmt = $connection->prepare("UPDATE blogs SET title = ?, description = ?, profile = ? WHERE id = ?");
        $stmt->execute([$title, $description, $profile, $blog_id]);

        if ($stmt) {
            echo "Blog updated successfully!";
        } else {
            echo "Error updating blog.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Blog</h2>
    <form method="POST" action="" class="mt-4" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Blog Title</label>
            <input type="text" name="title" id="title" class="form-control" value="<?php echo $blog['title']; ?>" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="5" required><?php echo $blog['description']; ?></textarea>
        </div>

        <div class="mb-3">
            <label for="profile" class="form-label">Profile Image</label>
            <input type="file" name="profile" id="profile" class="form-control">
            <img src="../../uploads/blog_images/<?php echo $blog['profile']; ?>" alt="Current Image" class="img-thumbnail mt-2" width="150">
        </div>

        <button type="submit" class="btn btn-primary">Update Blog</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
