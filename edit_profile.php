<?php
require 'db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

$error_message = '';
$success_message = '';

/*======== DEFAULT PROFILE IMAGE PATH ========*/
$default_profile = "profile/default_profile.jpg";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = trim($_POST['username']);
    $about = trim($_POST['about']);
    $profile_picture = $_FILES['profile_picture'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (strlen($username) > 20) {
        $error_message = "Username cannot be longer than 20 characters.";
    } elseif (empty($username)) {
        $error_message = "Username cannot be empty.";
    } else {
        if ($username !== $user['username']) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = ? AND id != ?");
            $stmt->execute([$username, $_SESSION['user_id']]);
            $count = $stmt->fetchColumn();
            
            if ($count > 0) {
                $error_message = "Username '{$username}' is already taken. Please choose a different username.";
            }
        }
    }

    if (strlen($about) > 250) {
        $error_message = "About info cannot be longer than 250 characters.";
    }

    if (empty($error_message)) {
        $stmt = $pdo->prepare("UPDATE users SET username = ?, about = ? WHERE id = ?");
        $stmt->execute([$username, $about, $_SESSION['user_id']]);
    }

    if ($profile_picture['error'] == 0) {
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $max_size = 2 * 1024 * 1024; 
        $target_dir = "uploads/";
        
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($profile_picture["name"], PATHINFO_EXTENSION));
        $unique_filename = uniqid('profile_') . '.' . $file_extension;
        $target_file = $target_dir . $unique_filename;
        
        if (!in_array($file_extension, $allowed_types)) {
            $error_message = "Only JPG, JPEG, PNG & GIF files are allowed.";
        }
        elseif ($profile_picture["size"] > $max_size) {
            $error_message = "File size exceeds the 2MB limit.";
        } 
        elseif (move_uploaded_file($profile_picture["tmp_name"], $target_file)) {
            /*======== UPDATE PROFILE PICTURE SAFELY ========*/

            $old_profile_picture = $user['profile_picture'] ?? '';

            /* Update the database with the new profile picture first */
            $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE id = ?");
            $stmt->execute([$target_file, $_SESSION['user_id']]);


            /*======== DELETE ONLY OLD CUSTOM UPLOADED PROFILE ========*/

            /*
            * Never delete the default profile image.
            * Only delete files that are inside the uploads folder.
            */
            if (
                !empty($old_profile_picture) &&
                $old_profile_picture !== $default_profile &&
                strpos(str_replace('\\', '/', $old_profile_picture), 'uploads/') === 0 &&
                file_exists($old_profile_picture)
            ) {
                unlink($old_profile_picture);
            }
        } else {
            $error_message = "Error uploading the file.";
        }
    }

    if (!empty($password) || !empty($confirm_password)) {
        if (strlen($password) < 8) {
            $error_message = "Password must be at least 8 characters long.";
        } elseif ($password !== $confirm_password) {
            $error_message = "Passwords do not match.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $_SESSION['user_id']]);
        }
    }

    $previous_url = isset($_SESSION['previous_action']) ? $_SESSION['previous_action'] : 'chat.php';

    if (empty($error_message)) {
        $success_message = "Profile updated successfully!";
        
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch();
        
        unset($_SESSION['previous_action']);
        
        header("refresh:2;url=$previous_url");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="plugins/bootstrap-4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="plugins/Font-Awesome-6.4.0/css/all.css">
    <link rel="stylesheet" href="styles/edit_profile.css">
</head>
<body>
<div class="container">
    <h4><i class="fas fa-user-edit"></i> Edit Profile</h4>
    
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $error_message; ?>
        </div>
    <?php elseif (!empty($success_message)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <div class="profile-preview">
        <?php 
        /*======== DISPLAY USER PROFILE OR DEFAULT ========*/
        if (!empty($user['profile_picture']) && file_exists($user['profile_picture'])): ?>
            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="profile-image">
        <?php else: ?>
            <img src="<?php echo $default_profile; ?>" alt="Default Profile" class="profile-image">
        <?php endif; ?>
    </div>
    
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="username"><i class="fas fa-user"></i> Username:</label>
            <input type="text" class="form-control" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required maxlength="20">
            <div class="char-counter"><span id="username-count">0</span>/20 characters</div>
        </div>
        <div class="form-group">
            <label for="about"><i class="fas fa-info-circle"></i> About:</label>
            <textarea class="form-control" id="about" name="about" rows="4" maxlength="250"><?php echo htmlspecialchars($user['about']); ?></textarea>
            <div class="char-counter"><span id="about-count">0</span>/250 characters</div>
        </div>
        <div class="form-group">
            <label for="profile_picture"><i class="fas fa-image"></i> Profile Picture:</label>
            <input type="file" class="form-control-file" id="profile_picture" name="profile_picture">
            <small class="form-text text-muted">Upload a new profile picture (optional). Max size: 2MB, Allowed types: JPG, JPEG, PNG, GIF</small>
        </div>
        <div class="form-group">
            <label for="password"><i class="fas fa-lock"></i> New Password:</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
            <small class="form-text text-muted">Minimum 8 characters</small>
        </div>
        <div class="form-group">
            <label for="confirm_password"><i class="fas fa-lock"></i> Confirm New Password:</label>
            <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Leave blank to keep current password">
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Save Changes</button>
            <a href="#" onclick="goBack()" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
            <button type="button" class="btn btn-danger" onclick="confirmDeleteAccount()"><i class="fas fa-trash-alt"></i> Delete Account</button>
        </div>
    </form>
</div>

<script src="plugins/jquery-3.6.0/jquery.slim.js"></script>
<script src="plugins/popperjs/v2.9.2/popper.min.js"></script>
<script src="plugins/bootstrap-4.5.2/js/bootstrap.min.js"></script>
<script>
    function goBack() {
        window.history.back();
    }
    
    async function confirmDeleteAccount() {
        const confirmed = confirm(
            "Are you sure you want to delete your account?\n\n" +
            "Your account will be anonymized and you will be logged out. " +
            "This action cannot be undone."
        );

        if (!confirmed) {
            return;
        }

        try {
            const response = await fetch('delete_account.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    action: 'delete_account'
                })
            });

            const data = await response.json();

            if (data.success) {
                alert("Your account has been successfully deleted.");

                window.location.href = 'logout.php';
            } else {
                alert(
                    data.message ||
                    "There was an error deleting your account. Please try again."
                );
            }

        } catch (error) {
            console.error('Account deletion error:', error);

            alert(
                "An unexpected error occurred while deleting your account. " +
                "Please try again later."
            );
        }
    }
    
    /*======== CHARACTER COUNTER LOGIC ========*/
    document.addEventListener('DOMContentLoaded', function() {
        const usernameInput = document.getElementById('username');
        const usernameCount = document.getElementById('username-count');
        const aboutInput = document.getElementById('about');
        const aboutCount = document.getElementById('about-count');
        
        usernameCount.textContent = usernameInput.value.length;
        aboutCount.textContent = aboutInput.value.length;
        
        usernameInput.addEventListener('input', function() {
            usernameCount.textContent = this.value.length;
        });
        
        aboutInput.addEventListener('input', function() {
            aboutCount.textContent = this.value.length;
        });
        
        /*======== PASSWORD VALIDATION ========*/
        const passwordInput = document.getElementById('password');
        const confirmPasswordInput = document.getElementById('confirm_password');
        
        confirmPasswordInput.addEventListener('input', function() {
            if (passwordInput.value !== this.value) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        
        passwordInput.addEventListener('input', function() {
            if (confirmPasswordInput.value !== '') {
                if (confirmPasswordInput.value !== this.value) {
                    confirmPasswordInput.setCustomValidity('Passwords do not match');
                } else {
                    confirmPasswordInput.setCustomValidity('');
                }
            }
        });
    });
</script>
</body>
</html>