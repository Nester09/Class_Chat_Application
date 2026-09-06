<?php
require 'db.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/*====== USER DATA RETRIEVAL ======*/
// Fetch the current user's data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Fetch the selected contact's data
$contact_id = isset($_GET['contact_id']) ? $_GET['contact_id'] : null;
if (!$contact_id) {
    header("Location: chat.php");
    exit();
}

$contact_stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$contact_stmt->execute([$contact_id]);
$contact = $contact_stmt->fetch();

/*====== MESSAGE RETRIEVAL ======*/
// Fetch private messages between the user and the contact
$messages_stmt = $pdo->prepare("
    SELECT pm.*, u.username, u.profile_picture 
    FROM private_messages pm
    JOIN users u ON pm.sender_id = u.id
    WHERE (pm.sender_id = ? AND pm.receiver_id = ?) 
    OR (pm.sender_id = ? AND pm.receiver_id = ?) 
    ORDER BY pm.created_at
");
$messages_stmt->execute([$_SESSION['user_id'], $contact_id, $contact_id, $_SESSION['user_id']]);
$messages = $messages_stmt->fetchAll();

// Mark unread messages as read
$mark_read_stmt = $pdo->prepare("
    UPDATE private_messages 
    SET is_read = 1 
    WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
");
$mark_read_stmt->execute([$contact_id, $_SESSION['user_id']]);

/*====== MESSAGE SENDING AND FILE UPLOADS ======*/
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'send_message') {
    $message_content = $_POST['message_content'];
    $file_paths = [];

    // Handle file uploads
    if (isset($_FILES['file']) && $_FILES['file']['error'][0] == 0) {
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        foreach ($_FILES['file']['name'] as $key => $name) {
            $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $new_filename = uniqid() . '.' . $file_extension;
            $file_path = $target_dir . $new_filename;
            
            if (move_uploaded_file($_FILES['file']['tmp_name'][$key], $file_path)) {
                $file_type = $_FILES['file']['type'][$key];
                $file_paths[] = [
                    'path' => $file_path,
                    'name' => $name,
                    'type' => $file_type
                ];
            }
        }
    }

    if (!empty($message_content) || !empty($file_paths)) {
        $reply_to = isset($_POST['reply_to']) ? $_POST['reply_to'] : null;
        $reply_to_name = isset($_POST['reply_to_name']) ? $_POST['reply_to_name'] : null;
        $reply_to_content = isset($_POST['reply_to_content']) ? $_POST['reply_to_content'] : null;
        
        $reply_data = null;
        if ($reply_to) {
            $reply_data = json_encode([
                'id' => $reply_to,
                'name' => $reply_to_name,
                'content' => $reply_to_content
            ]);
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO private_messages 
            (sender_id, receiver_id, content, file_path, created_at, is_read, reply_to) 
            VALUES (?, ?, ?, ?, NOW(), 0, ?)
        ");
        $stmt->execute([
            $_SESSION['user_id'], 
            $contact_id, 
            $message_content, 
            !empty($file_paths) ? json_encode($file_paths) : null,
            $reply_data
        ]);
        
        $message_id = $pdo->lastInsertId();
        
        // Return the new message for AJAX response
        if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
            $new_message = [
                'id' => $message_id,
                'sender_id' => $_SESSION['user_id'],
                'content' => $message_content,
                'file_path' => !empty($file_paths) ? json_encode($file_paths) : null,
                'created_at' => date('Y-m-d H:i:s'),
                'is_read' => 0,
                'username' => $user['username'],
                'profile_picture' => $user['profile_picture'],
                'reply_to' => $reply_data
            ];
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => $new_message]);
            exit;
        }
    }
}

/*====== MESSAGE DELETION ======*/
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'delete_message') {
    $message_id = $_POST['message_id'];
    
    // Verify the message belongs to the current user
    $check_stmt = $pdo->prepare("SELECT * FROM private_messages WHERE id = ? AND sender_id = ?");
    $check_stmt->execute([$message_id, $_SESSION['user_id']]);
    
    if ($check_stmt->rowCount() > 0) {
        $delete_stmt = $pdo->prepare("DELETE FROM private_messages WHERE id = ?");
        $delete_stmt->execute([$message_id]);
        
        if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
        }
    } else {
        if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'You can only delete your own messages']);
            exit;
        }
    }
}

/*====== MESSAGE REACTIONS ======*/
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'react_message') {
    $message_id = $_POST['message_id'];
    $reaction = $_POST['reaction'];
    
    // Check if reaction already exists
    $check_stmt = $pdo->prepare("
        SELECT * FROM message_reactions 
        WHERE message_id = ? AND user_id = ?
    ");
    $check_stmt->execute([$message_id, $_SESSION['user_id']]);
    
    if ($check_stmt->rowCount() > 0) {
        // Update existing reaction
        $update_stmt = $pdo->prepare("
            UPDATE message_reactions 
            SET reaction = ? 
            WHERE message_id = ? AND user_id = ?
        ");
        $update_stmt->execute([$reaction, $message_id, $_SESSION['user_id']]);
    } else {
        // Insert new reaction
        $insert_stmt = $pdo->prepare("
            INSERT INTO message_reactions 
            (message_id, user_id, reaction) 
            VALUES (?, ?, ?)
        ");
        $insert_stmt->execute([$message_id, $_SESSION['user_id'], $reaction]);
    }
    
    if (isset($_POST['ajax']) && $_POST['ajax'] == 1) {
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
}

/*====== NEW MESSAGES CHECK ======*/
if (isset($_GET['action']) && $_GET['action'] == 'check_new_messages') {
    $last_id = isset($_GET['last_id']) ? $_GET['last_id'] : 0;
    
    $new_messages_stmt = $pdo->prepare("
        SELECT pm.*, u.username, u.profile_picture 
        FROM private_messages pm
        JOIN users u ON pm.sender_id = u.id
        WHERE ((pm.sender_id = ? AND pm.receiver_id = ?) 
        OR (pm.sender_id = ? AND pm.receiver_id = ?))
        AND pm.id > ?
        ORDER BY pm.created_at
    ");
    $new_messages_stmt->execute([
        $_SESSION['user_id'], 
        $contact_id, 
        $contact_id, 
        $_SESSION['user_id'],
        $last_id
    ]);
    
    $new_messages = $new_messages_stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Mark messages as read
    if (!empty($new_messages)) {
        $mark_read_stmt = $pdo->prepare("
            UPDATE private_messages 
            SET is_read = 1 
            WHERE sender_id = ? AND receiver_id = ? AND is_read = 0
        ");
        $mark_read_stmt->execute([$contact_id, $_SESSION['user_id']]);
    }
    
    header('Content-Type: application/json');
    echo json_encode(['messages' => $new_messages]);
    exit;
}

/*====== TYPING STATUS ======*/
if (isset($_GET['action']) && $_GET['action'] == 'update_typing') {
    $is_typing = $_GET['is_typing'];
    
    // Update user's typing status
    $update_stmt = $pdo->prepare("
        UPDATE users 
        SET is_typing = ?, typing_to = ?, last_activity = NOW() 
        WHERE id = ?
    ");
    $update_stmt->execute([
        $is_typing, 
        $is_typing ? $contact_id : null, 
        $_SESSION['user_id']
    ]);
    
    // Check if contact is typing
    $check_stmt = $pdo->prepare("
        SELECT is_typing 
        FROM users 
        WHERE id = ? AND is_typing = 1 AND typing_to = ? 
        AND last_activity > DATE_SUB(NOW(), INTERVAL 10 SECOND)
    ");
    $check_stmt->execute([$contact_id, $_SESSION['user_id']]);
    
    $contact_typing = $check_stmt->rowCount() > 0;
    
    header('Content-Type: application/json');
    echo json_encode(['contact_typing' => $contact_typing]);
    exit;
}

/*====== MESSAGE REACTIONS RETRIEVAL ======*/
$reactions_stmt = $pdo->prepare("
    SELECT mr.*, u.username 
    FROM message_reactions mr
    JOIN users u ON mr.user_id = u.id
    WHERE mr.message_id IN (
        SELECT id FROM private_messages 
        WHERE (sender_id = ? AND receiver_id = ?) 
        OR (sender_id = ? AND receiver_id = ?)
    )
");
$reactions_stmt->execute([
    $_SESSION['user_id'], 
    $contact_id, 
    $contact_id, 
    $_SESSION['user_id']
]);

$reactions = [];
foreach ($reactions_stmt->fetchAll() as $reaction) {
    $reactions[$reaction['message_id']][] = [
        'user_id' => $reaction['user_id'],
        'username' => $reaction['username'],
        'reaction' => $reaction['reaction']
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat with <?php echo htmlspecialchars($contact['username']); ?></title>
    <link rel="stylesheet" href="plugins/bootstrap-4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="plugins/Font-Awesome-6.4.0/css/all.css">
    <link rel="stylesheet" href="plugins/emojionearea-3.4.1/emojionearea.css">
    <link rel="stylesheet" href="styles/private_chat.css">
</head>
<body>
    <div class="chat-container">
        <div class="chat-header">
            <button class="back-btn" onclick="window.location.href='chat.php'">
                <i class="fas fa-arrow-left"></i>
            </button>
            <img src="<?php echo !empty($contact['profile_picture']) ? htmlspecialchars($contact['profile_picture']) : 'assets/default-avatar.png'; ?>" alt="Profile">
            <div class="contact-info">
                <h4><?php echo htmlspecialchars($contact['username']); ?></h4>
                <div class="status" id="contact-status">
                    <?php echo $contact['is_online'] ? 'Online' : 'Offline'; ?>
                </div>
            </div>
            <div class="header-actions">
                <button id="search-toggle"><i class="fas fa-search"></i></button>
                <button id="call-btn"><i class="fas fa-phone"></i></button>
                <button id="video-btn"><i class="fas fa-video"></i></button>
                <button id="menu-btn"><i class="fas fa-ellipsis-v"></i></button>
            </div>
        </div>
        
        <div class="search-container" id="search-container">
            <input type="text" id="search-input" placeholder="Search in conversation...">
        </div>
        
        <div class="message-container" id="message-container">
            <?php
            $current_date = '';
            $last_sender = null;
            
            foreach ($messages as $index => $message):
                $message_date = date('Y-m-d', strtotime($message['created_at']));
                $is_sent = $message['sender_id'] == $_SESSION['user_id'];
                $show_date = $current_date != $message_date;
                $current_date = $message_date;
                
                // Check if we need to start a new message group
                $new_group = $last_sender !== $message['sender_id'];
                $last_sender = $message['sender_id'];
                
                if ($show_date):
            ?>
                <div class="message-date-divider">
                    <span>
                        <?php
                        $today = date('Y-m-d');
                        $yesterday = date('Y-m-d', strtotime('-1 day'));
                        
                        if ($message_date == $today) {
                            echo 'Today';
                        } elseif ($message_date == $yesterday) {
                            echo 'Yesterday';
                        } else {
                            echo date('F j, Y', strtotime($message['created_at']));
                        }
                        ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <?php if ($new_group): ?>
                <div class="message-group <?php echo $is_sent ? 'sent-group' : 'received-group'; ?>">
            <?php endif; ?>
            
                <div class="message-bubble <?php echo $is_sent ? 'sent' : 'received'; ?>" data-id="<?php echo $message['id']; ?>">
                    <?php if ($is_sent): ?>
                    <div class="message-actions">
                        <button class="reaction-btn" onclick="toggleReactionOptions(<?php echo $message['id']; ?>)">
                            <i class="far fa-smile"></i>
                        </button>
                        <button class="reply-btn" onclick="replyToMessage(<?php echo $message['id']; ?>, '<?php echo htmlspecialchars($message['username']); ?>', '<?php echo htmlspecialchars(str_replace("'", "\\'", str_replace("\n", " ", $message['content']))); ?>')">
                            <i class="fas fa-reply"></i>
                        </button>
                        <button class="delete-btn" onclick="deleteMessage(<?php echo $message['id']; ?>)">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </div>
                    <?php else: ?>
                    <div class="message-actions">
                        <button class="reaction-btn" onclick="toggleReactionOptions(<?php echo $message['id']; ?>)">
                            <i class="far fa-smile"></i>
                        </button>
                        <button class="reply-btn" onclick="replyToMessage(<?php echo $message['id']; ?>, '<?php echo htmlspecialchars($message['username']); ?>', '<?php echo htmlspecialchars(str_replace("'", "\\'", str_replace("\n", " ", $message['content']))); ?>')">
                            <i class="fas fa-reply"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                    
                    <div class="reaction-options" id="reaction-options-<?php echo $message['id']; ?>">
                        <span onclick="reactToMessage(<?php echo $message['id']; ?>, '👍')">👍</span>
                        <span onclick="reactToMessage(<?php echo $message['id']; ?>, '❤️')">❤️</span>
                        <span onclick="reactToMessage(<?php echo $message['id']; ?>, '😂')">😂</span>
                        <span onclick="reactToMessage(<?php echo $message['id']; ?>, '😮')">😮</span>
                        <span onclick="reactToMessage(<?php echo $message['id']; ?>, '😢')">😢</span>
                        <span onclick="reactToMessage(<?php echo $message['id']; ?>, '🙏')">🙏</span>
                    </div>
                    
                    <?php if ($message['file_path']): ?>
                        <?php 
                        $files = json_decode($message['file_path'], true);
                        if ($files):
                            foreach ($files as $file):
                                $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                                $is_image = in_array(strtolower($file_ext), ['jpg', 'jpeg', 'png', 'gif']);
                                $is_video = in_array(strtolower($file_ext), ['mp4', 'webm', 'ogg']);
                                $is_audio = in_array(strtolower($file_ext), ['mp3', 'wav', 'ogg']);
                        ?>
                            <div class="file-preview">
                                <?php if ($is_image): ?>
                                    <img src="<?php echo htmlspecialchars($file['path']); ?>" alt="Image" onclick="openLightbox('<?php echo htmlspecialchars($file['path']); ?>', 'image')">
                                <?php elseif ($is_video): ?>
                                    <video controls>
                                        <source src="<?php echo htmlspecialchars($file['path']); ?>" type="<?php echo htmlspecialchars($file['type']); ?>">
                                        Your browser does not support the video tag.
                                    </video>
                                <?php elseif ($is_audio): ?>
                                    <audio controls>
                                        <source src="<?php echo htmlspecialchars($file['path']); ?>" type="<?php echo htmlspecialchars($file['type']); ?>">
                                        Your browser does not support the audio tag.
                                    </audio>
                                <?php else: ?>
                                    <div class="file-download">
                                        <div class="file-icon">
                                            <i class="fas fa-file"></i>
                                        </div>
                                        <div class="file-name">
                                            <?php echo htmlspecialchars($file['name']); ?>
                                        </div>
                                        <div class="file-download-btn">
                                            <a href="<?php echo htmlspecialchars($file['path']); ?>" download="<?php echo htmlspecialchars($file['name']); ?>">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php 
                            endforeach;
                        endif; 
                        ?>
                    <?php endif; ?>
                    
                    <?php if (!empty($message['content'])): ?>
                        <div class="message-content">
                            <?php echo nl2br(htmlspecialchars($message['content'])); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="message-meta">
                        <span class="message-time">
                            <?php echo date('g:i A', strtotime($message['created_at'])); ?>
                        </span>
                        <?php if ($is_sent): ?>
                            <span class="message-status">
                                <?php if ($message['is_read']): ?>
                                    <i class="fas fa-check-double" style="color: #4fc3f7;"></i>
                                <?php else: ?>
                                    <i class="fas fa-check"></i>
                                <?php endif; ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (isset($reactions[$message['id']])): ?>
                        <div class="message-reactions">
                            <?php foreach ($reactions[$message['id']] as $reaction): ?>
                                <div class="reaction-bubble" title="<?php echo htmlspecialchars($reaction['username']); ?>">
                                    <?php echo $reaction['reaction']; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
                
            <?php if ($index == count($messages) - 1 || $messages[$index + 1]['sender_id'] != $message['sender_id']): ?>
                </div>
            <?php endif; ?>
            
            <?php endforeach; ?>
        </div>
        
        <div class="typing-indicator" id="typing-indicator" style="display:none;">
            <?php echo htmlspecialchars($contact['username']); ?> is typing...
        </div>
        
        <div class="input-container">
            <div class="input-wrapper">
                <div class="attachment-btn">
                    <i class="fas fa-paperclip"></i>
                    <input type="file" id="file-input" multiple accept="image/*,video/*,audio/*,application/pdf">
                </div>
                <textarea class="message-input" id="message-input" placeholder="Type a message..."></textarea>
            </div>
            <button class="send-btn" id="send-btn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
    </div>
    
    <div class="lightbox" id="lightbox">
        <div class="lightbox-close" onclick="closeLightbox()">×</div>
        <div class="lightbox-content" id="lightbox-content"></div>
    </div>
    
    <script src="plugins/jquery-3.6.0/jquery.js"></script>
    <script src="plugins/emojionearea-3.4.1/emojionearea.js"></script>
    <script>
        $(document).ready(function() {
            /*====== EMOJI PICKER INITIALIZATION ======*/
            // Initialize emoji picker with improved positioning and behavior
            $("#message-input").emojioneArea({
                pickerPosition: "top",
                tonesStyle: "bullet",
                search: true,
                shortnames: true,
                saveEmojisAs: "unicode",
                filtersPosition: "bottom",
                searchPosition: "bottom",
                buttonTitle: "Insert emoji",
                events: {
                    keyup: function(editor, event) {
                        updateTypingStatus(true);
                        
                        // Only send message on Enter without shift key
                        if (event.keyCode === 13 && !event.shiftKey) {
                            event.preventDefault();
                            sendMessage();
                            return false;
                        }
                    },
                    keydown: function(editor, event) {
                        // Allow new lines with Shift+Enter
                        if (event.keyCode === 13 && event.shiftKey) {
                            // Let the default behavior happen (new line)
                            return true;
                        }
                    },
                    focus: function(editor, event) {
                        // Ensure emoji picker is visible and properly positioned
                        setTimeout(() => {
                            const picker = $('.emojionearea-picker');
                            if (picker.length) {
                                picker.css({
                                    'z-index': 1000,
                                    'max-width': '100%',
                                    'left': '50%',
                                    'transform': 'translateX(-50%)'
                                });
                            }
                        }, 100);
                    }
                }
            });
            
            /*====== INITIAL SETUP ======*/
            // Scroll to bottom of chat
            scrollToBottom();
            
            // Set up file input change handler
            $("#file-input").on("change", function() {
                handleFileSelection(this.files);
            });
            
            // Set up send button click handler
            $("#send-btn").on("click", function() {
                sendMessage();
            });
            
            // Set up search toggle
            $("#search-toggle").on("click", function() {
                $("#search-container").toggleClass("active");
                if ($("#search-container").hasClass("active")) {
                    $("#search-input").focus();
                } else {
                    clearSearch();
                }
            });
            
            // Set up search functionality
            $("#search-input").on("input", function() {
                searchMessages($(this).val());
            });
            
            // Start polling for new messages
            setInterval(checkNewMessages, 3000);
            
            // Start polling for typing status
            setInterval(checkTypingStatus, 3000);
            
            // Set up last message ID
            window.lastMessageId = <?php echo !empty($messages) ? $messages[count($messages) - 1]['id'] : 0; ?>;
            
            // Set up selected files array
            window.selectedFiles = [];
            
            // Set up reply tracking
            window.replyingTo = null;
        });
        
        /*====== UTILITY FUNCTIONS ======*/
        // Function to scroll to bottom of chat
        function scrollToBottom() {
            const messageContainer = document.getElementById('message-container');
            messageContainer.scrollTop = messageContainer.scrollHeight;
        }
        
        // Function to handle file selection
        function handleFileSelection(files) {
            if (files.length === 0) return;
            
            // Add files to selected files array
            for (let i = 0; i < files.length; i++) {
                window.selectedFiles.push(files[i]);
            }
            
            // Show file preview
            showFilePreview();
        }
        
        // Function to show file preview
        function showFilePreview() {
            // Check if preview container exists
            let previewContainer = document.querySelector('.file-preview-container');
            if (!previewContainer) {
                previewContainer = document.createElement('div');
                previewContainer.className = 'file-preview-container';
                document.querySelector('.input-wrapper').appendChild(previewContainer);
            }
            
            // Clear preview container
            previewContainer.innerHTML = '';
            
            // Add preview for each file
            window.selectedFiles.forEach((file, index) => {
                const previewItem = document.createElement('div');
                previewItem.className = 'file-preview-item';
                
                // Create remove button
                const removeBtn = document.createElement('button');
                removeBtn.className = 'remove-file';
                removeBtn.innerHTML = '×';
                removeBtn.onclick = function() {
                    removeFile(index);
                };
                
                // Create file type label
                const fileTypeLabel = document.createElement('div');
                fileTypeLabel.className = 'file-type';
                
                // Check file type
                if (file.type.startsWith('image/')) {
                    const img = document.createElement('img');
                    img.src = URL.createObjectURL(file);
                    previewItem.appendChild(img);
                    fileTypeLabel.textContent = 'Image';
                } else if (file.type.startsWith('video/')) {
                    const img = document.createElement('img');
                    img.src = 'assets/video-placeholder.png';
                    previewItem.appendChild(img);
                    fileTypeLabel.textContent = 'Video';
                } else if (file.type.startsWith('audio/')) {
                    const img = document.createElement('img');
                    img.src = 'assets/audio-placeholder.png';
                    previewItem.appendChild(img);
                    fileTypeLabel.textContent = 'Audio';
                } else {
                    const img = document.createElement('img');
                    img.src = 'assets/file-placeholder.png';
                    previewItem.appendChild(img);
                    fileTypeLabel.textContent = 'File';
                }
                
                previewItem.appendChild(removeBtn);
                previewItem.appendChild(fileTypeLabel);
                previewContainer.appendChild(previewItem);
            });
        }
        
        // Function to remove file from selection
        function removeFile(index) {
            window.selectedFiles.splice(index, 1);
            
            if (window.selectedFiles.length === 0) {
                // Remove preview container if no files left
                const previewContainer = document.querySelector('.file-preview-container');
                if (previewContainer) {
                    previewContainer.remove();
                }
            } else {
                // Update preview
                showFilePreview();
            }
        }
        
        /*====== MESSAGE HANDLING FUNCTIONS ======*/
        // Function to send message
        function sendMessage() {
            const emojioneArea = $("#message-input").data("emojioneArea");
            const messageContent = emojioneArea.getText().trim();
            
            // Check if message is empty and no files selected
            if (messageContent === '' && window.selectedFiles.length === 0) {
                return;
            }
            
            // Create form data
            const formData = new FormData();
            formData.append('action', 'send_message');
            formData.append('message_content', messageContent);
            formData.append('ajax', 1);
            
            // Add files to form data
            for (let i = 0; i < window.selectedFiles.length; i++) {
                formData.append('file[]', window.selectedFiles[i]);
            }
            
            // Add reply info if replying
            if (window.replyingTo) {
                formData.append('reply_to', window.replyingTo.id);
                formData.append('reply_to_name', window.replyingTo.name);
                formData.append('reply_to_content', window.replyingTo.content);
            }
            
            // Send message
            $.ajax({
                url: 'private_chat.php?contact_id=<?php echo $contact_id; ?>',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Clear input
                        emojioneArea.setText('');
                        
                        // Clear file selection
                        window.selectedFiles = [];
                        const previewContainer = document.querySelector('.file-preview-container');
                        if (previewContainer) {
                            previewContainer.remove();
                        }
                        
                        // Reset file input
                        $("#file-input").val('');
                        
                        // Add message to chat
                        addMessageToChat(response.message);
                        
                        // Update last message ID
                        window.lastMessageId = response.message.id;
                        
                        // Clear reply preview
                        cancelReply();
                    }
                }
            });
        }
        
        // Function to add message to chat
        function addMessageToChat(message) {
            const messageContainer = document.getElementById('message-container');
            const isSent = message.sender_id == <?php echo $_SESSION['user_id']; ?>;
            
            // Create message bubble
            const messageBubble = document.createElement('div');
            messageBubble.className = `message-bubble ${isSent ? 'sent' : 'received'}`;
            messageBubble.setAttribute('data-id', message.id);
            
            // Add message actions
            if (isSent) {
                messageBubble.innerHTML = `
                    <div class="message-actions">
                        <button class="reaction-btn" onclick="toggleReactionOptions(${message.id})">
                            <i class="far fa-smile"></i>
                        </button>
                        <button class="reply-btn" onclick="replyToMessage(${message.id}, '${message.username}', '${message.content ? message.content.replace(/'/g, "\\'").replace(/\n/g, ' ') : ''}')">
                            <i class="fas fa-reply"></i>
                        </button>
                        <button class="delete-btn" onclick="deleteMessage(${message.id})">
                            <i class="far fa-trash-alt"></i>
                        </button>
                    </div>
                    <div class="reaction-options" id="reaction-options-${message.id}">
                        <span onclick="reactToMessage(${message.id}, '👍')">👍</span>
                        <span onclick="reactToMessage(${message.id}, '❤️')">❤️</span>
                        <span onclick="reactToMessage(${message.id}, '😂')">😂</span>
                        <span onclick="reactToMessage(${message.id}, '😮')">😮</span>
                        <span onclick="reactToMessage(${message.id}, '😢')">😢</span>
                        <span onclick="reactToMessage(${message.id}, '🙏')">🙏</span>
                    </div>
                `;
            } else {
                messageBubble.innerHTML = `
                    <div class="message-actions">
                        <button class="reaction-btn" onclick="toggleReactionOptions(${message.id})">
                            <i class="far fa-smile"></i>
                        </button>
                        <button class="reply-btn" onclick="replyToMessage(${message.id}, '${message.username}', '${message.content ? message.content.replace(/'/g, "\\'").replace(/\n/g, ' ') : ''}')">
                            <i class="fas fa-reply"></i>
                        </button>
                    </div>
                    <div class="reaction-options" id="reaction-options-${message.id}">
                        <span onclick="reactToMessage(${message.id}, '👍')">👍</span>
                        <span onclick="reactToMessage(${message.id}, '❤️')">❤️</span>
                        <span onclick="reactToMessage(${message.id}, '😂')">😂</span>
                        <span onclick="reactToMessage(${message.id}, '😮')">😮</span>
                        <span onclick="reactToMessage(${message.id}, '😢')">😢</span>
                        <span onclick="reactToMessage(${message.id}, '🙏')">🙏</span>
                    </div>
                `;
            }
            
            // Add quoted reply if any
            if (message.reply_to) {
                try {
                    const replyData = JSON.parse(message.reply_to);
                    const quotedDiv = document.createElement('div');
                    quotedDiv.className = 'message-quoted';
                    quotedDiv.innerHTML = `
                        <div class="message-quoted-name">${replyData.name}</div>
                        <div class="message-quoted-text">${replyData.content || 'Media message'}</div>
                    `;
                    messageBubble.appendChild(quotedDiv);
                } catch (e) {
                    console.error('Error parsing reply data:', e);
                }
            }
            
            // Add file previews if any
            if (message.file_path) {
                try {
                    const files = JSON.parse(message.file_path);
                    if (files && files.length > 0) {
                        files.forEach(file => {
                            const fileExt = file.name.split('.').pop().toLowerCase();
                            const isImage = ['jpg', 'jpeg', 'png', 'gif'].includes(fileExt);
                            const isVideo = ['mp4', 'webm', 'ogg'].includes(fileExt);
                            const isAudio = ['mp3', 'wav', 'ogg'].includes(fileExt);
                            
                            const filePreview = document.createElement('div');
                            filePreview.className = 'file-preview';
                            
                            if (isImage) {
                                filePreview.innerHTML = `
                                    <img src="${file.path}" alt="Image" onclick="openLightbox('${file.path}', 'image')">
                                `;
                            } else if (isVideo) {
                                filePreview.innerHTML = `
                                    <video controls>
                                        <source src="${file.path}" type="${file.type}">
                                        Your browser does not support the video tag.
                                    </video>
                                `;
                            } else if (isAudio) {
                                filePreview.innerHTML = `
                                    <audio controls>
                                        <source src="${file.path}" type="${file.type}">
                                        Your browser does not support the audio tag.
                                    </audio>
                                `;
                            } else {
                                filePreview.innerHTML = `
                                    <div class="file-download">
                                        <div class="file-icon">
                                            <i class="fas fa-file"></i>
                                        </div>
                                        <div class="file-name">
                                            ${file.name}
                                        </div>
                                        <div class="file-download-btn">
                                            <a href="${file.path}" download="${file.name}">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        </div>
                                    </div>
                                `;
                            }
                            
                            messageBubble.appendChild(filePreview);
                        });
                    }
                } catch (e) {
                    console.error('Error parsing file path:', e);
                }
            }
            
            // Add message content if any
            if (message.content) {
                const messageContent = document.createElement('div');
                messageContent.className = 'message-content';
                messageContent.innerHTML = message.content.replace(/\n/g, '<br>');
                messageBubble.appendChild(messageContent);
            }
            
            // Add message meta
            const messageMeta = document.createElement('div');
            messageMeta.className = 'message-meta';
            
            const messageTime = document.createElement('span');
            messageTime.className = 'message-time';
            
            const date = new Date(message.created_at);
            const hours = date.getHours();
            const minutes = date.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            const formattedHours = hours % 12 || 12;
            const formattedMinutes = minutes < 10 ? '0' + minutes : minutes;
            
            messageTime.textContent = `${formattedHours}:${formattedMinutes} ${ampm}`;
            messageMeta.appendChild(messageTime);
            
            if (isSent) {
                const messageStatus = document.createElement('span');
                messageStatus.className = 'message-status';
                
                if (message.is_read) {
                    messageStatus.innerHTML = '<i class="fas fa-check-double" style="color: #4fc3f7;"></i>';
                } else {
                    messageStatus.innerHTML = '<i class="fas fa-check"></i>';
                }
                
                messageMeta.appendChild(messageStatus);
            }
            
            messageBubble.appendChild(messageMeta);
            
            // Find or create message group
            let messageGroup;
            const lastGroup = messageContainer.lastElementChild;
            
            if (lastGroup && lastGroup.classList.contains(isSent ? 'sent-group' : 'received-group')) {
                messageGroup = lastGroup;
            } else {
                messageGroup = document.createElement('div');
                messageGroup.className = `message-group ${isSent ? 'sent-group' : 'received-group'}`;
                messageContainer.appendChild(messageGroup);
            }
            
            // Add message bubble to group
            messageGroup.appendChild(messageBubble);
            
            // Scroll to bottom
            scrollToBottom();
        }
        
        // Function to check for new messages
        function checkNewMessages() {
            $.ajax({
                url: 'private_chat.php?contact_id=<?php echo $contact_id; ?>&action=check_new_messages&last_id=' + window.lastMessageId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.messages && response.messages.length > 0) {
                        response.messages.forEach(function(message) {
                            addMessageToChat(message);
                            
                            // Update last message ID
                            if (message.id > window.lastMessageId) {
                                window.lastMessageId = message.id;
                            }
                        })
                    }
                }
            });
        }
        
        /*====== TYPING STATUS FUNCTIONS ======*/
        // Function to update typing status
        function updateTypingStatus(isTyping) {
            clearTimeout(window.typingTimer);
            
            if (isTyping) {
                // Set typing timer
                window.typingTimer = setTimeout(function() {
                    // Send typing status update
                    $.ajax({
                        url: 'private_chat.php?contact_id=<?php echo $contact_id; ?>&action=update_typing&is_typing=0',
                        type: 'GET',
                        dataType: 'json'
                    });
                }, 3000);
                
                // Send typing status update
                $.ajax({
                    url: 'private_chat.php?contact_id=<?php echo $contact_id; ?>&action=update_typing&is_typing=1',
                    type: 'GET',
                    dataType: 'json'
                });
            } else {
                // Send typing status update
                $.ajax({
                    url: 'private_chat.php?contact_id=<?php echo $contact_id; ?>&action=update_typing&is_typing=0',
                    type: 'GET',
                    dataType: 'json'
                });
            }
        }
        
        // Function to check typing status
        function checkTypingStatus() {
            $.ajax({
                url: 'private_chat.php?contact_id=<?php echo $contact_id; ?>&action=update_typing&is_typing=0',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.contact_typing) {
                        $('#typing-indicator').show();
                    } else {
                        $('#typing-indicator').hide();
                    }
                }
            });
        }
        
        /*====== REACTION FUNCTIONS ======*/
        // Function to toggle reaction options
        function toggleReactionOptions(messageId) {
            const reactionOptions = document.getElementById(`reaction-options-${messageId}`);
            reactionOptions.classList.toggle('active');
            
            // Close other reaction options
            document.querySelectorAll('.reaction-options.active').forEach(function(element) {
                if (element.id !== `reaction-options-${messageId}`) {
                    element.classList.remove('active');
                }
            });
            
            // Close reaction options when clicking outside
            document.addEventListener('click', function closeReactions(event) {
                if (!event.target.closest('.reaction-options') && !event.target.closest('.reaction-btn')) {
                    document.querySelectorAll('.reaction-options.active').forEach(function(element) {
                        element.classList.remove('active');
                    });
                    document.removeEventListener('click', closeReactions);
                }
            });
        }
        
        // Function to react to message
        function reactToMessage(messageId, reaction) {
            $.ajax({
                url: 'private_chat.php?contact_id=<?php echo $contact_id; ?>',
                type: 'POST',
                data: {
                    action: 'react_message',
                    message_id: messageId,
                    reaction: reaction,
                    ajax: 1
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // Close reaction options
                        document.getElementById(`reaction-options-${messageId}`).classList.remove('active');
                        
                        // Add reaction to message
                        const messageBubble = document.querySelector(`.message-bubble[data-id="${messageId}"]`);
                        
                        // Check if reactions container exists
                        let reactionsContainer = messageBubble.querySelector('.message-reactions');
                        if (!reactionsContainer) {
                            reactionsContainer = document.createElement('div');
                            reactionsContainer.className = 'message-reactions';
                            messageBubble.appendChild(reactionsContainer);
                        }
                        
                        // Check if reaction already exists
                        const existingReaction = reactionsContainer.querySelector(`.reaction-bubble[data-user-id="<?php echo $_SESSION['user_id']; ?>"]`);
                        if (existingReaction) {
                            existingReaction.innerHTML = reaction;
                        } else {
                            // Create new reaction
                            const reactionBubble = document.createElement('div');
                            reactionBubble.className = 'reaction-bubble';
                            reactionBubble.setAttribute('data-user-id', '<?php echo $_SESSION['user_id']; ?>');
                            reactionBubble.setAttribute('title', '<?php echo htmlspecialchars($user['username']); ?>');
                            reactionBubble.innerHTML = reaction;
                            reactionsContainer.appendChild(reactionBubble);
                        }
                    }
                }
            });
        }
        
        /*====== REPLY FUNCTIONS ======*/
        // Function to reply to message
        function replyToMessage(messageId, senderName, content) {
            // Store reply info
            window.replyingTo = {
                id: messageId,
                name: senderName,
                content: content
            };
            
            // Show reply preview
            showReplyPreview(senderName, content);
            
            // Focus on input
            $("#message-input").data("emojioneArea").setFocus();
        }
        
        // Function to show reply preview
        function showReplyPreview(senderName, content) {
            // Remove existing preview
            $('.reply-preview').remove();
            
            // Create reply preview
            const replyPreview = document.createElement('div');
            replyPreview.className = 'reply-preview';
            replyPreview.innerHTML = `
                <div class="reply-preview-content">
                    <div class="reply-preview-name">${senderName}</div>
                    <div class="reply-preview-text">${content}</div>
                </div>
                <button class="reply-preview-close" onclick="cancelReply()">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            // Insert before input wrapper
            $('.input-wrapper').before(replyPreview);
        }
        
        // Function to cancel reply
        function cancelReply() {
            // Remove reply preview
            $('.reply-preview').remove();
            
            // Clear reply info
            window.replyingTo = null;
        }
        
        /*====== MESSAGE MANAGEMENT FUNCTIONS ======*/
        // Function to delete message
        function deleteMessage(messageId) {
            if (confirm('Are you sure you want to delete this message?')) {
                $.ajax({
                    url: 'private_chat.php?contact_id=<?php echo $contact_id; ?>',
                    type: 'POST',
                    data: {
                        action: 'delete_message',
                        message_id: messageId,
                        ajax: 1
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // Remove message from chat
                            const messageBubble = document.querySelector(`.message-bubble[data-id="${messageId}"]`);
                            const messageGroup = messageBubble.parentElement;
                            
                            messageBubble.remove();
                            
                            // Remove message group if empty
                            if (messageGroup.children.length === 0) {
                                messageGroup.remove();
                            }
                        } else {
                            alert(response.message);
                        }
                    }
                });
            }
        }
        
        /*====== SEARCH FUNCTIONS ======*/
        // Function to search messages
        function searchMessages(query) {
            if (query.trim() === '') {
                clearSearch();
                return;
            }
            
            // Remove existing highlights
            document.querySelectorAll('.highlight').forEach(function(element) {
                const parent = element.parentNode;
                parent.innerHTML = parent.innerHTML.replace(/<span class="highlight">([^<]+)<\/span>/g, '$1');
            });
            
            // Highlight matching text
            const regex = new RegExp(query, 'gi');
            document.querySelectorAll('.message-content').forEach(function(element) {
                const content = element.innerHTML;
                element.innerHTML = content.replace(regex, function(match) {
                    return `<span class="highlight">${match}</span>`;
                });
            });
            
            // Find first highlight and scroll to it
            const firstHighlight = document.querySelector('.highlight');
            if (firstHighlight) {
                firstHighlight.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
        
        // Function to clear search
        function clearSearch() {
            document.querySelectorAll('.highlight').forEach(function(element) {
                const parent = element.parentNode;
                parent.innerHTML = parent.innerHTML.replace(/<span class="highlight">([^<]+)<\/span>/g, '$1');
            });
        }
        
        /*====== LIGHTBOX FUNCTIONS ======*/
        // Function to open lightbox
        function openLightbox(src, type) {
            const lightbox = document.getElementById('lightbox');
            const lightboxContent = document.getElementById('lightbox-content');
            
            lightboxContent.innerHTML = '';
            
            if (type === 'image') {
                const img = document.createElement('img');
                img.src = src;
                lightboxContent.appendChild(img);
            } else if (type === 'video') {
                const video = document.createElement('video');
                video.controls = true;
                video.autoplay = true;
                
                const source = document.createElement('source');
                source.src = src;
                
                video.appendChild(source);
                lightboxContent.appendChild(video);
            }
            
            lightbox.classList.add('active');
        }
        
        // Function to close lightbox
        function closeLightbox() {
            const lightbox = document.getElementById('lightbox');
            lightbox.classList.remove('active');
        }
        
        /*====== NAVIGATION FUNCTIONS ======*/
        // Close window function
        function closeWindow() {
            window.location.href = "chat.php";
        }
        
        // Logout function
        function logout() {
            window.location.href = 'logout.php';
        }
    </script>
</body>
</html>
