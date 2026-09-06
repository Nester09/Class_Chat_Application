<?php

session_start();

require 'db.php';

/*
|--------------------------------------------------------------------------
| Basic session validation
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = "Your session has expired. Please log in again.";
    header("Location: login.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| Redirect helper
|--------------------------------------------------------------------------
*/

function redirectBack()
{
    $referrer = $_SESSION['referrer'] ?? 'chat.php';

    /*
     * Prevent accidental redirects to an external website.
     */
    if (
        !preg_match(
            '/^(chat\.php|chat\.php\?.*)$/',
            basename(parse_url($referrer, PHP_URL_PATH)) .
            (parse_url($referrer, PHP_URL_QUERY) ? '?' . parse_url($referrer, PHP_URL_QUERY) : '')
        )
    ) {
        $referrer = 'chat.php';
    }

    header("Location: " . $referrer);
    exit();
}


/*
|--------------------------------------------------------------------------
| Store referrer
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION['referrer'])) {
    $_SESSION['referrer'] = $_SERVER['HTTP_REFERER'] ?? 'chat.php';
}


/*
|--------------------------------------------------------------------------
| Get group ID
|--------------------------------------------------------------------------
*/

$group_id = null;

if (isset($_POST['group_id'])) {
    $group_id = (int) $_POST['group_id'];
} elseif (isset($_GET['group_id'])) {
    $group_id = (int) $_GET['group_id'];
} elseif (isset($_SESSION['group_id'])) {
    $group_id = (int) $_SESSION['group_id'];
}

if ($group_id <= 0) {
    $_SESSION['error'] = "Group ID is missing.";
    redirectBack();
}

$_SESSION['group_id'] = $group_id;

$user_id = (int) $_SESSION['user_id'];


/*
|--------------------------------------------------------------------------
| Verify that the logged-in user belongs to this group
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT role
    FROM group_users
    WHERE user_id = ?
      AND group_id = ?
    LIMIT 1
");

$stmt->execute([
    $user_id,
    $group_id
]);

$userRole = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$userRole) {
    $_SESSION['error'] = "You are not a member of this group.";
    redirectBack();
}


/*
|--------------------------------------------------------------------------
| IMPORTANT:
|
| Your database only has:
|   user
|   admin
|
| There is NO creator role.
|
| Therefore an admin is allowed to manage the group.
|--------------------------------------------------------------------------
*/

$role = $userRole['role'];

$isAdmin = ($role === 'admin');


/*
|--------------------------------------------------------------------------
| Only POST requests perform management operations
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirectBack();
}


/*
|--------------------------------------------------------------------------
| UPDATE GROUP NAME
|--------------------------------------------------------------------------
*/

if (isset($_POST['update_group_name'])) {

    if (!$isAdmin) {
        $_SESSION['error'] = "Only a group admin can update the group name.";
        redirectBack();
    }

    $new_group_name = trim($_POST['new_group_name'] ?? '');

    /*
     * Validate group name.
     */
    if ($new_group_name === '') {
        $_SESSION['error'] = "Group name cannot be empty.";
        redirectBack();
    }

    if (mb_strlen($new_group_name) > 255) {
        $_SESSION['error'] = "Group name cannot exceed 255 characters.";
        redirectBack();
    }

    /*
     * Confirm that the group still exists.
     */
    $stmt = $pdo->prepare("
        SELECT id
        FROM groups
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$group_id]);

    if (!$stmt->fetch()) {
        $_SESSION['error'] = "The group no longer exists.";
        redirectBack();
    }

    /*
     * Update group name.
     */
    $stmt = $pdo->prepare("
        UPDATE groups
        SET name = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $new_group_name,
        $group_id
    ]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "Group name updated successfully.";
    } else {
        /*
         * rowCount() can be 0 if the new name is identical
         * to the existing name.
         */
        $_SESSION['success'] = "Group name saved successfully.";
    }

    redirectBack();
}


/*
|--------------------------------------------------------------------------
| REMOVE MEMBER
|--------------------------------------------------------------------------
*/

if (isset($_POST['remove_member'])) {

    if (!$isAdmin) {
        $_SESSION['error'] = "Only a group admin can remove members.";
        redirectBack();
    }

    $member_id = (int) ($_POST['member_id'] ?? 0);

    if ($member_id <= 0) {
        $_SESSION['error'] = "Invalid member selected.";
        redirectBack();
    }

    /*
     * Admin cannot remove themselves using this function.
     */
    if ($member_id === $user_id) {
        $_SESSION['error'] = "You cannot remove yourself from the group.";
        redirectBack();
    }

    /*
     * Check that target user is actually a member.
     */
    $stmt = $pdo->prepare("
        SELECT role
        FROM group_users
        WHERE group_id = ?
          AND user_id = ?
        LIMIT 1
    ");

    $stmt->execute([
        $group_id,
        $member_id
    ]);

    $member = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$member) {
        $_SESSION['error'] = "The selected user is not a member of this group.";
        redirectBack();
    }

    /*
     * Remove member.
     */
    $stmt = $pdo->prepare("
        DELETE FROM group_users
        WHERE group_id = ?
          AND user_id = ?
    ");

    $stmt->execute([
        $group_id,
        $member_id
    ]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success'] = "Member removed successfully.";
    } else {
        $_SESSION['error'] = "The member could not be removed.";
    }

    redirectBack();
}


/*
|--------------------------------------------------------------------------
| ADD MEMBER
|--------------------------------------------------------------------------
*/

if (isset($_POST['add_member'])) {

    if (!$isAdmin) {
        $_SESSION['error'] = "Only a group admin can add members.";
        redirectBack();
    }

    $new_member_email = trim($_POST['new_member_email'] ?? '');

    if ($new_member_email === '') {
        $_SESSION['error'] = "Please enter the user's email address.";
        redirectBack();
    }

    if (!filter_var($new_member_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "Please enter a valid email address.";
        redirectBack();
    }

    /*
     * Find user.
     */
    $stmt = $pdo->prepare("
        SELECT id, username
        FROM users
        WHERE email = ?
        LIMIT 1
    ");

    $stmt->execute([$new_member_email]);

    $newMember = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$newMember) {
        $_SESSION['error'] = "No user was found with that email address.";
        redirectBack();
    }

    $new_member_id = (int) $newMember['id'];

    /*
     * Do not allow adding the current user.
     */
    if ($new_member_id === $user_id) {
        $_SESSION['error'] = "You are already a member of this group.";
        redirectBack();
    }

    /*
     * Check existing membership.
     */
    $stmt = $pdo->prepare("
        SELECT 1
        FROM group_users
        WHERE group_id = ?
          AND user_id = ?
        LIMIT 1
    ");

    $stmt->execute([
        $group_id,
        $new_member_id
    ]);

    if ($stmt->fetch()) {
        $_SESSION['error'] = "User is already a member of this group.";
        redirectBack();
    }

    /*
     * Add member as ordinary user.
     */
    $stmt = $pdo->prepare("
        INSERT INTO group_users
            (group_id, user_id, role)
        VALUES
            (?, ?, 'user')
    ");

    $stmt->execute([
        $group_id,
        $new_member_id
    ]);

    $_SESSION['success'] =
        htmlspecialchars($newMember['username'], ENT_QUOTES, 'UTF-8') .
        " was added to the group.";

    redirectBack();
}


/*
|--------------------------------------------------------------------------
| DELETE GROUP
|--------------------------------------------------------------------------
*/

if (isset($_POST['delete_group'])) {

    if (!$isAdmin) {
        $_SESSION['error'] = "Only a group admin can delete the group.";
        redirectBack();
    }

    /*
     * Confirm that the group still exists.
     */
    $stmt = $pdo->prepare("
        SELECT id
        FROM groups
        WHERE id = ?
        LIMIT 1
    ");

    $stmt->execute([$group_id]);

    if (!$stmt->fetch()) {
        $_SESSION['error'] = "The group no longer exists.";
        redirectBack();
    }

    /*
     * Delete everything belonging specifically to this group.
     */
    try {

        $pdo->beginTransaction();


        /*
         * 1. Delete per-user cleared-message records.
         */
        $stmt = $pdo->prepare("
            DELETE FROM user_cleared_messages
            WHERE group_id = ?
        ");

        $stmt->execute([$group_id]);


        /*
         * 2. Delete reactions belonging to messages
         *    in this group.
         *
         * Your current schema does not have a direct
         * group_id in message_reactions, so we use
         * the related message IDs.
         */
        $stmt = $pdo->prepare("
            DELETE mr
            FROM message_reactions mr
            INNER JOIN messages m
                ON mr.message_id = m.id
            WHERE m.group_id = ?
        ");

        $stmt->execute([$group_id]);


        /*
         * 3. Delete group messages.
         */
        $stmt = $pdo->prepare("
            DELETE FROM messages
            WHERE group_id = ?
        ");

        $stmt->execute([$group_id]);


        /*
         * 4. Delete group memberships.
         */
        $stmt = $pdo->prepare("
            DELETE FROM group_users
            WHERE group_id = ?
        ");

        $stmt->execute([$group_id]);


        /*
         * 5. Finally delete the group.
         */
        $stmt = $pdo->prepare("
            DELETE FROM groups
            WHERE id = ?
        ");

        $stmt->execute([$group_id]);


        /*
         * Commit everything.
         */
        $pdo->commit();


        /*
         * Clear group from session if this was
         * the currently selected group.
         */
        if (
            isset($_SESSION['group_id']) &&
            (int) $_SESSION['group_id'] === $group_id
        ) {
            unset($_SESSION['group_id']);
        }


        $_SESSION['success'] = "Group deleted successfully.";

    } catch (Throwable $e) {

        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log(
            "Group deletion error for group " .
            $group_id .
            " by user " .
            $user_id .
            ": " .
            $e->getMessage()
        );

        $_SESSION['error'] =
            "The group could not be deleted. Please try again.";
    }

    redirectBack();
}


/*
|--------------------------------------------------------------------------
| No recognized action
|--------------------------------------------------------------------------
*/

$_SESSION['error'] = "No valid group management action was requested.";
redirectBack();

?>