<?php
session_start();
require '../../config/database.php';


$searchTitle = $_GET['title'] ?? '';
$searchDescription = $_GET['description'] ?? '';
$searchAuthor = $_GET['author'] ?? '';
$searchCategory = $_GET['category'] ?? '';

// Fetch categories for the category filter dropdown
$categoriesQuery = $connection->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);

// Fetch blogs based on search criteria
$blogsQuery = $connection->prepare("
    SELECT blogs.*, users.name AS author_name, categories.name AS category_name 
    FROM blogs
    LEFT JOIN users ON blogs.user_id = users.id
    LEFT JOIN categories ON blogs.category_id = categories.id
    WHERE blogs.title LIKE ? AND blogs.description LIKE ? AND users.name LIKE ? AND categories.name LIKE ?
");
$blogsQuery->execute(["%$searchTitle%", "%$searchDescription%", "%$searchAuthor%", "%$searchCategory%"]);
$blogs = $blogsQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Blogs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Search Blogs</h2>
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
        <?php if (!empty($blogs)): ?>
            <?php foreach ($blogs as $blog): ?>
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($blog['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars(substr($blog['description'], 0, 100)); ?>...</p>
                            <p class="card-text"><small class="text-muted">Author: <?php echo htmlspecialchars($blog['author_name']); ?></small></p>
                            <p class="card-text"><small class="text-muted">Category: <?php echo htmlspecialchars($blog['category_name']); ?></small></p>
                            <a href="viewblog.php?id=<?php echo $blog['id']; ?>" class="btn btn-primary">Read More</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No blogs found matching your search criteria.</p>
        <?php endif; ?>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
