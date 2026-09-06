<?php
session_start();

if (isset($_GET['group_id'])) {
    $_SESSION['group_id'] = $_GET['group_id'];
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'No group ID provided']);
}
?>