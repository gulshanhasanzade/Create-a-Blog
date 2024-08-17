<?php
include "../navbar.php";
include "../head.php"; 
require '../helper/helper.php';
require "../config/database.php";
use PHPMailer\PHPMailer\PHPMailer;
require "../vendor/PHPMailer/src/Exception.php";
require "../vendor/PHPMailer/src/PHPMailer.php";
require "../vendor/PHPMailer/src/SMTP.php";
$errors = [];


try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $errors = validation(['name', 'surname', 'email', 'password', 'password-confirmation', 'profile', 'role', 'gender']);
        
        if (isset($_FILES['profile']) && $_FILES['profile']['name'] != null) {
            $newFileName = fileUpload('../public/', $_FILES['profile']);
            if ($newFileName === false) {
                $errors['profile'] = 'Failed to upload profile picture';
            }
        }

        if (!empty($errors)) {
            if (post('password') !== post('password-confirmation')) {
                $errors['password-confirmation'] = 'Password does not match';
            } else {
                $otp = rand(1000, 9999);
                $passwordHash = password_hash(post('password'), PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (name, email, password, role, gender, profile ,otp) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $insertQuery = $connection->prepare($sql);
                $check = $insertQuery->execute([
                    post("name"),
                    post("email"),
                    $passwordHash,
                    post('role'),
                    post('gender'),
                    $newFileName ?? null,
                    $otp
                ]);
                session_start();
                $_SESSION['user_role'] = post('role');
                $_SESSION['user_profile'] = post('profile');
                if ($check) {
                    $_SESSION['otp_email'] = post('email');
                    $_SESSION['otp'] = $otp;
                    $_SESSION['otp_ttl'] = time() + 300;
                    $email=new PHPMailer(true);

                    try{
                        $email->isSMTP();
                        $email->Host ='smtp.gmail.com';
                        $email->SMTPAuth = true;
                        $email->Username = 'hasanzade678@gmail.com';
                        $email->Password = 'byvq pkaq depo skiw';
                        $email->SMTPSecure = 'tls';
                        $email->Port = 587;

//                         MAIL_DRIVER=smtp
// MAIL_HOST=smtp.gmail.com
// MAIL_PORT=587
// MAIL_ENCRYPTION=tls
// MAIL_USERNAME=gismathusein@gmail.com
// MAIL_PASSWORD="byvq pkag depo skiw"
// MAIL_FROM_ADDRESS=gismathusein@gmail.com
// MAIL_FROM_NAME=Gismat


                        $email->setFrom('hasanzade678@gmail.com','projectBack');
                        $email->addAddress('hasanzade678@gmail.com');

                        $email->isHTML(true);
                        $email->Subject = 'Coders caravan project OTP code:';
                        $email->Body='Your OTP code'. $otp;

                    }
                    catch(Exception $e){
                        echo $e->getMessage();
                    }
                    view(route("auth/otp.php"));
                    exit();
                }
            }
        }
    }
} catch (PDOException $e) {
    if ($e->errorInfo[1] == 1062) {
        $errors['email'] = "This email is already registered";
    } else {
        echo $e->getMessage();
    }
}

?>

<div class="container">
    <form action="" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate style="width: 100%; max-width: 400px; margin: 0 auto;">
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control <?php echo isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?php echo post('name'); ?>" required />
            <?php if (isset($errors['name'])): ?>
                <div class="invalid-feedback"><?php echo $errors['name']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="surname" class="form-label">Surname</label>
            <input type="text" class="form-control <?php echo isset($errors['surname']) ? 'is-invalid' : ''; ?>" id="surname" name="surname" value="<?php echo post('surname'); ?>" required>
            <?php if (isset($errors['surname'])): ?>
                <div class="invalid-feedback"><?php echo $errors['surname']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>" id="email" name="email" value="<?php echo post('email'); ?>" required>
            <?php if (isset($errors['email'])): ?>
                <div class="invalid-feedback"><?php echo $errors['email']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control <?php echo isset($errors['password']) ? 'is-invalid' : ''; ?>" id="password" name="password" required>
            <?php if (isset($errors['password'])): ?>
                <div class="invalid-feedback"><?php echo $errors['password']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="password-confirmation" class="form-label">Password Confirmation</label>
            <input type="password" class="form-control <?php echo isset($errors['password-confirmation']) ? 'is-invalid' : ''; ?>" id="password-confirmation" name="password-confirmation" required>
            <?php if (isset($errors['password-confirmation'])): ?>
                <div class="invalid-feedback"><?php echo $errors['password-confirmation']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="profile" class="form-label">Profile Picture:</label>
            <input type="file" class="form-control <?php echo isset($errors['profile']) ? 'is-invalid' : ''; ?>" id="profile" name="profile">
            <?php if (isset($errors['profile'])): ?>
                <div class="invalid-feedback"><?php echo $errors['profile']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="gender" class="form-label">Gender</label>
            <select id="gender" name="gender" class="form-select <?php echo isset($errors['gender']) ? 'is-invalid' : ''; ?>" required>
                <option value="1" <?php echo post('gender') == '1' ? 'selected' : ''; ?>>Male</option>
                <option value="2" <?php echo post('gender') == '2' ? 'selected' : ''; ?>>Female</option>
            </select>
            <?php if (isset($errors['gender'])): ?>
                <div class="invalid-feedback"><?php echo $errors['gender']; ?></div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select id="role" name="role" class="form-select <?php echo isset($errors['role']) ? 'is-invalid' : ''; ?>" required>
                <option value="1" <?php echo post('role') == '1' ? 'selected' : ''; ?>>Admin</option>
                <option value="0" <?php echo post('role') == '0' ? 'selected' : ''; ?>>Client</option>
            </select>
            <?php if (isset($errors['role'])): ?>
                <div class="invalid-feedback"><?php echo $errors['role']; ?></div>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary w-100">Submit</button>
    </form>
</div>

<?php include '../footer.php'; ?>
