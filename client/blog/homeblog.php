<?php
session_start();
require '../../config/database.php';
include '../../navbar2.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../auth/login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch only the logged-in user's published blogs
$blogsQuery = $connection->prepare("SELECT * FROM blogs WHERE user_id = ? AND is_publish = 1 ORDER BY created_at DESC");
$blogsQuery->execute([$user_id]);
$blogs = $blogsQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Blogs</title>
   
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Şəkillər üçün ölçü */
        .blog-image {
            width: 50%;
            height: auto;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="row">
        <!-- User's All Blogs -->
        <div class="col-md-12">
            <h2>My Blogs</h2>
            <div class="row">
                <?php if (!empty($blogs)) : ?>
                    <?php foreach ($blogs as $blog): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <?php if (!empty($blog['profile'])): ?>
                                    <img src="../../uploads/blog_images/<?php echo htmlspecialchars($blog['profile']); ?>" class="card-img-top blog-image" alt="Blog Image">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($blog['title']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars(substr($blog['description'], 0, 100)); ?>...</p>
                                    <p class="card-text"><small class="text-muted">Views: <?php echo $blog['view_count']; ?></small></p> 
                                    <a href="viewblog.php?id=<?php echo $blog['id']; ?>" class="btn btn-primary">Read More</a>
                                    
                                    <!-- These buttons will only appear for the user's own blogs -->
                                    <a href="editblog.php?id=<?php echo $blog['id']; ?>" class="btn btn-secondary">Edit</a>
                                    <a href="deleteblog.php?id=<?php echo $blog['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this blog?');">Delete</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>You have not created any blogs yet.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
