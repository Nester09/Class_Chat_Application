<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: chat.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat Application</title>
    <link rel="stylesheet" href="plugins/bootstrap-4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="plugins/Font-Awesome-6.4.0/css/all.css">
    <link rel="stylesheet" href="styles/index.css">
</head>
<body onload="initializePage()">
    <div class="container">
        <div id="typingContainer" class="text-center">
            <h1 id="typedText"></h1>
        </div>
        <div id="mainContent" class="hidden text-center">
            <div class="mb-4">
                <h2>NexyMessenger</h2>
                <p>A simple chat app for staying in touch with friends and groups. Send messages, share photos, and keep conversations going in real-time.</p> 
            </div>
            <div class="button-container">
                <a href="register.php" class="btn btn-primary"> <i class="fas fa-user-plus"></i> Register </a>
                <a href="login.php" class="btn btn-secondary"> <i class="fas fa-sign-in-alt"></i> Login </a>
                <button type="button" class="btn btn-info" data-toggle="modal" data-target="#previewModal"> <i class="fas fa-eye"></i> Preview </button>
            </div> 
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1" role="dialog" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="previewModalLabel">What's Inside</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="preview-section">
                        <h6><i class="fas fa-comments text-primary"></i> Real-Time Messaging</h6>
                        <p>Chat with your friends instantly. Messages show up right away without refreshing the page.</p>
                        <img src="images/preview-chat.png" alt="Chat Interface" class="img-fluid rounded mb-3">
                    </div>
                    
                    <div class="preview-section">
                        <h6><i class="fas fa-users text-success"></i> Group Conversations</h6>
                        <p>Create groups for your team, family, or friend circles. Everyone stays on the same page.</p>
                        <img src="images/preview-groups.png" alt="Group Chat" class="img-fluid rounded mb-3">
                    </div>
                    
                    <div class="preview-section">
                        <h6><i class="fas fa-image text-warning"></i> Photo Sharing</h6>
                        <p>Share images directly in your chats. No need for external links or apps.</p>
                        <img src="images/preview-photos.png" alt="Photo Sharing" class="img-fluid rounded mb-3">
                    </div>
                    
                    <div class="preview-section">
                        <h6><i class="fas fa-bell text-danger"></i> Instant Notifications</h6>
                        <p>Get notified when someone sends you a message so you never miss important updates.</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <a href="register.php" class="btn btn-primary">Get Started</a>
                </div>
            </div>
        </div>
    </div>

    <script src="plugins/jquery-3.6.0/jquery.slim.js"></script>
    <script src="plugins/bootstrap-4.5.2/js/bootstrap.bundle.js"></script>
    <script>
        const word = "NexyMessenger";
        let index = 0;

        function initializePage() {
            if (!sessionStorage.getItem('typedEffectCompleted')) {
                startTyping();
                sessionStorage.setItem('typedEffectCompleted', 'true'); 
            } else {
                showMainContent();
            }
        }

        function startTyping() {
            const typedText = document.getElementById("typedText");
            typedText.innerHTML = ""; 
            
            word.split('').forEach((char, i) => {
                const span = document.createElement('span');
                span.textContent = char;
                span.style.display = 'inline-block';
                span.style.clipPath = 'inset(0 100% 0 0)';
                span.style.transition = 'clip-path 0.3s ease-out';
                typedText.appendChild(span);
                
                setTimeout(() => {
                    span.style.clipPath = 'inset(0 0 0 0)';
                }, i * 150);
            });
            
            setTimeout(() => {
                setTimeout(swipeUp, 1000);
            }, word.length * 150 + 300);
        }

        function swipeUp() {
            document.body.style.transition = "transform 1s ease-in-out";
            document.body.style.transform = "translateY(-100%)"; 
            setTimeout(showMainContent, 1000); 
        }

        function showMainContent() {
            document.getElementById("typingContainer").classList.add("hidden");
            document.getElementById("mainContent").classList.remove("hidden");
            document.body.style.transform = "none"; 
        }
    </script>
</body>
</html>