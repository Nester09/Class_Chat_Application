<?php

session_start();

require 'db.php';

header('Content-Type: application/json; charset=UTF-8');


/* =========================================================
   VERIFY USER SESSION
   ========================================================= */

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Your session has expired. Please log in again.'
    ]);
    exit();
}


/* =========================================================
   VERIFY REQUEST METHOD
   ========================================================= */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method.'
    ]);
    exit();
}


/* =========================================================
   READ JSON REQUEST
   ========================================================= */

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['action']) || $data['action'] !== 'delete_account') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action.'
    ]);
    exit();
}


$userId = $_SESSION['user_id'];


/* =========================================================
   GET CURRENT USER PROFILE
   ========================================================= */

$stmt = $pdo->prepare("
    SELECT profile_picture
    FROM users
    WHERE id = ?
");
$stmt->execute([$userId]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode([
        'success' => false,
        'message' => 'User account could not be found.'
    ]);
    exit();
}

$currentProfilePicture = $user['profile_picture'] ?? '';

$defaultProfile = 'profile/default_profile.jpg';
$deletedProfile = 'profile/deleted_user.jpg';


/* =========================================================
   BEGIN DATABASE TRANSACTION
   ========================================================= */

$pdo->beginTransaction();

try {

    /* =====================================================
       1. HANDLE GROUP ADMIN TRANSFERS AND EMPTY GROUPS
       ===================================================== */

    $stmt = $pdo->prepare("
        SELECT group_id, role
        FROM group_users
        WHERE user_id = ?
    ");

    $stmt->execute([$userId]);

    $userGroups = $stmt->fetchAll(PDO::FETCH_ASSOC);


    foreach ($userGroups as $group) {

        if ($group['role'] === 'admin') {

            /* Find other members */
            $stmt = $pdo->prepare("
                SELECT user_id
                FROM group_users
                WHERE group_id = ?
                AND user_id != ?
            ");

            $stmt->execute([
                $group['group_id'],
                $userId
            ]);

            $otherMembers = $stmt->fetchAll(PDO::FETCH_ASSOC);


            if (count($otherMembers) > 0) {

                /* Promote first available member */
                $newAdminId = $otherMembers[0]['user_id'];

                $stmt = $pdo->prepare("
                    UPDATE group_users
                    SET role = 'admin'
                    WHERE group_id = ?
                    AND user_id = ?
                ");

                $stmt->execute([
                    $group['group_id'],
                    $newAdminId
                ]);

            } else {

                /* =========================================
                   NO OTHER MEMBERS - DELETE EMPTY GROUP
                   ========================================= */

                /* Delete group messages */
                $stmt = $pdo->prepare("
                    DELETE FROM messages
                    WHERE group_id = ?
                ");

                $stmt->execute([$group['group_id']]);


                /* Delete cleared-message records */
                $stmt = $pdo->prepare("
                    DELETE FROM user_cleared_messages
                    WHERE group_id = ?
                ");

                $stmt->execute([$group['group_id']]);


                /* Remove group membership */
                $stmt = $pdo->prepare("
                    DELETE FROM group_users
                    WHERE group_id = ?
                ");

                $stmt->execute([$group['group_id']]);


                /* Delete the group */
                $stmt = $pdo->prepare("
                    DELETE FROM groups
                    WHERE id = ?
                ");

                $stmt->execute([$group['group_id']]);


                continue;
            }
        }
    }


    /* =====================================================
       2. REMOVE USER FROM REMAINING GROUPS
       ===================================================== */

    $stmt = $pdo->prepare("
        DELETE FROM group_users
        WHERE user_id = ?
    ");

    $stmt->execute([$userId]);


    /* =====================================================
       3. DELETE MESSAGE REACTIONS
       ===================================================== */

    $stmt = $pdo->prepare("
        DELETE FROM message_reactions
        WHERE user_id = ?
    ");

    $stmt->execute([$userId]);


    /* =====================================================
       4. DELETE USER CLEARED-MESSAGE RECORDS
       ===================================================== */

    $stmt = $pdo->prepare("
        DELETE FROM user_cleared_messages
        WHERE user_id = ?
    ");

    $stmt->execute([$userId]);


    /* =====================================================
       5. DELETE PASSWORD RESET TOKENS
       ===================================================== */

    $stmt = $pdo->prepare("
        DELETE FROM password_resets
        WHERE user_id = ?
    ");

    $stmt->execute([$userId]);


    /* =====================================================
       6. ANONYMIZE USER ACCOUNT
       ===================================================== */

    $anonymousUsername =
        'DeletedUser_' . bin2hex(random_bytes(8));

    $anonymousEmail =
        'deleted_' . bin2hex(random_bytes(8)) . '@deleted.local';


    $stmt = $pdo->prepare("
        UPDATE users
        SET
            username = ?,
            email = ?,
            password = '',
            about = 'This account has been deleted.',
            profile_picture = ?,
            last_activity = NULL,
            is_online = 0,
            is_typing = 0,
            typing_to = NULL
        WHERE id = ?
    ");

    $stmt->execute([
        $anonymousUsername,
        $anonymousEmail,
        $deletedProfile,
        $userId
    ]);


    /* =====================================================
       7. COMMIT DATABASE CHANGES
       ===================================================== */

    $pdo->commit();


    /* =====================================================
       8. DELETE OLD CUSTOM PROFILE PICTURE
       ===================================================== */

    /*
     * Only delete a profile image if:
     *
     * - It exists
     * - It is not the default profile image
     * - It is not the deleted-user image
     * - It belongs to the uploads directory
     */

    if (
        !empty($currentProfilePicture) &&
        $currentProfilePicture !== $defaultProfile &&
        $currentProfilePicture !== $deletedProfile &&
        strpos(
            str_replace('\\', '/', $currentProfilePicture),
            'uploads/'
        ) === 0 &&
        file_exists($currentProfilePicture)
    ) {
        unlink($currentProfilePicture);
    }


    /* =====================================================
       9. DESTROY USER SESSION
       ===================================================== */

    session_unset();
    session_destroy();


    /* =====================================================
       10. CLEAR SESSION COOKIE
       ===================================================== */

    if (ini_get("session.use_cookies")) {

        $params = session_get_cookie_params();

        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }


    /* =====================================================
       SUCCESS RESPONSE
       ===================================================== */

    echo json_encode([
        'success' => true,
        'message' => 'Account deleted successfully. Your account has been anonymized.'
    ]);

    exit();


} catch (Throwable $e) {

    /* Roll back database changes if possible */
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }


    /* Log the actual error */
    error_log(
        'Account deletion error for user ' .
        $userId .
        ': ' .
        $e->getMessage()
    );


    /* Return safe message to browser */
    echo json_encode([
        'success' => false,
        'message' => 'Failed to delete your account. Please try again.'
    ]);

    exit();
}
?>