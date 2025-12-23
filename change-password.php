<?php
// ALL PHP LOGIC MUST BE HERE BEFORE ANY HTML OUTPUT
require_once 'session.php';
require_once 'config.php';

Session::start();

// Check if user is authenticated and on first login
if (!Session::isAuthenticated()) {
    header('Location: login.php');
    exit;
}

if (!Session::get('first_login', false)) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';
    $confirmPassword = isset($_POST['confirm_password']) ? $_POST['confirm_password'] : '';

    // Validation
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'All fields are required';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'New passwords do not match';
    } elseif (strlen($newPassword) < 8) {
        $error = 'Password must be at least 8 characters long';
    } else {
        // Verify current password
        $validPassword = Config::get('ADMIN_PASSWORD');
        if ($currentPassword !== $validPassword) {
            $error = 'Current password is incorrect';
        } else {
            // Update password in .env file
            $updated = Session::updatePassword($newPassword);
            if ($updated) {
                // Set PASSWORD_CHANGED flag in .env
                Session::setPasswordChangedFlag(true);
                // Clear first_login flag
                Session::set('first_login', false);
                Session::set('password_changed', true);
                
                $success = 'Password changed successfully! Redirecting to dashboard...';
            } else {
                $error = 'Failed to update password. Please try again.';
            }
        }
    }
}

$username = Session::get('username', 'User');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Firewall Internet Control</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header h1 {
            color: #333;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .badge-first-login {
            display: inline-block;
            background-color: #ff9800;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        input[type="password"],
        input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }

        input[type="password"]:focus,
        input[type="text"]:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .password-requirements {
            background-color: #f5f5f5;
            padding: 12px;
            border-radius: 5px;
            font-size: 13px;
            color: #666;
            margin-top: 10px;
            margin-bottom: 20px;
        }

        .requirements-list {
            list-style: none;
            margin-top: 8px;
        }

        .requirements-list li {
            padding: 4px 0;
            display: flex;
            align-items: center;
        }

        .requirements-list li:before {
            content: "✓";
            color: #4caf50;
            font-weight: bold;
            margin-right: 8px;
        }

        .requirements-list li.unmet:before {
            content: "✗";
            color: #f44336;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-submit {
            flex: 1;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .btn-logout {
            flex: 1;
            padding: 12px;
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .btn-logout:hover {
            background-color: #e0e0e0;
        }

        .error-message {
            background-color: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
            font-size: 14px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #155724;
            font-size: 14px;
            display: none;
        }

        .success-message.show {
            display: block;
        }

        .info-box {
            background-color: #e3f2fd;
            border-left: 4px solid #2196f3;
            padding: 12px;
            border-radius: 5px;
            font-size: 13px;
            color: #1565c0;
            margin-bottom: 20px;
        }

        @media (max-width: 480px) {
            .container {
                padding: 30px 20px;
            }

            .header h1 {
                font-size: 20px;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 Change Password</h1>
            <p>This is your first login</p>
            <span class="badge-first-login">⚠️ REQUIRED ACTION</span>
        </div>

        <?php if ($success): ?>
            <div class="success-message show"><?php echo htmlspecialchars($success); ?></div>
            <script>
                setTimeout(function() {
                    window.location.href = 'dashboard.php';
                }, 2000);
            </script>
        <?php endif; ?>

        <div class="info-box">
            <strong>👤 User:</strong> <?php echo htmlspecialchars($username); ?><br>
            <strong>ℹ️ Note:</strong> You must change your password before accessing the dashboard.
        </div>

        <?php if ($error): ?>
            <div class="error-message show"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="success-message show"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <form method="POST" id="changePasswordForm">
            <div class="form-group">
                <label for="current_password">Current Password</label>
                <input type="password" id="current_password" name="current_password" required autofocus>
            </div>

            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required>
                <div class="password-requirements">
                    <strong>Password Requirements:</strong>
                    <ul class="requirements-list">
                        <li class="req-length unmet">At least 8 characters</li>
                    </ul>
                </div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="button-group">
                <button type="submit" class="btn-submit" id="submitBtn">Change Password</button>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </form>
    </div>

    <script>
        const newPasswordInput = document.getElementById('new_password');
        const reqLength = document.querySelector('.req-length');
        const submitBtn = document.getElementById('submitBtn');

        // Real-time password validation
        newPasswordInput.addEventListener('input', function() {
            const value = this.value;
            
            if (value.length >= 8) {
                reqLength.classList.remove('unmet');
            } else {
                reqLength.classList.add('unmet');
            }

            // Enable/disable submit button
            const confirmPassword = document.getElementById('confirm_password').value;
            if (value.length >= 8 && value === confirmPassword) {
                submitBtn.disabled = false;
            }
        });

        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            if (newPassword === this.value && newPassword.length >= 8) {
                submitBtn.disabled = false;
            } else {
                submitBtn.disabled = true;
            }
        });

        // Form submission
        document.getElementById('changePasswordForm').addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (newPassword !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match');
                return;
            }

            if (newPassword.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Updating...';
        });
    </script>
</body>
</html>
