<?php

session_start();

include 'db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $group_id = $_POST['group_id'];
    $user_id = $_SESSION['user_id'];

    //// Fetch messages for the logged-in user in the specified group
    $stmt = $pdo->prepare("
        SELECT m.*, u.username, u.profile_picture 
        FROM messages m 
        JOIN users u ON m.user_id = u.id 
        WHERE m.group_id = ? AND m.user_id = ? 
        ORDER BY m.timestamp ASC
    ");
    $stmt->execute([$group_id, $user_id]);
    $messages = $stmt->fetchAll();

    if ($messages) {
        $currentDate = '';
        foreach ($messages as $message) {
            $formattedDate = date('F j, Y', strtotime($message['timestamp']));
            $formattedTime = date('g:i A', strtotime($message['timestamp']));

            if ($currentDate !== $formattedDate) {
                echo '<div class="message-date"><strong>' . htmlspecialchars($formattedDate) . '</strong></div>';
                $currentDate = $formattedDate;
            }
            echo '<div class="message-bubble sent">
                    <img src="' . htmlspecialchars($message['profile_picture']) . '" alt="Profile Picture">
                    <span class="sender">You(' . htmlspecialchars($message['username']) . ')</span>
                    <div>' . nl2br(htmlspecialchars($message['content'])) . '</div>
                    <small class="text-muted">' . $formattedTime . '</small>
                  </div>';
        }
    } else {
        echo '<p>No message(s) found for you in this group.</p>';
    }
}
?>
