<?php
session_start();
include '../config/database.php'; 
include '../helper/helper.php';  
include '../head.php';  
include '../navbar2.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit();
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id=?";
$query = $connection->prepare($sql);
$query->execute([$user_id]);
$user = $query->fetch(PDO::FETCH_ASSOC);

$updateSuccess = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = post('name');
    $surname = post('surname');
    $dob = post('dob');
    $gender = post('gender');
    
    $sql = "UPDATE users SET name=?, surname=?, dob=?, gender=? WHERE id=?";
    $updateQuery = $connection->prepare($sql);
    $updateQuery->execute([$name, $surname, $dob, $gender, $user_id]);

    $updateSuccess = true; 
}
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <h2 class="text-center">Your Profile</h2>

            <?php if ($updateSuccess): ?>
                <div class="alert alert-success">
                    Your profile has been updated successfully.
                </div>
            <?php endif; ?>

            <form action="" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>">
                </div>
                <div class="mb-3">
                    <label for="surname" class="form-label">Surname</label>
                    <input type="text" class="form-control" id="surname" name="surname" value="<?php echo htmlspecialchars($user['surname']); ?>">
                </div>
                <div class="mb-3">
                    <label for="dob" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob']); ?>">
                </div>
                <div class="mb-3">
                    <label for="gender" class="form-label">Gender</label>
                    <select id="gender" name="gender" class="form-select">
                        <option value="1" <?php echo $user['gender'] == 1 ? 'selected' : ''; ?>>Male</option>
                        <option value="2" <?php echo $user['gender'] == 2 ? 'selected' : ''; ?>>Female</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary w-100">Update</button>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
