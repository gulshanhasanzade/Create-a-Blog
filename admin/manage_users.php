<?php
session_start();
require '../config/database.php';


$limit = 5; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;


$usersQuery = $connection->prepare("SELECT * FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset");
$usersQuery->bindParam(':limit', $limit, PDO::PARAM_INT);
$usersQuery->bindParam(':offset', $offset, PDO::PARAM_INT);
$usersQuery->execute();
$users = $usersQuery->fetchAll(PDO::FETCH_ASSOC);

// Ümumi istifadəçi sayı
$totalUsersQuery = $connection->query("SELECT COUNT(*) as total FROM users");
$totalUsers = $totalUsersQuery->fetch(PDO::FETCH_ASSOC)['total'];

$totalPages = ceil($totalUsers / $limit); // Ümumi səhifə sayı

if (isset($_POST['change_status'])) {
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];

    $updateStatusQuery = $connection->prepare("UPDATE users SET active = ? WHERE id = ?");
    $updateStatusQuery->execute([$status, $user_id]);

    header("Location: manage_users.php?page=" . $page);
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


.pagination {
    display: flex;
    justify-content: center;
    list-style: none;
    padding: 0;
    margin-top: 20px;
}

.pagination li {
    margin: 0 3px;
}

.pagination a {
    display: block;
    padding: 10px 15px;
    text-decoration: none;
    border: 1px solid #ddd;
    color: #007bff;
    border-radius: 5px;
}

.pagination a:hover {
    background-color: #e1eff8;
}

.pagination .active a {
    background-color: #007bff;
    color: white;
    border-color: #007bff;
}

.pagination .disabled a {
    color: #ddd;
    pointer-events: none;
    cursor: default;
}

.pagination .page-item span {
    font-size: 16px;
}

    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <a href="index.php">Manage Categories</a>
        <a href="manage_users.php">Manage Users</a>
        <a href="manage_blogs.php">Manage Blogs</a>
        <a href="admin_report.php">Statistics</a>
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

      
        <ul class="pagination">
            <li class="page-item <?php if ($page <= 1) { echo 'disabled'; } ?>">
                <a class="page-link" href="<?php if ($page > 1) { echo "?page=" . ($page - 1); } else { echo "#"; } ?>">
                    <span>&laquo;</span>
                </a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?php if ($i == $page) { echo 'active'; } ?>">
                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?php if ($page >= $totalPages) { echo 'disabled'; } ?>">
                <a class="page-link" href="<?php if ($page < $totalPages) { echo "?page=" . ($page + 1); } else { echo "#"; } ?>">
                    <span>&raquo;</span>
                </a>
            </li>
        </ul>
    </div>
</body>
</html>
