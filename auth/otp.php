<?php
session_start(); // Start the session

include "../config/database.php"; // Include database connection
include "../helper/helper.php";   // Include helper functions like validation
// include "../index.php";

$errors = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate the OTP field
    $errors = validation(['otp']);

    if (count($errors) == 0) {
        $otp = post('otp');
        $comfirmation_otp = $_SESSION['otp'];
        $otp_ttl = $_SESSION['otp_ttl'];
        $user_email = $_SESSION['otp_email'];

        // Check if the OTP is still valid (within time limit)
        if (time() <= $otp_ttl) {
            // Verify OTP
            if ($otp == $comfirmation_otp) {
                // Update the user's record in the database to clear the OTP
                $sql = "UPDATE users SET otp=NULL WHERE email=?";
                $updateQuery = $connection->prepare($sql);
                $check = $updateQuery->execute([$user_email]);

                if ($check) {
                    // Clear session and redirect to login page
                    $_SESSION = [];
                    session_destroy(); // Destroy the session for security
                    header("Location: http://localhost/projectBack/auth/login.php");
                    exit();
                }
            } else {
                $errors['otp'] = "Invalid OTP. Please try again.";
            }
        } else {
            $errors['otp'] = "OTP has expired. Please request a new one.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OTP Form</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="card" style="width: 100%; max-width: 400px;">
            <div class="card-body">
                <h2 class="text-center mb-4">OTP Verification</h2>
                <form action="" method="post">
                    <div class="mb-3">
                        <label for="otp" class="form-label">Enter OTP code</label>
                        <input type="text" class="form-control <?php echo isset($errors['otp']) ? 'is-invalid' : ''; ?>" name="otp" id="otp" required>
                        <?php if (isset($errors['otp'])): ?>
                            <div class="invalid-feedback"><?php echo $errors['otp']; ?></div>
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Submit</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>

