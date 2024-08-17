<?php
session_start();  
require '../config/database.php';  
require '../helper/helper.php';  
require '../head.php';  
require '../navbar.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] == "POST") {

    // E-poçt və parol yoxlanılır
    $email = post('email');
    $password = post('password');

    // Xətaların yoxlanılması
    if (empty($email)) {
        $errors['email'] = "Email is required";
    }
    if (empty($password)) {
        $errors['password'] = "Password is required";
    }

    if (empty($errors)) {
        $query = "SELECT * FROM users WHERE email=?";
        $loginQuery = $connection->prepare($query);
        $loginQuery->execute([$email]);
        $user = $loginQuery->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            if ($user['otp'] != null) {
                $otp = rand(1000, 9999);
                $_SESSION['otp_email'] = $email;
                $_SESSION['otp'] = $otp;
                $_SESSION['otp_ttl'] = time() + 300; 
                header('Location: otp.php');
                exit();
            } elseif (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];  
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_profile'] = $user['profile'];
                           
                if ($user['role'] == 1) {
                    header('Location: http://localhost/projectBack/admin/index.php');
                exit();

                } else {
                    
                    header('Location:  http://localhost/projectBack/client/profile.php');
                exit();

                }
            } else {
                $errors['login'] = "Email or password is incorrect";
            }
        } else {
            $errors['login'] = "Email or password is incorrect";
        }
    }
}
?>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card" style="width: 100%; max-width: 400px;">
        <div class="card-body">
            <h2 class="text-center mb-4">Login Form</h2>
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" name="email" id="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
                    <?php if (isset($errors['email'])): ?>
                        <div class='invalid-feedback'><?php echo $errors['email']; ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" name="password" id="password" required>
                    <?php if (isset($errors['password'])): ?>
                        <div class='invalid-feedback'><?php echo $errors['password']; ?></div>
                    <?php endif; ?>
                    <?php if (isset($errors['login'])): ?>
                        <div class='invalid-feedback'><?php echo $errors['login']; ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>
