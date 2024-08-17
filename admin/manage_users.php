<?php
session_start();
require '../config/database.php';


$usersQuery = $connection->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $usersQuery->fetchAll(PDO::FETCH_ASSOC);


if (isset($_POST['change_status'])) {
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];

    $updateStatusQuery = $connection->prepare("UPDATE users SET active = ? WHERE id = ?");
    $updateStatusQuery->execute([$status, $user_id]);

    header("Location: manage_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users</title>
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
.main-content h2{
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
        <a href="admin_report.php">Admin Report</a>
        <a href="../auth/logout.php">Logout</a>
    </div>

    <div class="main-content">
        <h2>Manage Users</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td><?php echo $user['active'] ? 'Active' : 'Deactivated'; ?></td>
                        <td>
                            <form method="POST" action="">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <input type="hidden" name="status" value="<?php echo $user['active'] ? 0 : 1; ?>">
                                <button type="submit" name="change_status" class="btn btn-<?php echo $user['active'] ? 'warning' : 'success'; ?>">
                                    <?php echo $user['active'] ? 'Deactivate' : 'Activate'; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
