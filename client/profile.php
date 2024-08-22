<?php
session_start();
include '../config/database.php'; 
include '../helper/helper.php';  
include '../head.php';  
include '../navbar2.php';

$searchTitle = $_GET['title'] ?? '';
$searchDescription = $_GET['description'] ?? '';
$searchAuthor = $_GET['author'] ?? '';
$searchCategory = $_GET['category'] ?? '';

$categoriesQuery = $connection->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);

$perPage = 4; 
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $perPage;

$totalBlogsQuery = $connection->prepare("
    SELECT COUNT(*) 
    FROM blogs
    LEFT JOIN users ON blogs.user_id = users.id
    LEFT JOIN categories ON blogs.category_id = categories.id
    WHERE blogs.is_publish = 1 AND blogs.title LIKE ? AND blogs.description LIKE ? AND users.name LIKE ? AND categories.name LIKE ?
");
$totalBlogsQuery->execute(["%$searchTitle%", "%$searchDescription%", "%$searchAuthor%", "%$searchCategory%"]);
$totalBlogs = $totalBlogsQuery->fetchColumn();

$totalPages = ceil($totalBlogs / $perPage);

$blogsQuery = $connection->prepare("
    SELECT blogs.*, users.name AS author_name, categories.name AS category_name 
    FROM blogs
    LEFT JOIN users ON blogs.user_id = users.id
    LEFT JOIN categories ON blogs.category_id = categories.id
    WHERE blogs.is_publish = 1 AND blogs.title LIKE ? AND blogs.description LIKE ? AND users.name LIKE ? AND categories.name LIKE ?
    ORDER BY blogs.created_at DESC
    LIMIT " . intval($perPage) . " OFFSET " . intval($offset) . "
");

$blogsQuery->execute(["%$searchTitle%", "%$searchDescription%", "%$searchAuthor%", "%$searchCategory%"]);

$blogs = $blogsQuery->fetchAll(PDO::FETCH_ASSOC);

$lastFiveBlogsQuery = $connection->query("SELECT * FROM blogs WHERE is_publish = 1 ORDER BY created_at DESC LIMIT 5");
$lastFiveBlogs = $lastFiveBlogsQuery->fetchAll(PDO::FETCH_ASSOC);

$topFiveBlogsQuery = $connection->query("SELECT * FROM blogs WHERE is_publish = 1 ORDER BY view_count DESC LIMIT 5");
$topFiveBlogs = $topFiveBlogsQuery->fetchAll(PDO::FETCH_ASSOC);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog Page</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .blog-image {
            width: 100%; 
            height: auto; 
            object-fit: cover;
        }
        
        .scrollable-card {
            width: 100%;
            max-height: 55vh;
            overflow-y: auto;
        }

        .container {
            /* width: 95%; */
            max-width: 95%;
        }

        .card-body1 {
            width: 200px;
            display: block;
            margin: 0 auto;
        }

        .col-md-4, .col-md-41 {
            padding-left: 15px;
            padding-right: 15px;
        }

        .row {
            margin-left: -15px;
            margin-right: -15px;
        }
        
        .col-md-8 {
            padding-left: 15px;
            padding-right: 15px;
        }

        .mb-4, .mb-41 {
            margin-bottom: 20px !important;
        }
        .card.mb-41 {
          max-width: 250px; 
          margin: 0 auto; 
         }

        .card-body1 {
          max-width: 300px; 
          margin: 0 auto;
         }

         
    </style>
</head>
<body>

<div class="container mt-5">
    <form action="" method="GET" class="mb-5">
        <div class="row">
            <div class="col-md-3">
                <input type="text" class="form-control" name="title" placeholder="Search by Title" value="<?php echo htmlspecialchars($searchTitle); ?>">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="description" placeholder="Search by Description" value="<?php echo htmlspecialchars($searchDescription); ?>">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="author" placeholder="Search by Author" value="<?php echo htmlspecialchars($searchAuthor); ?>">
            </div>
            <div class="col-md-3">
                <select class="form-control" name="category">
                    <option value="">Search by Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category['name']); ?>" <?php if ($category['name'] == $searchCategory) echo 'selected'; ?>><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Search</button>
    </form>

    <div class="row">
        <div class="col-md-8">
            <h2>All Blogs</h2>
            <div class="row">
                <?php if (!empty($blogs)) : ?>
                    <?php foreach ($blogs as $blog): ?>
                        <div class="col-md-3 mb-4"> 
                            <div class="card">
                                <?php if (!empty($blog['profile'])): ?>
                                    <img src="<?php echo htmlspecialchars('../uploads/blog_images/'.$blog['profile']); ?>" class="card-img-top blog-image" alt="Blog Image">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($blog['title']); ?></h5>
                                    <p class="card-text"><?php echo htmlspecialchars(substr($blog['description'], 0, 100)); ?>...</p>
                                    <p class="card-text"><small class="text-muted">Author: <?php echo htmlspecialchars($blog['author_name']); ?></small></p>
                                    <p class="card-text"><small class="text-muted">Category: <?php echo htmlspecialchars($blog['category_name']); ?></small></p>
                                    <p class="card-text"><small class="text-muted">Views: <?php echo $blog['view_count']; ?></small></p>
                                    <a href="blog/viewblog.php?id=<?php echo $blog['id']; ?>" class="btn btn-primary">Read More</a>
                                    
                                    <?php if ($blog['user_id'] == $_SESSION['user_id']): ?>
                                        <a href="blog/editblog.php?id=<?php echo $blog['id']; ?>" class="btn btn-secondary">Edit</a>
                                        <a href="blog/deleteblog.php?id=<?php echo $blog['id']; ?>" class="btn btn-danger mt-2" onclick="return confirm('Are you sure you want to delete this blog?');">Delete</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No blogs available.</p>
                <?php endif; ?>
            </div>

          
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&title=<?php echo urlencode($searchTitle); ?>&description=<?php echo urlencode($searchDescription); ?>&author=<?php echo urlencode($searchAuthor); ?>&category=<?php echo urlencode($searchCategory); ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                                <span class="sr-only">Previous</span>
                            </a>
                        </li>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php if ($i == $page) echo 'active'; ?>"><a class="page-link" href="?page=<?php echo $i; ?>&title=<?php echo urlencode($searchTitle); ?>&description=<?php echo urlencode($searchDescription); ?>&author=<?php echo urlencode($searchAuthor); ?>&category=<?php echo urlencode($searchCategory); ?>"><?php echo $i; ?></a></li>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&title=<?php echo urlencode($searchTitle); ?>&description=<?php echo urlencode($searchDescription); ?>&author=<?php echo urlencode($searchAuthor); ?>&category=<?php echo urlencode($searchCategory); ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                                <span class="sr-only">Next</span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
<div class="col-md-4">
    <div class="card mb-41">
        <div class="card-body1">
            <h2>Top 5 Blogs</h2>
            <div class="scrollable-card">
                <?php foreach ($topFiveBlogs as $blog): ?>
                    <div class="mb-3">
                        <?php if (!empty($blog['profile'])): ?>
                            <img src="<?php echo htmlspecialchars('../uploads/blog_images/'.$blog['profile']); ?>" class="blog-image mb-2" alt="Blog Image">
                        <?php endif; ?>
                        <h5 class="card-title"><?php echo htmlspecialchars($blog['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(substr($blog['description'], 0, 50)); ?>...</p>
                        <p class="card-text"><small class="text-muted">Views: <?php echo $blog['view_count']; ?></small></p>
                        <a href="blog/viewblog.php?id=<?php echo $blog['id']; ?>" class="btn btn-primary btn-sm">Read More</a>
                    </div>
                    <hr>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

   
    <div class="card mb-41">
        <div class="card-body1">
            <h2>Last 5 Blogs</h2>
            <div class="scrollable-card">
                <?php foreach ($lastFiveBlogs as $blog): ?>
                    <div class="mb-3">
                        <?php if (!empty($blog['profile'])): ?>
                            <img src="<?php echo htmlspecialchars('../uploads/blog_images/'.$blog['profile']); ?>" class="blog-image mb-2" alt="Blog Image">
                        <?php endif; ?>
                        <h5 class="card-title"><?php echo htmlspecialchars($blog['title']); ?></h5>
                        <p class="card-text"><?php echo htmlspecialchars(substr($blog['description'], 0, 50)); ?>...</p>
                        <p class="card-text"><small class="text-muted">Views: <?php echo $blog['view_count']; ?></small></p>
                        <a href="blog/viewblog.php?id=<?php echo $blog['id']; ?>" class="btn btn-primary btn-sm">Read More</a>

                        <?php if ($blog['user_id'] == $_SESSION['user_id']): ?>
                            <a href="blog/editblog.php?id=<?php echo $blog['id']; ?>" class="btn btn-secondary btn-sm">Edit</a>
                            <a href="blog/deleteblog.php?id=<?php echo $blog['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this blog?');">Delete</a>
                        <?php endif; ?>
                    </div>
                    <hr>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>