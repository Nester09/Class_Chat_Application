<?php
require 'db.php';

session_start();

$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if (empty($username) || empty($email) || empty($password)) {
        $errorMessage = "All fields are required.";
    } else {
        //// Check if user already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errorMessage = "Email already exists.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$username, $email, $password])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                header("Location: chat.php");
                exit();
            } else {
                $errorMessage = "Registration failed.";
            }
        }
    }
}

if (isset($_SESSION['error_message'])) {
    $errorMessage = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="plugins/bootstrap-4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="plugins/Font-Awesome-6.4.0/css/all.css">
    <link rel="stylesheet" href="styles/login_register.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Register</h2>
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <i class="fas fa-user"></i>
                <input type="text" class="form-control" name="username" placeholder="Username" required maxlength="20">
            </div>
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" class="form-control" name="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" name="password" placeholder="Password" required minlength="8">
            </div>
            <button type="submit" class="btn-primary">
                <i class="fas fa-user-plus"></i> Register
            </button>
            <button type="reset" class="btn-primary mt-2">
                <i class="fas fa-redo"></i> Reset
            </button>
            <p class="mt-3">Already have an account? <a href="login.php">Login here!</a></p>
            <hr>
            <p>
                <a href="#" data-toggle="modal" data-target="#licenseModal" class="text-primary">
                    <i class="fas fa-file-contract"></i> User License Agreement
                </a>
            </p>
        </form>
    </div>

    <!-- License Agreement Modal -->
    <div class="modal fade" id="licenseModal" tabindex="-1" role="dialog" aria-labelledby="licenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="licenseModalLabel"><i class="fas fa-file-contract"></i> User License Agreement</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        By creating an account and using NexyMessenger, you agree to the following terms and conditions:
                    </p>
                    <h6><strong>1. Acceptable Use</strong></h6>
                    <ul>
                        <li>You will not use this application for any illegal activities or purposes.</li>
                        <li>You will not harass, threaten, or harm other users.</li>
                        <li>You will not share offensive, discriminatory, or inappropriate content.</li>
                    </ul>
                    
                    <h6><strong>2. Privacy & Data</strong></h6>
                    <ul>
                        <li>You agree to respect other users' privacy and confidentiality.</li>
                        <li>You understand that messages may be stored on our servers.</li>
                        <li>You are responsible for maintaining the security of your account credentials.</li>
                    </ul>
                    
                    <h6><strong>3. Limitation of Liability</strong></h6>
                    <ul>
                        <li>The developers are not responsible for any data loss or service interruptions.</li>
                        <li>This service is provided "as is" without warranties of any kind.</li>
                        <li>Users are responsible for backing up important conversations.</li>
                    </ul>
                    
                    <h6><strong>4. Community Guidelines</strong></h6>
                    <ul>
                        <li>Users must adhere to respectful communication standards.</li>
                        <li>Spam, phishing attempts, and malicious links are strictly prohibited.</li>
                        <li>Violation of these terms may result in account suspension or termination.</li>
                    </ul>
                    
                    <p class="mt-3">
                        <small class="text-muted">Last updated: September 2025. By clicking "Register," you acknowledge that you have read and agree to these terms.</small>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="plugins/jquery-3.6.0/jquery.slim.js"></script>
    <script src="plugins/popperjs/v2.5.4/popper.min.js"></script>
    <script src="plugins/bootstrap-4.5.2/js/bootstrap.js"></script>
</body>
</html>