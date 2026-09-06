<?php
session_start();

require_once 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = trim($_POST['token']);
    $new_password = trim($_POST['new_password']);

    if (strlen($new_password) < 8) {
        $_SESSION['message'] = "Password must be at least 8 characters long.";
        $_SESSION['message_type'] = 'error';
        header("Location: reset_password.php?token=$token");
        exit;
    }

    //// Check if the token exists and is still valid
    $stmt = $pdo->prepare("SELECT pr.user_id, pr.token_expiry FROM password_resets pr 
                           JOIN users u ON pr.user_id = u.id 
                           WHERE pr.token = ?");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();

    if ($reset) {
        //// Check if the token is expired
        if (strtotime($reset['token_expiry']) < time()) {
            $_SESSION['message'] = "Token has expired.";
            $_SESSION['message_type'] = 'error';
            header("Location: reset_password.php?token=$token");
            exit;
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        //// Update the user's password
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $reset['user_id']]);

        //// Delete the token after successful reset
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = ?");
        $stmt->execute([$token]);

        $_SESSION['message'] = "Your password has been reset and updated successfully!";
        $_SESSION['message_type'] = 'success';

        header("Location: login.php");
        exit;
    } else {
        $_SESSION['message'] = "Invalid or expired token.";
        $_SESSION['message_type'] = 'error';
        header("Location: reset_password.php?token=$token");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="plugins/bootstrap-4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="plugins/Font-Awesome-6.4.0/css/all.css">
    <link rel="stylesheet" href="styles/reset_forgot_password.css">
</head>
<body>
    <div class="container">
        <h2>Reset Your Password</h2>
        <form action="reset_password.php" method="POST">
            <div class="form-group">
                <i class="fas fa-key"></i>
                <input type="text" class="form-control" id="token" name="token" required placeholder="Your reset token" value="<?php echo isset($_GET['token']) ? htmlspecialchars($_GET['token']) : ''; ?>">
            </div>
            <div class="form-group">
                <i class="fas fa-lock"></i>
                <input type="password" class="form-control" id="new_password" name="new_password" required placeholder="New password" minlength="8">
            </div>
            <button type="submit" class="btn-primary"><i class="fas fa-check-circle"></i> Reset Password</button>
        </form>

        <br>

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