<?php
session_start();
require '../config/database.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = $_POST['category_name'] ?? null;

    if ($category_name) {
        $stmt = $connection->prepare("INSERT INTO categories (name) VALUES (?)");
        $result = $stmt->execute([$category_name]);

        if ($result) {
            $success_message = "Category added successfully!";
        } else {
            $error_message = "Error adding category.";
        }
    } else {
        $error_message = "Category name is required.";
    }
}


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_category'])) {
    $category_id = $_POST['category_id'] ?? null;
    $new_category_name = $_POST['new_category_name'] ?? null;

    if ($category_id && $new_category_name) {
        $stmt = $connection->prepare("UPDATE categories SET name = ? WHERE id = ?");
        $result = $stmt->execute([$new_category_name, $category_id]);

        if ($result) {
            $success_message = "Category updated successfully!";
        } else {
            $error_message = "Error updating category.";
        }
    } else {
        $error_message = "Category name is required.";
    }
}

// Handle deleting a category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_category'])) {
    $category_id = $_POST['category_id'] ?? null;

    if ($category_id) {
        $stmt = $connection->prepare("DELETE FROM categories WHERE id = ?");
        $result = $stmt->execute([$category_id]);

        if ($result) {
            $success_message = "Category deleted successfully!";
        } else {
            $error_message = "Error deleting category.";
        }
    } else {
        $error_message = "Invalid category.";
    }
}


$categoriesQuery = $connection->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $categoriesQuery->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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

        .main-content h2 {
            margin-left: 30px;
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
        <a href="admin_dashboard.php">Manage Categories</a>
        <a href="../auth/logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h2>Manage Categories</h2>

       
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>

        
        <form action="" method="POST" class="mb-5">
            <div class="mb-3">
                <label for="category_name" class="form-label">Category Name</label>
                <input type="text" class="form-control" id="category_name" name="category_name" required>
            </div>
            <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
        </form>

      
        <h3>Existing Categories</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>Category Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($category['name']); ?></td>
                        <td>
                            <!-- Edit Category Form -->
                            <form action="" method="POST" class="d-inline">
                                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                <input type="text" name="new_category_name" class="form-control d-inline-block w-50" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                                <button type="submit" name="edit_category" class="btn btn-warning">Edit</button>
                            </form>
                            <!-- Delete Category Form -->
                            <form action="" method="POST" class="d-inline">
                                <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                <button type="submit" name="delete_category" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this category?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
