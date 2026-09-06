<?php
require 'db.php';

session_start();

define('MAX_LOGIN_ATTEMPTS', 5);
define('LOCKOUT_TIME', 15 * 60); // 15 minutes

$email = '';
$password = '';
$errorMessage = '';

if (isset($_SESSION['login_attempts']) && isset($_SESSION['lockout_time'])) {
    if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS && time() < $_SESSION['lockout_time']) {
        $errorMessage = "Your account is locked. Please try again later.";
    } else {
        if (time() >= $_SESSION['lockout_time']) {
            unset($_SESSION['login_attempts']);
            unset($_SESSION['lockout_time']);
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && empty($errorMessage)) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        unset($_SESSION['login_attempts']);
        unset($_SESSION['lockout_time']);

        if (isset($_POST['save_password'])) {
            setcookie('email', $email, time() + (86400 * 30), "/");
            setcookie('password', $password, time() + (86400 * 30), "/");
        }

        $_SESSION['user_id'] = $user['id'];

        header("Location: chat.php");
        exit();
    } else {
        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }
        $_SESSION['login_attempts']++;

        if ($_SESSION['login_attempts'] >= MAX_LOGIN_ATTEMPTS) {
            $_SESSION['lockout_time'] = time() + LOCKOUT_TIME;
            $errorMessage = "Your account is locked due to too many failed login attempts. Please try again later.";
        } else {
            $errorMessage = "Invalid credentials. You have " . (MAX_LOGIN_ATTEMPTS - $_SESSION['login_attempts']) . " attempts left.";
        }

        $_SESSION['error_message'] = $errorMessage;
        header("Location: login.php");
        exit();
    }
}

if (isset($_SESSION['error_message'])) {
    $errorMessage = $_SESSION['error_message'];
    unset($_SESSION['error_message']);
}

if (isset($_COOKIE['email']) && isset($_COOKIE['password'])) {
    $email = $_COOKIE['email'];
    $password = $_COOKIE['password'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="plugins/bootstrap-4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="plugins/Font-Awesome-6.4.0/css/all.css">
    <link rel="stylesheet" href="styles/login_register.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Login</h2>
        <?php if (!empty($errorMessage)): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Email" required>
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" id="password" name="password" value="<?php echo htmlspecialchars($password); ?>" placeholder="Password" required>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="save_password" name="save_password">
                <label class="form-check-label" for="save_password">Save Password</label>
            </div>
            <button type="submit" class="btn-primary">
                <i class="fas fa-sign-in-alt"></i> Login
            </button>
            <button type="button" class="btn-primary mt-2" onclick="window.location.href='index.php'">
                <i class="fas fa-arrow-left"></i> Back To Home
            </button>
        </form>
        <br>
        <button type="button" class="btn btn-link text-primary" onclick="forgotPassword()">
            <i class="fas fa-question-circle"></i> Forgot Password
        </button>
        <hr>
        Don't have an account? <a href="register.php" class="text-#00bfff">Register here!</a>
    </div>

    <script>
        function forgotPassword() {
            window.location.href= "forgot_password.php";
        }
    </script>
</body>
</html>
