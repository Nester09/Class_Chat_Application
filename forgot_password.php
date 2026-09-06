<?php
session_start();

require_once 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    //// Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['message'] = "Invalid email format.";
        $_SESSION['message_type'] = 'error';
        header("Location: forgot_password.php"); 
        exit;
    }

    //// Check if the email exists in the database
    $stmt = $pdo->prepare("SELECT id, username FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user) {
        //// Generate a reset token
        $token = bin2hex(random_bytes(32)); // 64 character token
        $expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // 1 hour expiration

        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, token_expiry) VALUES (?, ?, ?)");
        $stmt->execute([$user['id'], $token, $expiry]);

        //// Store success message in session
        $_SESSION['message'] = "A password reset token has been sent to your email. It will expire in one hour.";
        $_SESSION['message_type'] = 'success';

        header("Location: reset_password.php?token=$token");
        exit;
    } else {
        $_SESSION['message'] = "Email not found in our system.";
        $_SESSION['message_type'] = 'error';
        header("Location: forgot_password.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="plugins/bootstrap-4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="plugins/Font-Awesome-6.4.0/css/all.css">
    <link rel="stylesheet" href="styles/reset_forgot_password.css">
    <style>
        .alert {
            text-align: center;
            color: #721c24;
            font-size: 14px;
            background-color: #f8d7da;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Forgot Password</h2>
        <form action="forgot_password.php" method="POST">
            <div class="form-group">
                <i class="fas fa-envelope"></i>
                <input type="email" class="form-control" id="email" name="email" required placeholder="Your email address">
            </div>
            <button type="submit" class="btn-primary"><i class="fas fa-paper-plane"></i> Reset Password</button>
        </form>
        <br>
        <p class="text-center">Back to <a href="login.php">Login</a></p>

        <?php
        if (isset($_SESSION['message'])) {
            $message = $_SESSION['message'];
            $message_type = $_SESSION['message_type']; 

            echo "<div class='alert alert-$message_type'>$message</div>";
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        }
        ?>
    </div>
</body>
</html>