<?php
session_start();
require '../../config/database.php';
include '../../navbar2.php';

// Fetch categories from the database
$categoriesQuery = $connection->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'] ?? null;
    $description = $_POST['description'] ?? null;
    $category_id = $_POST['category_id'] ?? null;
    $profile = '';
    
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        echo "User ID is not set. Please log in.";
        exit();
    }
    
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
                $profile = $newFileName;
            } else {
                echo "There was an error moving the uploaded file.";
                exit();
            }
        } else {
            echo "Upload failed. Allowed file types: " . implode(',', $allowedfileExtensions);
            exit();
        }
    }

    if ($title && $description && $category_id && $user_id) {
        $stmt = $connection->prepare("INSERT INTO blogs (user_id, title, description, profile, category_id, is_publish) VALUES (?, ?, ?, ?, ?, 0)");
        $result = $stmt->execute([$user_id, $title, $description, $profile, $category_id]);

        if ($result) {
            header("Location: homeblog.php");
            exit();
        } else {
            echo "Error creating blog.";
        }
    } else {
        echo "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Blog</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Add New Blog</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea class="form-control" id="description" name="description" rows="5" required></textarea>
        </div>
        <div class="mb-3">
            <label for="category" class="form-label">Category</label>
            <select class="form-control" id="category" name="category_id" required>
                <option value="">Select a category</option>
                <?php foreach ($categories as $category): ?>
                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="mb-3">
            <label for="profile" class="form-label">Profile Image</label>
            <input type="file" class="form-control" id="profile" name="profile">
        </div>
        <button type="submit" class="btn btn-primary">Add Blog</button>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
