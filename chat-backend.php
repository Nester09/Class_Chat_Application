<?php
require 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Fetch the current user's data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();
$userId = $_SESSION['user_id'];

// ===== FETCH USER GROUPS =====
// Fetch groups the user is a member of
$user_groups_stmt = $pdo->prepare("SELECT g.id, g.name, gu.role 
                                  FROM groups g 
                                  JOIN group_users gu ON g.id = gu.group_id 
                                  WHERE gu.user_id = ?");
$user_groups_stmt->execute([$userId]);
$user_groups_data = $user_groups_stmt->fetchAll(PDO::FETCH_ASSOC);

// Extract just the group IDs for simpler checks
$user_groups = array_column($user_groups_data, 'id');
$user_groups_count = count($user_groups_data);

// ===== FETCH AVAILABLE CONTACTS =====
if (empty($user_groups)) {
    $availableContacts = [];
} else {
    // Create placeholders for the IN clause
    $placeholders = implode(',', array_fill(0, count($user_groups), '?'));
    
    // Prepare the query with placeholders
    $contacts_stmt = $pdo->prepare("
        SELECT u.id, u.username, u.profile_picture
        FROM users u
        INNER JOIN group_users gu ON u.id = gu.user_id
        WHERE gu.group_id IN ($placeholders) AND u.id != ?
    ");
    
    // Combine group IDs and user ID for binding
    $params = array_merge($user_groups, [$userId]);
    
    // Execute the query
    $contacts_stmt->execute($params);
    $availableContacts = $contacts_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ensure uniqueness
    $availableContacts = array_map('unserialize', array_unique(array_map('serialize', $availableContacts)));
}

// ===== FETCH ALL GROUPS =====
$groups_stmt = $pdo->query("SELECT * FROM groups");
$all_groups = $groups_stmt->fetchAll(PDO::FETCH_ASSOC);

// Sort groups - user's groups first
$groups = [];
$user_member_groups = [];
$non_member_groups = [];

foreach ($all_groups as $group) {
    if (in_array($group['id'], $user_groups)) {
        $user_member_groups[] = $group;
    } else {
        $non_member_groups[] = $group;
    }
}

// Combine the arrays - user's groups first, then others
$groups = array_merge($user_member_groups, $non_member_groups);

// ===== HANDLE FORM SUBMISSIONS =====
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Store the previous action in a session variable
    if (!isset($_SESSION['previous_action'])) {
        $_SESSION['previous_action'] = $_SERVER['REQUEST_URI'];
    }

    // Create Group
    if (isset($_POST['create_group'])) {
        $group_name = trim($_POST['group_name']);
        
        if (strlen($group_name) > 50) {
            $_SESSION['error'] = 'Group name is too long! Maximum length is 50 characters.';
        } else {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM groups WHERE BINARY name = ?");
            $stmt->execute([$group_name]);
        
            if ($stmt->fetchColumn() > 0) {
                $_SESSION['error'] = 'Group name already exists! Please choose a different name.';
            } else {
                $stmt = $pdo->prepare("INSERT INTO groups (name) VALUES (?)");
                $stmt->execute([$group_name]);
                $group_id = $pdo->lastInsertId();
                
                $stmt = $pdo->prepare("INSERT INTO group_users (group_id, user_id, role) VALUES (?, ?, 'admin')");
                $stmt->execute([$group_id, $userId]);
                
                $_SESSION['success'] = 'Group created successfully!';
            }
        }
        
        header("Location: " . (isset($_SESSION['previous_action']) ? $_SESSION['previous_action'] : 'chat.php'));
        unset($_SESSION['previous_action']);
        exit();
    }

    // Join Group
    if (isset($_POST['join_group'])) {
        $group_id = $_POST['group_id'];
        if (!in_array($group_id, $user_groups)) {
            $stmt = $pdo->prepare("INSERT INTO group_users (group_id, user_id) VALUES (?, ?)");
            $stmt->execute([$group_id, $userId]);
            
            // Refresh the page to update user_groups
            header("Location: chat.php" . (isset($_GET['group_id']) ? "?group_id=" . $_GET['group_id'] : ""));
            exit();
        }
    }

    // Leave Group
    if (isset($_POST['leave_group_button'])) {
        $group_id = $_POST['group_id'];
        $stmt = $pdo->prepare("SELECT * FROM group_users WHERE group_id = ? AND user_id = ?");
        $stmt->execute([$group_id, $userId]);
        $user_in_group = $stmt->fetch();

        if ($user_in_group) {
            $stmt = $pdo->prepare("DELETE FROM group_users WHERE group_id = ? AND user_id = ?");
            if ($stmt->execute([$group_id, $userId])) {
                $_SESSION['leave_group_success'] = "You have successfully left the group!";
            } else {
                $_SESSION['leave_group_error'] = "An error occurred. Please try again.";
            }
        } else {
            $_SESSION['leave_group_error'] = "You are not a member of this group!";
        }

        header("Location: " . $_SESSION['previous_action']);
        unset($_SESSION['previous_action']);
        exit();
    }

    // Send Message
    if (isset($_POST['send_message'])) {
        $group_id = $_POST['group_id'];
        $message_content = $_POST['message_content'];
        $file_path = null;

        // Check if user is a member of the group
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM group_users WHERE group_id = ? AND user_id = ?");
        $stmt->execute([$group_id, $userId]);
        $is_member = $stmt->fetchColumn() > 0;

        if (!$is_member) {
            $_SESSION['message'] = "Error: You must join this group to send messages.";
            $_SESSION['message_type'] = 'error';
            header("Location: chat.php?group_id=" . $group_id);
            exit();
        }

        // Handle file upload
        if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
            $maxFileSize = 100 * 1024 * 1024; // 100MB

            if ($_FILES["file"]["size"] > $maxFileSize) {
                $_SESSION['message'] = "Error: The file exceeds the maximum allowed size of 100MB.";
                $_SESSION['message_type'] = 'error';
                header("Location: chat.php?group_id=" . $group_id);
                exit();
            }

            $target_dir = "uploads/";
            $file_path = $target_dir . basename($_FILES["file"]["name"]);

            if (!move_uploaded_file($_FILES["file"]["tmp_name"], $file_path)) {
                $_SESSION['message'] = "Error: Failed to upload file.";
                $_SESSION['message_type'] = 'error';
                header("Location: chat.php?group_id=" . $group_id);
                exit();
            }
        } elseif ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE || $_FILES['file']['error'] == UPLOAD_ERR_FORM_SIZE) {
            $_SESSION['message'] = "Error: The uploaded file exceeds the maximum file size limit.";
            $_SESSION['message_type'] = 'error';
            header("Location: chat.php?group_id=" . $group_id);
            exit();
        }

        // Truncate message if it exceeds 1000 characters
        if (strlen($message_content) > 5000) {
            $message_content = substr($message_content, 0, 5000);
        }

        // Insert message if content or file exists
        if (!empty($message_content) || $file_path) {
            try {
                $stmt = $pdo->prepare("INSERT INTO messages (group_id, user_id, content, file_path) VALUES (?, ?, ?, ?)");
                $stmt->execute([$group_id, $userId, $message_content, $file_path]);

                $_SESSION['message'] = "Message sent successfully!";
                $_SESSION['message_type'] = 'success';
            } catch (PDOException $e) {
                $_SESSION['message'] = "Error: Could not send the message. Please try again later.";
                $_SESSION['message_type'] = 'error';
            }
        }

        header("Location: chat.php?group_id=" . $group_id);
        exit();
    }

    // Edit Message
    if (isset($_POST['edit_message'])) {
        $message_id = $_POST['message_id'];
        $new_content = $_POST['new_content'];
        
        // Check if message is less than 12 hours old
        $stmt = $pdo->prepare("SELECT created_at FROM messages WHERE id = ? AND user_id = ?");
        $stmt->execute([$message_id, $userId]);
        $message_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($message_data) {
            $created_time = strtotime($message_data['created_at']);
            $current_time = time();
            $time_diff = $current_time - $created_time;
            
            if ($time_diff <= 43200) { // 12 hours in seconds
                // Truncate message if it exceeds 5000 characters
                if (strlen($new_content) > 5000) {
                    $new_content = substr($new_content, 0, 5000);
                }
                
                $stmt = $pdo->prepare("UPDATE messages SET content = ? WHERE id = ? AND user_id = ?");
                $stmt->execute([$new_content, $message_id, $userId]);
                $_SESSION['message'] = "Message updated successfully!";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Error: You can only edit messages within 12 hours of sending.";
                $_SESSION['message_type'] = 'error';
            }
        }
        
        header("Location: chat.php?group_id=" . $_GET['group_id']);
        exit();
    }

    // Delete Message
    if (isset($_POST['delete_message'])) {
        $message_id = $_POST['message_id'];
        
        // Check if message is less than 12 hours old
        $stmt = $pdo->prepare("SELECT created_at, file_path FROM messages WHERE id = ? AND user_id = ?");
        $stmt->execute([$message_id, $userId]);
        $message_data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($message_data) {
            $created_time = strtotime($message_data['created_at']);
            $current_time = time();
            $time_diff = $current_time - $created_time;
            
            if ($time_diff <= 43200) { // 12 hours in seconds
                // Delete the associated file if it exists
                if (!empty($message_data['file_path']) && file_exists($message_data['file_path'])) {
                    unlink($message_data['file_path']);
                }
                
                $stmt = $pdo->prepare("DELETE FROM messages WHERE id = ? AND user_id = ?");
                $stmt->execute([$message_id, $userId]);
                $_SESSION['message'] = "Message deleted successfully!";
                $_SESSION['message_type'] = 'success';
            } else {
                $_SESSION['message'] = "Error: You can only delete messages within 12 hours of sending.";
                $_SESSION['message_type'] = 'error';
            }
        }
        
        header("Location: chat.php?group_id=" . $_GET['group_id']);
        exit();
    }

    // Clear Chat
    if (isset($_POST['clear_chat'])) {
        $clearGroupId = $_POST['clear_group_id'];

        // Ensure the user is a member of the selected group
        if (in_array($clearGroupId, $user_groups)) {
            $pdo->beginTransaction();

            try {
                // Get all message IDs in the group
                $stmt = $pdo->prepare("SELECT id FROM messages WHERE group_id = ?");
                $stmt->execute([$clearGroupId]);
                $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($messages as $message) {
                    // Insert into user_cleared_messages table
                    $stmt = $pdo->prepare("INSERT INTO user_cleared_messages (user_id, message_id, group_id) VALUES (?, ?, ?)");
                    $stmt->execute([$userId, $message['id'], $clearGroupId]);
                }

                $pdo->commit();
                $_SESSION['success'] = "Chat cleared successfully on your side!";
            } catch (Exception $e) {
                $pdo->rollBack();
                $_SESSION['error'] = "Failed to clear chat.";
            }

            header("Location: " . $_SESSION['previous_action']);
            unset($_SESSION['previous_action']);
            exit();
        } else {
            $_SESSION['error'] = "You are not a member of this group.";
        }
    }
}

// ===== LOAD GROUP CHAT MESSAGES =====
$group_id = isset($_GET['group_id']) ? $_GET['group_id'] : null;
$messages = [];
$group_name = '';
$group_users = [];

if ($group_id) {
    // Fetch group details
    $stmt = $pdo->prepare("SELECT name FROM groups WHERE id = :group_id");
    $stmt->execute(['group_id' => $group_id]);
    $group = $stmt->fetch(PDO::FETCH_ASSOC);
    $group_name = $group ? $group['name'] : 'Unknown Group';

    // Check if user is a member of the group
    $is_member = in_array($group_id, $user_groups);

    if ($is_member) {
        // Get messages from the group, excluding those cleared by the user
        $messages_stmt = $pdo->prepare("
            SELECT messages.*, users.username, users.profile_picture
            FROM messages
            JOIN users ON messages.user_id = users.id
            LEFT JOIN user_cleared_messages AS ucm ON messages.id = ucm.message_id AND ucm.user_id = ?
            WHERE messages.group_id = ? AND ucm.message_id IS NULL
            ORDER BY messages.created_at
        ");
        $messages_stmt->execute([$userId, $group_id]);
        $messages_raw = $messages_stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group messages by date
        $grouped_messages = [];
        foreach ($messages_raw as $message) {
            $date_key = date('Y-m-d', strtotime($message['created_at']));
            $message['formatted_date'] = date('F j, Y', strtotime($message['created_at']));
            if (!isset($grouped_messages[$date_key])) {
                $grouped_messages[$date_key] = [];
            }
            $grouped_messages[$date_key][] = $message;
        }
        $messages = $grouped_messages;

        // Fetch users in the group along with their roles
        $stmt = $pdo->prepare("
            SELECT u.id, u.username, u.profile_picture, gu.role 
            FROM users u 
            JOIN group_users gu ON u.id = gu.user_id 
            WHERE gu.group_id = :group_id
        ");
        $stmt->execute(['group_id' => $group_id]);
        $group_users_unsorted = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Sort users: current user first, then admin, then others
        $current_user = [];
        $admin_users = [];
        $regular_users = [];
        
        foreach ($group_users_unsorted as $group_user) {
            if ($group_user['id'] == $userId) {
                $current_user[] = $group_user;
            } else if ($group_user['role'] == 'admin' || $group_user['role'] == 'creator') {
                $admin_users[] = $group_user;
            } else {
                $regular_users[] = $group_user;
            }
        }
        
        $group_users = array_merge($current_user, $admin_users, $regular_users);
    } else {
        $messages = ['prompt' => true]; // Indicate that user needs to join the group
    }
}

// Clear last message ID from session after fetching messages
if (isset($_SESSION['last_message_id'])) {
    unset($_SESSION['last_message_id']);
}
?>