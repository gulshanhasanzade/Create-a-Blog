<?php
session_start();
require '../../config/database.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Blog ID is not set. Please go back and select a blog to delete.";
    exit;
}

$blog_id = $_GET['id'];

// Blogun məlumatlarını əldə etmək
$stmt = $connection->prepare("SELECT * FROM blogs WHERE id = ?");
$stmt->execute([$blog_id]);
$blog = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$blog) {
    echo "Blog not found.";
    exit;
}

// Blogun sahibi olub-olmadığını yoxlamaq
if ($blog['user_id'] != $_SESSION['user_id']) {
    echo "You do not have permission to delete this blog.";
    exit;
}

// Blogu silmək
$deleteStmt = $connection->prepare("DELETE FROM blogs WHERE id = ?");
$deleteResult = $deleteStmt->execute([$blog_id]);

if ($deleteResult) {
    // Şəkili də silmək (əgər varsa)
    if (!empty($blog['profile']) && file_exists("../../uploads/blog_images/" . $blog['profile'])) {
        unlink("../../uploads/blog_images/" . $blog['profile']);
    }

    // Uğurla silindiyini bildirmək üçün yönləndirmə
    header("Location: homeblog.php?message=Blog deleted successfully.");
    exit();
} else {
    echo "Error deleting blog.";
}
?>
