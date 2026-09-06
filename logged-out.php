<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logout Successful</title>
    <link href="plugins/bootstrap-5.3.0/css/bootstrap.css" rel="stylesheet">
    <link href="plugins/Font-Awesome-6.4.0/css/all.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #f0f4f8, #d6e8f5); 
            font-family: Arial, Helvetica, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .logout-container {
            max-width: 450px;
            margin: 0 auto;
            padding: 2rem;
        }
        
        .logout-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            padding: 3rem 2rem;
        }
        
        .success-icon {
            width: 80px;
            height: 80px;
            background: #28a745;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }
        
        .success-icon i {
            color: white;
            font-size: 2rem;
        }
        
        .logout-title {
            color: #333;
            margin-bottom: 1rem;
        }
        
        .logout-message {
            color: #6c757d;
            line-height: 1.6;
            margin-bottom: 2rem;
        }
        
        .btn-login {
            background: #007bff;
            border: none;
            border-radius: 5px;
            padding: 12px 24px;
            font-weight: 500;
            width: 100%;
            margin-bottom: 1rem;
        }
        
        .btn-home {
            border-radius: 5px;
            padding: 10px 24px;
            font-weight: 500;
            width: 100%;
        }
        
        /*====== RESPONSIVE ADJUSTMENTS ======*/
        @media (max-width: 576px) {
            .logout-container {
                padding: 1rem;
            }
            
            .logout-card {
                padding: 2rem 1.5rem;
            }
            
            .success-icon {
                width: 60px;
                height: 60px;
            }
            
            .success-icon i {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logout-container">
            <!------------------- LOGOUT SUCCESS CARD -------------------->
            <div class="card logout-card text-center">
                <div class="success-icon">
                    <i class="fas fa-check"></i>
                </div>
                
                <h2 class="logout-title">Successfully Logged Out</h2>
                
                <p class="logout-message">
                    You have been securely logged out of your chat application account. 
                    Thank you for using our service!
                </p>
                
                <!------------------- ACTION BUTTONS -------------------->
                <div class="d-grid gap-2">
                    <a href="login.php" class="btn btn-primary btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>
                        Sign In Again
                    </a>
                    
                    <a href="index.php" class="btn btn-outline-secondary btn-home">
                        <i class="fas fa-home me-2"></i>
                        Go to Homepage
                    </a>
                </div>
                
                <!------------------- SECURITY NOTE -------------------->
                <div class="mt-4">
                    <small class="text-muted">
                        <i class="fas fa-shield-alt me-1"></i>
                        Your session has been completely terminated for security.
                    </small>
                </div>
            </div>
        </div>
    </div>

    <script src="plugins/bootstrap-5.3.0/js/bootstrap.bundle.js"></script>
    
    <script>
        /////////// AUTO REDIRECT AFTER 10 SECONDS DELAY
        setTimeout(function() {
            window.location.href = 'login.php';
        }, 10000);
        
        /////////// PREVENT BACK BUTTON ACCESS
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.go(1);
        };
    </script>
</body>
</html>