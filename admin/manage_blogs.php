<?php
session_start();
require '../config/database.php';

// Blogların siyahısını əldə et (təsdiqlənməmiş bloglar)
$blogsQuery = $connection->query("SELECT * FROM blogs WHERE is_publish = 0 ORDER BY created_at DESC");
$blogs = $blogsQuery->fetchAll(PDO::FETCH_ASSOC);

// Blog statusunu dəyişdirmək (Təsdiqləmək)
if (isset($_POST['change_blog_status'])) {
    $blog_id = $_POST['blog_id'];
    $status = $_POST['status'];

    $updateBlogStatusQuery = $connection->prepare("UPDATE blogs SET is_publish = ? WHERE id = ?");
    $updateBlogStatusQuery->execute([$status, $blog_id]);

    header("Location: manage_blogs.php");
    exit();
}

// Blogu silmək
if (isset($_POST['delete_blog'])) {
    $blog_id = $_POST['blog_id'];

    $deleteBlogQuery = $connection->prepare("DELETE FROM blogs WHERE id = ?");
    $deleteBlogQuery->execute([$blog_id]);

    header("Location: manage_blogs.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Blogs</title>
    <style>
 
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
}


.sidebar {
    width: 250px;
    height: 100vh;
    background-color: darkblue;
    color: white;
    padding: 20px;
    position: fixed;
}

.sidebar h2 {
    margin-top: 0;
}

.sidebar a {
    display: block;
    color: white;
    text-decoration: none;
    padding: 10px;
    border-radius: 4px;
    margin-bottom: 10px;
}

.sidebar a:hover {
    background-color: #024872;
}


.main-content {
    margin-left: 250px;
    padding: 20px;
    width: calc(100% - 250px);
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background-color: #e1eff8;
    padding: 10px 20px;
    border-bottom: 1px solid #ddd;
}

.header h1 {
    margin: 0;
}

.header .search-bar input {
    padding: 5px;
    font-size: 16px;
}

.content {
    margin-top: 20px;
}

.content h2 {
    border-bottom: 2px solid #333;
    padding-bottom: 10px;
    margin-bottom: 20px;
}


table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    margin-left: 30px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}
.main-content h2{
    margin-left: 30px;
}


.btn-group {
    display: flex;
    gap: 4px; 
}


.btn {
    padding: 5px 10px;
    text-decoration: none;
    border-radius: 4px;
    border: none;
    cursor: pointer;
}

.btn-warning {
    background-color: #ffc107;
    color: white;
}

.btn-warning:hover {
    background-color: #e0a800;
}

.btn-success {
    background-color: #28a745;
    color: white;
}

.btn-success:hover {
    background-color: #218838;
}

.btn-danger {
    background-color: #dc3545;
    color: white;
}

.btn-danger:hover {
    background-color: #c82333;
}

    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="index.php">Dashboard</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_blogs.php">Manage Blogs</a>
        <a href="admin_report.php">Admin Report</a>
        <a href="../auth/logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h2>Manage Blogs</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($blogs as $blog): ?>
                    <tr>
                        <td><?php echo $blog['id']; ?></td>
                        <td><?php echo htmlspecialchars($blog['title']); ?></td>
                        <td><?php echo htmlspecialchars($blog['user_id']); ?></td>
                        <td><?php echo $blog['is_publish'] ? 'Published' : 'Unpublished'; ?></td>
                        <td>
                            <div class="btn-group">
                                <form method="POST" action="">
                                    <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
                                    <input type="hidden" name="status" value="<?php echo $blog['is_publish'] ? 0 : 1; ?>">
                                    <button type="submit" name="change_blog_status" class="btn btn-<?php echo $blog['is_publish'] ? 'warning' : 'success'; ?>">
                                        <?php echo $blog['is_publish'] ? 'Unpublish' : 'Publish'; ?>
                                    </button>
                                </form>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="blog_id" value="<?php echo $blog['id']; ?>">
                                    <button type="submit" name="delete_blog" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this blog?');">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
