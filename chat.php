<?php include 'chat-backend.php';?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="styles/chat.css">
    <link rel="stylesheet" href="plugins/bootstrap-4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="plugins/Font-Awesome-6.4.0/css/all.css">
    <link rel="stylesheet" href="plugins/emojionearea-3.4.1/emojionearea.css">
</head>
<body>
    <!-- Search Messages Modal -->
    <div class="modal fade" id="searchMessagesModal" tabindex="-1" role="dialog" aria-labelledby="searchMessagesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="searchMessagesModalLabel">Search Messages</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="searchMessagesContent">
                    <!-- Messages will be loaded here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Message Modal -->
    <div class="modal fade" id="editMessageModal" tabindex="-1" role="dialog" aria-labelledby="editMessageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editMessageModalLabel">Edit Message</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST" id="editMessageForm">
                    <div class="modal-body">
                        <input type="hidden" name="message_id" id="edit_message_id">
                        <textarea name="new_content" id="edit_message_content" class="form-control" rows="5" maxlength="5000" required></textarea>
                        <div id="emoji-picker"></div>
                        <small class="text-muted">Character count: <span id="edit-char-count">0</span>/5000</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" name="edit_message" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Group Settings Modal -->
    <div id="group-settings-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <span class="close-btn" onclick="closeGroupSettings()">&times;</span>
            <h5>Group Settings</h5>
            <form method="POST" action="group_management.php?group_id=<?php echo $group_id; ?>">
                <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                <input type="text" name="new_group_name" placeholder="New Group Name" required>
                <button type="submit" name="update_group_name" class="btn btn-primary">Update Group Name</button>
            </form><br>

            <h5>Members Management</h5>
            <form method="POST" action="group_management.php?group_id=<?php echo $group_id; ?>">
                <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                <select name="member_id" required>
                    <option value="">Select Member to Remove</option>
                    <?php foreach ($group_users as $member): ?>
                        <?php if ($member['id'] != $userId): ?>
                            <option value="<?php echo $member['id']; ?>"><?php echo htmlspecialchars($member['username']); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="remove_member" class="btn btn-danger">Remove Member</button>
            </form><br>

            <form method="POST" action="group_management.php?group_id=<?php echo $group_id; ?>">
                <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                <input type="text" name="new_member_email" placeholder="Email of New Member" required>
                <button type="submit" name="add_member" class="btn btn-success">Add Member</button>
            </form>

            <form method="POST" action="group_management.php?group_id=<?php echo $group_id; ?>">
                <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
                <button type="submit" name="delete_group" class="btn btn-danger">Delete Group</button>
            </form>
        </div>
    </div>

    <div class="container-fluid chat-container">
        <div class="left-column">
            <div class="left">
                <div class="user-profile">
                    <!-- Profile picture display -->
                    <?php if ($user['profile_picture']): ?>
                        <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture">
                    <?php else: ?>
                        <img src="profile/default_profile.jpg" alt="Profile Picture">
                    <?php endif; ?>
                    <h4><?php echo htmlspecialchars($user['username'] ?? 'Unknown User'); ?></h4>
                    <p><?php echo htmlspecialchars($user['about'] ?? 'No about information available.'); ?></p>
                    <p><div class="dot"></div><small class="text-muted">Online</small></p>        
                    <p><small class="text-muted" id="current-time">Current Time: <?php echo date('H:i:s'); ?></small></p>
                </div>

                <div class="options-menu">
                    <h5>Profile & Communication Management</h5>
                    <button class="btn btn-secondary" onclick="openSearchModal()"><i class="fas fa-search"></i> Search Messages</button>
                    
                    <?php $_SESSION['previous_action'] = $_SERVER['REQUEST_URI']; ?>
                    
                    <a href="edit_profile.php" class="btn btn-info"><i class="fas fa-user-edit"></i> Edit Profile</a>

                    <button class="btn btn-primary" onclick="openAvailableContactsModal()"><i class="fas fa-address-book"></i> Available Contacts</button>
                    <a href="logout.php" class="btn btn-danger" onclick="return confirm('Confirm Logout.');"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
            
            <!-- Available Contacts Modal -->
            <div id="availableContactsModal" class="modal" style="width: 40%; padding-left:35px;">
                <div class="modal-content">
                    <span class="close" onclick="closeAvailableContactsModal()">&times;</span>
                    <h5>Available Contacts</h5>
                    <p class="text-muted">Choose someone to start a private conversation</p>
                    <input type="text" id="search-contact-input" placeholder="Search contacts..." onkeyup="searchContacts()">
                    <ul id="available-contacts-list" class="list-group scrollable-list">
                        <?php if (empty($availableContacts)): ?>
                            <li class="contact-item">
                                <span>No available contacts</span>
                            </li>
                        <?php else: ?>
                            <?php foreach ($availableContacts as $contact): ?>
                                <li class="contact-item" onclick="openPrivateChat(<?php echo $contact['id']; ?>)">
                                    <img src="<?php echo htmlspecialchars($contact['profile_picture'] ?? 'default_avatar.png'); ?>" alt="Profile Picture">
                                    <?php echo htmlspecialchars($contact['username']); ?>
                                </li>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="central-column">
            <?php if ($group_id): ?>
                <div class="group-info">
                    <h2><?php echo htmlspecialchars($group_name); ?></h2>
                    <p>Number of Users: <?php echo count($group_users); ?></p>
                    <div class="user-list-container">
                        <button class="btn btn-secondary" onclick="toggleUserList()"><i class="fas fa-users"></i> View Users</button>
                        <div id="user-list" class="user-list" style="display: none;">
                            <?php foreach ($group_users as $user): ?>
                                <div class="user-item">
                                    <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" alt="Profile Picture" class="user-profile-pic">
                                    <span><?php echo htmlspecialchars($user['username']); ?>
                                        <?php if ($user['role'] === 'admin' || $user['role'] === 'creator'): ?>
                                            <span class="badge badge-admin">Group Admin</span>
                                        <?php endif; ?>
                                    </span>
                                    <?php if ($user['id'] == $userId && ($user['role'] === 'admin' || $user['role'] === 'creator')): ?>
                                        <button class="managesettings" onclick="openGroupSettings()"><i class="fas fa-cog"></i> Manage Group</button>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Messages Section -->
                <div id="messages" class="flex-grow-1 overflow-auto">
                    <?php if (isset($messages['prompt'])): ?>
                        <p>You need to join this group to view its messages. <button class="btn btn-primary" onclick="joinGroup(<?php echo $group_id; ?>)">Join Group</button></p>
                    <?php else: ?>
                        <?php foreach ($messages as $date => $msgs): ?>
                            <h5><?php echo $date; ?></h5>
                            <?php foreach ($msgs as $message): ?>
                                <?php 
                                    // Check if message is less than 12 hours old
                                    $created_time = strtotime($message['created_at']);
                                    $current_time = time();
                                    $time_diff = $current_time - $created_time;
                                    $can_edit_delete = ($time_diff <= 43200) && ($message['user_id'] == $userId);
                                ?>
                                <div class="message-bubble <?php echo $message['user_id'] == $userId ? 'sent' : 'received'; ?>">
                                    <img src="<?php echo htmlspecialchars($message['profile_picture']); ?>" alt="Profile Picture">
                                    <span class="sender"><?php echo $message['user_id'] == $userId ? 'You' : htmlspecialchars($message['username']); ?></span>
                                    <div><?php echo nl2br(htmlspecialchars($message['content'])); ?></div> 
                                    <small><?php echo date('g:i A', strtotime($message['created_at'])); ?></small> 
                                    <?php if ($message['file_path']): ?>
                                        <a href="<?php echo htmlspecialchars($message['file_path']); ?>" class="file-link" download>Download File</a>
                                    <?php endif; ?>
                                    <?php if ($message['user_id'] == $userId): ?>
                                        <div class="message-actions">
                                            <button class="btn btn-link text-info <?php echo !$can_edit_delete ? 'disabled' : ''; ?>"
                                                    data-message-id="<?php echo $message['id']; ?>"
                                                    data-message-content="<?php echo htmlspecialchars($message['content'], ENT_QUOTES); ?>"
                                                    onclick="<?php echo $can_edit_delete ? 'editMessageFromButton(this)' : 'alert(\'You can only edit messages within 12 hours of sending.\')'; ?>"
                                                    <?php echo !$can_edit_delete ? 'disabled' : ''; ?>>
                                                Edit
                                            </button>
                                            <form method="POST" style="display:inline;">
                                                <input type="hidden" name="message_id" value="<?php echo $message['id']; ?>">
                                                <button type="submit" name="delete_message" class="btn btn-link text-danger <?php echo !$can_edit_delete ? 'disabled' : ''; ?>"
                                                        onclick="return <?php echo $can_edit_delete ? 'confirm(\'Are you sure you want to delete this message? This action cannot be undone.\')' : 'alert(\'You can only delete messages within 12 hours of sending.\'); return false'; ?>"
                                                        <?php echo !$can_edit_delete ? 'disabled' : ''; ?>>
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="message-input">
                    <form method="POST" enctype="multipart/form-data" onsubmit="return validateAndSubmit()">
                        <input type="hidden" name="group_id" value="<?php echo $group_id; ?>">

                        <textarea id="message_content" name="message_content" placeholder="Type your message (max 5000 characters)..." maxlength="5000" required></textarea>

                        <div class="message-actions mt-2">
                            <label for="file_input" class="btn btn-secondary">
                                <i class="fas fa-paperclip"></i>
                            </label>
                            <input type="file" id="file_input" name="file" accept=".jpg,.jpeg,.png,.gif,.mp4,.mp3,.pdf,.docx,.txt" style="display:none;">
                            <button type="submit" name="send_message" class="btn btn-primary" <?php echo !in_array($group_id, $user_groups) ? 'disabled' : ''; ?>>
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>

                    <!-- Display Success or Error Message -->
                    <div id="messageNotifications">
                        <?php
                        if (isset($_SESSION['message'])) {
                            $message = $_SESSION['message'];
                            $message_type = $_SESSION['message_type']; 

                            echo "<div class='notification $message_type'>" . htmlspecialchars($message) . "</div>";
                            unset($_SESSION['message']);
                            unset($_SESSION['message_type']);
                        }
                        ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="welcome-dashboard">
                    <div class="welcome-header">
                        <?php 
                        $username = $user['username'] ?? 'Guest';

                        //// ======== TIME-BASED GREETING ========
                        $hour = date("H");
                        if ($hour < 11) {
                            $greeting = "Good Morning🌄";
                        } elseif ($hour < 17) {
                            $greeting = "Good Afternoon🕑";
                        } else {
                            $greeting = "Good Evening🌃";
                        }
                        ?>
                        <i class="fas fa-comments welcome-icon"></i>
                        <h3><?php echo "$greeting, $username!"; ?></h3>
                    </div>
                    
                    <div class="feature-cards">
                        <div class="feature-card">
                        <i class="fas fa-users"></i>
                        <h4>Join Groups</h4>
                        <p>Connect with others by joining available groups or create your own.</p>
                        </div>
                        
                        <div class="feature-card">
                        <i class="fas fa-file-upload"></i>
                        <h4>Share Files</h4>
                        <p>Share images, documents, and more with your group members.</p>
                        </div>
                        
                        <div class="feature-card">
                        <i class="fas fa-search"></i>
                        <h4>Search Messages</h4>
                        <p>Easily find past conversations with the search feature.</p>
                        </div>
                    </div>
                    
                    <div class="quick-stats">
                        <div class="stat-item">
                        <span class="stat-value"><?php echo $user_groups_count; ?></span>
                        <span class="stat-label">Your Groups</span>
                        </div>
                        
                        <div class="stat-item">
                        <span class="stat-value"><?php echo count($availableContacts); ?></span>
                        <span class="stat-label">Contacts</span>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div class="right-column">
            <!-- Actions -->
            
            <!-- Display number of groups user is a member of -->
            <div class="right-section">
                <h5><i class="fas fa-chart-bar"></i> Your Stats</h5>
                <div class="user-groups-count">
                    <h5>Your Groups <span class="badge badge-primary"><?php echo $user_groups_count; ?></span></h5>
                    <h5>Contacts <span class="badge badge-primary"><?php echo count($availableContacts); ?></span></h5>
                </div>
            </div>
            
            <!-- Clear Chat - Only show groups user is a member of -->
            <div class="right-section">
                <h5><i class="fas fa-cog"></i> Group Management</h5>
                <!-- Group Selection to clear chat -->
                <form method="POST" onsubmit="return confirmClearChat();">
                    <select name="clear_group_id" id="clear_group_id" required>
                        <option value="">Select Group to Clear Chat</option>
                        <?php foreach ($user_groups_data as $group): ?>
                            <option value="<?php echo $group['id']; ?>"><?php echo htmlspecialchars($group['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="clear_chat" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Clear Chat</button> 
                </form>
            </div>

            <div class="right-section">
                <!-- Search Groups -->
                <input type="text" id="search-group-input" placeholder="Search groups..." onkeyup="searchGroups()">

                <h5><i class="fas fa-th-list"></i> Available Groups</h5>
                <ul class="list-group scrollable-list" id="group-list">
                    <?php foreach ($groups as $group): ?>
                        <li class="list-group-item group-item <?php echo in_array($group['id'], $user_groups) ? 'member' : ''; ?>" 
                            data-group-id="<?php echo $group['id']; ?>" 
                            onclick="loadGroup(<?php echo $group['id']; ?>)">
                            <?php if (in_array($group['id'], $user_groups)): ?>
                                <i class="fas fa-user-check text-success"></i>
                            <?php endif; ?>
                            <?php echo htmlspecialchars($group['name']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul><br>
            </div>
            
            <div class="right-section">
                <h5><i class="fas fa-users-cog"></i> Group Actions</h5>
                <h5>Create Group</h5>
                <form method="POST">
                    <div class="input-container" style="position: relative;">
                        <input type="text" name="group_name" placeholder="Enter group name" required id="groupNameInput" maxlength="50" <?php if (isset($_SESSION['success'])) echo 'disabled'; ?>>
                        
                        <span id="emojiButton" class="emoji-icon" title="Select Emoji">
                            <i class="fas fa-smile"></i>
                        </span>
                    </div>
                    <button type="submit" name="create_group" class="btn btn-primary" id="createGroupButton" <?php if (isset($_SESSION['success'])) echo 'disabled'; ?>><i class="fas fa-plus"></i> Create Group</button>
                </form>

                <!-- Notification Area -->
                <div id="notificationArea">
                    <?php 
                    if (isset($_SESSION['success'])) {
                        echo "<div class='notification success'>" . htmlspecialchars($_SESSION['success']) . "</div>";
                        unset($_SESSION['success']); 
                    }
                    
                    if (isset($_SESSION['error'])) {
                        echo "<div class='notification error'>" . htmlspecialchars($_SESSION['error']) . "</div>";
                        unset($_SESSION['error']);
                    }
                    ?>
                </div>

                <!-- Emoji Picker Modal -->
                <div id="emojiPicker" style="display:none; position: absolute; z-index: 1000;">
                    <button type="button" class="emoji" onclick="insertEmoji('😊')">😊</button>
                    <button type="button" class="emoji" onclick="insertEmoji('😂')">😂</button>
                    <button type="button" class="emoji" onclick="insertEmoji('❤️')">❤️</button>
                    <button type="button" class="emoji" onclick="insertEmoji('🔥')">🔥</button>
                </div>

                <!-- Join Group - Only show groups user is NOT a member of -->
                <h5>Join Group</h5>
                <form method="POST">
                    <select name="group_id" required>
                        <option value="">Select a group</option>
                        <?php foreach ($non_member_groups as $group): ?>
                            <option value="<?php echo $group['id']; ?>"><?php echo htmlspecialchars($group['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="join_group" class="btn btn-success"><i class="fas fa-user-plus"></i> Join Group</button>
                </form>

                <!-- Leave Group - Only show groups user is a member of -->
                <h5>Select Group to Leave</h5>
                <form method="POST" id="leaveGroupForm" onsubmit="return confirmLeaveGroup();">
                    <select name="group_id" id="group_id" required>
                        <option value="">Select a group to leave</option>
                        <?php foreach ($user_groups_data as $group): ?>
                            <option value="<?php echo $group['id']; ?>"><?php echo htmlspecialchars($group['name']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" name="leave_group_button" class="btn btn-warning"><i class="fas fa-sign-out-alt"></i> Leave Group</button>
                </form>

                <!-- Notification Area -->
                <div id="leaveGroupNotificationArea">
                    <?php 
                        if (isset($_SESSION['leave_group_success'])) {
                            echo "<div class='notification success'>" . htmlspecialchars($_SESSION['leave_group_success']) . "</div>";
                            unset($_SESSION['leave_group_success']);
                        }
                        if (isset($_SESSION['leave_group_error'])) {
                            echo "<div class='notification error'>" . htmlspecialchars($_SESSION['leave_group_error']) . "</div>";
                            unset($_SESSION['leave_group_error']);
                        }
                    ?>
                </div>
            </div>

            <div class="right-section">
                <h5><i class="fas fa-question-circle"></i> Support</h5>
                <h5>Help Center</h5>
                <p><a href="#" onclick="openHelpCenterModal()"><i class="fas fa-life-ring"></i> For quick solutions, visit our Help Center.</a></p>        
                <h5>Privacy Policy</h5>
                <p><a href="#" onclick="openPrivacyPolicyModal()"><i class="fas fa-shield-alt"></i> Read our Privacy Policy.</a></p>
            </div>
        </div>
    </div>

    <div class="scroll-indicator"></div>

    <!-- Modal for Help Center -->
    <div id="helpCenterModal" class="modal" id="options">
        <div class='modal-content' id="modal-content1">
            <span class='close' id="close1" onclick='closeHelpCenterModal()'>&times;</span>
            <h4>Help Center</h4>
            <p>
                Welcome to NexyMessenger Help Center. This section provides
                guidance on the main features available in the chat application.
            </p>
    
            <h6>Creating and Joining Groups</h6>
            <ul>
                <li>Create new groups by entering a name and clicking "Create Group"</li>
                <li>Join existing groups from the "Available Groups" list</li>
                <li>Leave groups anytime from the "Group Actions" section</li>
            </ul>

            <h6>Need More Help?</h6>
            <p>Contact our support team:</p>
            <p><strong>Email:</strong> support@chatapp.com<br>
            <strong>Response Time:</strong> Within 24 hours<br>
            <strong>Available:</strong> Monday - Friday, 9 AM - 6 PM</p>
        </div>
    </div>

    <!-- Modal for Privacy Policy -->
    <div id='privacyPolicyModal' class='modal' id="options">
        <div class='modal-content' id="modal-content1">
            <span class='close' id="close1" onclick='closePrivacyPolicyModal()'>&times;</span>
            <h4>Privacy Policy</h4>
            <p>
                Your privacy is important to us. This Privacy Policy explains
                what information NexyMessenger stores and how that information
                is used within the application.
            </p>
            <p>We take the security of your personal information seriously and 
               implement reasonable measures to protect it from unauthorized access, 
               use, or disclosure. However, no method of transmission over the Internet
               or method of electronic storage is 100% secure.
            </p>
        </div>
    </div>

    <script src="plugins/jquery-3.6.0/jquery.js"></script>
    <script src="plugins/emojionearea-3.4.1/emojionearea.js"></script>
    <script src="plugins/bootstrap-4.5.2/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            // Initialize emoji area
            $("#message_content").emojioneArea({
                pickerPosition: "top",
                tones: false,
                events: {
                    keyup: function(editor, event) {
                        
                        // Show typing indicator
                        $("#typing-indicator").show();
                        clearTimeout($.data(this, 'timer'));
                        $.data(this, 'timer', setTimeout(function() {
                            $("#typing-indicator").fadeOut();
                        }, 1000));
                    },
                    paste: function(editor, event) {
                        // Handle paste event to enforce character limit
                        setTimeout(function() {
                            const text = editor[0].value;
                            if (text.length > 5000) {
                                editor[0].value = text.substring(0, 5000);
                            }
                        }, 10);
                    }
                }
            });

            // Character counter for edit message modal
            $("#edit_message_content").on("input", function() {
                updateEditCharCount(this);
            });

            // Scroll to the last message when loaded
            $(window).on('load', function() {
                scrollToBottom();
            });

            // Disable send button on message send
            $('#messageForm').on('submit', function() {
                $('#sendButton').prop('disabled', true);
            });
        });

        // Update character count for edit message textarea
        function updateEditCharCount(textarea) {
            const count = textarea.value.length;
            document.getElementById('edit-char-count').textContent = count;
        }

        // Validate message before submitting
        function validateAndSubmit() {
            const textarea = document.getElementById('message_content');
            const content = textarea.value;
            
            // Check if user is a member of the group
            <?php if (!in_array($group_id, $user_groups)): ?>
                alert("You must join this group to send messages.");
                return false;
            <?php endif; ?>
            
            // Truncate if over 1000 characters
            if (content.length > 5000) {
                textarea.value = content.substring(0, 5000);
            }
            
            // Clear stored message
            localStorage.removeItem('unsentMessage');
            return true;
        }

        function scrollToBottom() {
            var messagesContainer = $('#messages');
            messagesContainer.scrollTop(messagesContainer[0].scrollHeight);
        }

        function toggleUserList() {
            const userList = document.getElementById('user-list');
            userList.style.display = userList.style.display === 'none' ? 'block' : 'none';
        }

        function openPrivateChat(contactId) {
            window.location.href = "private_chat.php?contact_id=" + contactId; 
        }

        // Open available contacts modal
        function openAvailableContactsModal() {
            document.getElementById('availableContactsModal').style.display = 'block';
        }

        // Close available contacts modal
        function closeAvailableContactsModal() {
            document.getElementById('availableContactsModal').style.display = 'none';
        }

        // Search contacts
        function searchContacts() {
            const input = document.getElementById('search-contact-input').value.toLowerCase();
            const contacts = document.querySelectorAll('#available-contacts-list .contact-item');
            
            contacts.forEach(contact => {
                const name = contact.textContent.toLowerCase();
                contact.style.display = name.includes(input) ? '' : 'none';
            });
        }

        // Update current time every second
        setInterval(() => {
            const currentTimeElement = document.getElementById('current-time');
            const now = new Date();
            currentTimeElement.innerText = `Current Time: ${now.toLocaleTimeString()}`;
        }, 1000);

        // Confirm leaving the group
        function confirmLeaveGroup() {
            var groupId = document.getElementById('group_id').value;
            if (groupId) {
                return confirm("Are you sure you want to leave this group?");
            } else {
                alert("Please select a group first.");
                return false;
            }
        }

        // Load group
        function loadGroup(groupId) {
            window.location.href = "?group_id=" + groupId; 
        }

        // Open search modal
        function openSearchModal() {
            $("#searchMessagesModal").modal('show');

            $.post('search_messages.php', { group_id: <?php echo json_encode($group_id); ?> }, function(response) {
                $('#searchMessagesContent').html(response);
            });
        }
        
        // Edit message
        function editMessage(messageId, content) {
            $('#edit_message_id').val(messageId);
            const formattedContent = content.replace(/<br\s*\/?>/gi, '\n');
            $('#edit_message_content').val(formattedContent);
            updateEditCharCount(document.getElementById('edit_message_content'));
            $('#editMessageModal').modal('show');
        }

        function editMessageFromButton(button) {
            const messageId = button.getAttribute('data-message-id');
            const messageContent = button.getAttribute('data-message-content');
            editMessage(messageId, messageContent);
        }

        // Join group
        function joinGroup(groupId) {
            $.post('chat.php', { join_group: true, group_id: groupId }, function(response) {
                window.location.reload();
            });
        }

        // Toggle emoji picker visibility
        document.getElementById('emojiButton').addEventListener('click', function(event) {
            const emojiPicker = document.getElementById('emojiPicker');
            emojiPicker.style.display = emojiPicker.style.display === 'none' ? 'block' : 'none';
            
            // Position the emoji picker below the input field
            const inputRect = document.getElementById('groupNameInput').getBoundingClientRect();
            emojiPicker.style.top = `${inputRect.bottom + window.scrollY}px`;
            emojiPicker.style.left = `${inputRect.left + window.scrollX}px`;
            
            // Prevent event propagation to avoid closing the picker when clicking inside it
            event.stopPropagation();
        });

        // Insert selected emoji into the input field
        function insertEmoji(emoji) {
            const groupNameInput = document.getElementById('groupNameInput');
            groupNameInput.value += emoji;
            document.getElementById('emojiPicker').style.display = 'none'; 
        }

        // Close emoji picker when clicking outside of it
        document.addEventListener('click', function() {
            document.getElementById('emojiPicker').style.display = 'none';
        });

        // Function to remove notifications after a certain time
        function removeNotifications() {
            const notifications = document.querySelectorAll('.notification');
            notifications.forEach((notification, index) => {
                setTimeout(() => {
                    notification.style.transition = "opacity 0.5s ease-out";
                    notification.style.opacity = 0;
                    setTimeout(() => {
                        notification.remove();
                        // Re-enable the input and button after the success notification disappears
                        if (notification.classList.contains('success')) {
                            document.getElementById('groupNameInput').disabled = false;
                            document.getElementById('createGroupButton').disabled = false;
                        }
                    }, 500); // Wait for the fade-out effect before removing
                }, 3000 + index * 1000); // Time in milliseconds before fading out (3 seconds + stagger for multiple messages)
            });
        }

        // Call the function to remove notifications when the page loads
        window.onload = function() {
            removeNotifications();
            
            // Load unsent message from localStorage
            const messageContent = localStorage.getItem('unsentMessage');
            if (messageContent) {
                document.getElementById('message_content').value = messageContent;
                // Update the emojionearea with the stored message
                if ($('.emojionearea-editor').length) {
                    $('.emojionearea-editor').html(messageContent);
                }
            }
            
            // Save message content to localStorage on input
            document.getElementById('message_content').addEventListener('input', function() {
                localStorage.setItem('unsentMessage', this.value);
            });
            
            // Also handle input in the emojionearea editor
            $('.emojionearea-editor').on('input', function() {
                localStorage.setItem('unsentMessage', $(this).html());
            });
        };

        // Search groups
        function searchGroups() {
            let input = document.getElementById('search-group-input');
            let filter = input.value.toLowerCase();
            let groupList = document.getElementById('group-list');
            let groups = groupList.getElementsByClassName('group-item');

            for (let i = 0; i < groups.length; i++) {
                let groupName = groups[i].textContent || groups[i].innerText;
                if (groupName.toLowerCase().indexOf(filter) > -1) {
                    groups[i].style.display = "";
                } else {
                    groups[i].style.display = "none";
                }
            }
        }

        // Confirm clear chat function
        function confirmClearChat() {
            const groupId = document.getElementById('clear_group_id').value;
            if (groupId && confirm("Are you sure you want to clear all messages in this group on your side? This action cannot be undone on your side.")) {
                return true;
            }
            return false;
        }

        // Function to open group settings
        function openGroupSettings() {
            if (confirm("Are you sure you want to manage this group?")) {
                // Store the group_id in the session via AJAX before opening the modal
                fetch('store_group_id.php?group_id=<?php echo $group_id; ?>')
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            document.getElementById('group-settings-modal').style.display = 'block';
                        } else {
                            alert('Error: Could not store group ID. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('group-settings-modal').style.display = 'block';
                    });
            }
        }

        // Function to close group settings
        function closeGroupSettings() {
            document.getElementById('group-settings-modal').style.display = 'none';
        }

        // Function to open help center modal
        function openHelpCenterModal() {
            document.getElementById('helpCenterModal').style.display = 'block';
        }

        // Function to close help center modal
        function closeHelpCenterModal() {
            document.getElementById('helpCenterModal').style.display = 'none';
        }

        // Function to open privacy policy modal
        function openPrivacyPolicyModal() {
            document.getElementById('privacyPolicyModal').style.display = 'block';
        }

        // Function to close privacy policy modal
        function closePrivacyPolicyModal() {
            document.getElementById('privacyPolicyModal').style.display = 'none';
        }
        
        // Function to check for new messages using AJAX
        function checkNewMessages() {
            fetch('check_new_messages.php')
                .then(response => response.json())
                .then(data => {
                    if (data.new_messages) {
                        showNotification("New message(s) in group: " + data.group_name);
                    }
                });
        }

        // Function to show notification at the top of the page
        function showNotification(message) {
            const notification = document.createElement("div");
            notification.className = "notification";
            notification.innerText = message;

            document.body.appendChild(notification);

            setTimeout(() => {
                notification.remove();
            }, 5000); 
        }

        // Call the function to check for new messages periodically
        setInterval(checkNewMessages, 30000);

        // Function to toggle the visibility of the dot
        function toggleDot() {
            const dot = document.querySelector('.dot');

            // Check current opacity and toggle
            if (dot.style.opacity === '1' || dot.style.opacity === '') {
                dot.style.opacity = '0'; 
            } else {
                dot.style.opacity = '1'; 
            }
        }

        // Set an interval to toggle the dot every second
        setInterval(toggleDot, 1000);
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Helper function to save and restore scroll positions
            function handleScrollPersistence(selector, storageKey) {
                const element = document.querySelector(selector);
                if (!element) return;

                // Restore previous scroll position (if any)
                const savedScroll = localStorage.getItem(storageKey);
                if (savedScroll !== null) {
                element.scrollTop = parseInt(savedScroll);
                }

                // Save scroll position on scroll
                element.addEventListener('scroll', () => {
                localStorage.setItem(storageKey, element.scrollTop);
                });
            }

            handleScrollPersistence('.left-column', 'leftColumnScroll');
            handleScrollPersistence('.right-column', 'rightColumnScroll');
            handleScrollPersistence('#group-list', 'groupListScroll');
            handleScrollPersistence('#available-contacts-list', 'contactsListScroll');
        });
    </script>
    <script>
        const container = document.querySelector('.chat-container');
        const indicator = document.querySelector('.scroll-indicator');

        container.addEventListener('scroll', () => {
            const maxScroll = container.scrollWidth - container.clientWidth;
            const percentage = (container.scrollLeft / maxScroll) * 100;
            indicator.style.width = percentage + '%';
        });
    </script>
</body>
</html>