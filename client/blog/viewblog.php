<?php
session_start();
require '../../config/database.php';
include '../../navbar2.php';


$blogId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($blogId > 0) {
   
    if (!isset($_SESSION['viewed_blogs'])) {
        $_SESSION['viewed_blogs'] = [];
    }

   
    $query = $connection->prepare("SELECT blogs.*, categories.name AS category_name, users.surname AS author_name 
                                   FROM blogs 
                                   LEFT JOIN categories ON blogs.category_id = categories.id 
                                   LEFT JOIN users ON blogs.user_id = users.id 
                                   WHERE blogs.id = :id");
    $query->execute(['id' => $blogId]);
    $blog = $query->fetch(PDO::FETCH_ASSOC);

    if ($blog) {
       
        if (!in_array($blogId, $_SESSION['viewed_blogs'])) {
            $updateViewQuery = $connection->prepare("UPDATE blogs SET view_count = view_count + 1 WHERE id = :id");
            $updateViewQuery->execute(['id' => $blogId]);

            
            $_SESSION['viewed_blogs'][] = $blogId;
        }
    } else {
        echo "Blog not found!";
        exit;
    }
} else {
    echo "Invalid blog ID!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Details</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
       
        .blog-image {
            width: 200px;
            height: auto;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h1 class="mb-4"><?php echo htmlspecialchars($blog['title']); ?></h1>

    <div class="row">
        <div class="col-md-8">
            <p><strong>Category:</strong> <?php echo htmlspecialchars($blog['category_name']); ?></p>
            <p><strong>Author:</strong> <?php echo htmlspecialchars($blog['author_name']); ?></p>
            <p><strong>View Count:</strong> <?php echo htmlspecialchars($blog['view_count']); ?></p>
            <hr>

            <?php if (!empty($blog['profile'])): ?>
                <img src="../../uploads/blog_images/<?php echo htmlspecialchars($blog['profile']); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="img-fluid mb-4 blog-image">
            <?php endif; ?>

            <p><?php echo nl2br(htmlspecialchars($blog['description'])); ?></p>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Blog Information</h5>
                    <p class="card-text"><strong>Published on:</strong> <?php echo htmlspecialchars($blog['created_at']); ?></p>
                    <p class="card-text"><strong>Last Updated:</strong> <?php echo htmlspecialchars($blog['update_at']); ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
