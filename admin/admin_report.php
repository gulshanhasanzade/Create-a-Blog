<?php
session_start();
require '../config/database.php';

$today = date('Y-m-d');
$blogsTodayQuery = $connection->prepare("SELECT title FROM blogs WHERE DATE(created_at) = ?");
$blogsTodayQuery->execute([$today]);
$blogsToday = $blogsTodayQuery->fetchAll(PDO::FETCH_ASSOC);
$blogsTodayCount = count($blogsToday);

$currentMonth = date('Y-m');
$blogsThisMonthQuery = $connection->prepare("SELECT title FROM blogs WHERE DATE_FORMAT(created_at, '%Y-%m') = ?");
$blogsThisMonthQuery->execute([$currentMonth]);
$blogsThisMonth = $blogsThisMonthQuery->fetchAll(PDO::FETCH_ASSOC);
$blogsThisMonthCount = count($blogsThisMonth);

$blogsPerCategoryQuery = $connection->query("
    SELECT categories.name AS category_name, COUNT(blogs.id) AS blog_count
    FROM categories
    LEFT JOIN blogs ON categories.id = blogs.category_id
    GROUP BY categories.id
");
$blogsPerCategory = $blogsPerCategoryQuery->fetchAll(PDO::FETCH_ASSOC);

$blogsPerUserQuery = $connection->query("
    SELECT users.name AS user_name, COUNT(blogs.id) AS blog_count
    FROM users
    LEFT JOIN blogs ON users.id = blogs.user_id
    GROUP BY users.id
");
$blogsPerUser = $blogsPerUserQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            flex-direction: column;
        }
        .wrapper {
            display: flex;
            flex: 1;
        }
        .sidebar {
            width: 250px;
            background-color: #00008B; /* Dark Blue */
            color: white;
            padding: 15px;
            min-height: 100vh;
        }
        .sidebar a {
            color: white;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin-bottom: 5px;
            border-radius: 4px;
        }
        .sidebar a:hover {
            background-color: #024872;
        }
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="sidebar">
            <h2>Admin Panel</h2>
            <a href="index.php">Dashboard</a>
            <a href="manage_users.php">Manage Users</a>
            <a href="manage_blogs.php">Manage Blogs</a>
            <a href="admin_report.php">Admin Report</a>
            <a href="../auth/logout.php">Logout</a>
        </div>

        <div class="main-content">
            <div class="container">
                <h2 class="mb-4">Admin Report</h2>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        Blogs Created Today
                    </div>
                    <div class="card-body">
                        <p>Number of blogs created today: <strong><?php echo $blogsTodayCount; ?></strong></p>
                        <?php if ($blogsTodayCount > 0): ?>
                            <ul class="list-group">
                                <?php foreach ($blogsToday as $blog): ?>
                                    <li class="list-group-item"><?php echo htmlspecialchars($blog['title']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">No blogs created today.</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        Blogs Created This Month
                    </div>
                    <div class="card-body">
                        <p>Number of blogs created this month: <strong><?php echo $blogsThisMonthCount; ?></strong></p>
                        <?php if ($blogsThisMonthCount > 0): ?>
                            <ul class="list-group">
                                <?php foreach ($blogsThisMonth as $blog): ?>
                                    <li class="list-group-item"><?php echo htmlspecialchars($blog['title']); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p class="text-muted">No blogs created this month.</p>
                        <?php endif; ?>
                    </div>
                </div>

              
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        Blogs Category
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Number of Blogs</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($blogsPerCategory as $category): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                        <td><?php echo $category['blog_count']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                       User Blogs
                    </div>
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Number of Blogs</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($blogsPerUser as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['user_name']); ?></td>
                                        <td><?php echo $user['blog_count']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
