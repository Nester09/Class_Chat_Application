<?php
session_start();

require 'db.php';

$userId = $_SESSION['user_id'];

//// Check for new messages not sent by the current user in the last minute
$stmt = $pdo->prepare("SELECT DISTINCT group_id FROM messages WHERE user_id != ? AND created_at > NOW() - INTERVAL 1 MINUTE");
$stmt->execute([$userId]);
$newMessagesGroups = $stmt->fetchAll(PDO::FETCH_COLUMN);

if ($newMessagesGroups) {
    //// Fetch group names for notification
    $groupNamesStmt = $pdo->prepare("SELECT name FROM groups WHERE id IN (" . implode(',', array_fill(0, count($newMessagesGroups), '?')) . ")");
    $groupNamesStmt->execute($newMessagesGroups);
    
    $groupNames = $groupNamesStmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo json_encode(['new_messages' => true, 'group_name' => implode(', ', $groupNames)]);
} else {
    echo json_encode(['new_messages' => false]);
}
?>