<?php
// ALL PHP LOGIC MUST BE HERE BEFORE ANY HTML OUTPUT
require_once 'session.php';
require_once 'config.php';

Session::requireLogin();

$username = Session::get('username', 'User');
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
                $success = 'Password changed successfully!';
                // Clear form
                $currentPassword = '';
                $newPassword = '';
                $confirmPassword = '';
            } else {
                $error = 'Failed to update password. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - Firewall Internet Control</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            min-height: 100vh;
        }

        .navbar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            flex-wrap: wrap;
            gap: 10px;
        }

        .navbar-title {
            font-size: 20px;
            font-weight: 600;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .user-info {
            font-size: 14px;
        }

        .btn-logout {
            padding: 8px 16px;
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid white;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-logout:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .breadcrumb {
            margin-bottom: 20px;
            font-size: 14px;
        }

        .breadcrumb a {
            color: #667eea;
            text-decoration: none;
            margin-right: 10px;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .settings-header {
            margin-bottom: 30px;
        }

        .settings-header h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .settings-header p {
            color: #666;
            font-size: 14px;
        }

        .settings-card {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }

        .settings-card h2 {
            color: #333;
            font-size: 20px;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
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

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-submit {
            padding: 12px 30px;
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

        .btn-cancel {
            padding: 12px 30px;
            background-color: #f5f5f5;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s;
        }

        .btn-cancel:hover {
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

        .password-requirements {
            background-color: #f5f5f5;
            padding: 12px;
            border-radius: 5px;
            font-size: 13px;
            color: #666;
            margin-top: 10px;
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

        @media (max-width: 768px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .navbar-right {
                width: 100%;
                justify-content: space-between;
            }

            .settings-card {
                padding: 20px;
            }

            .button-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-title">⚙️ Settings</div>
        <div class="navbar-right">
            <div class="user-info">User: <strong><?php echo htmlspecialchars($username); ?></strong></div>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="breadcrumb">
            <a href="dashboard.php">← Back to Dashboard</a>
        </div>

        <div class="settings-header">
            <h1>Account Settings</h1>
            <p>Manage your account and password</p>
        </div>

        <div class="settings-card">
            <h2>🔐 Change Password</h2>

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
                    <button type="submit" class="btn-submit" id="submitBtn">Update Password</button>
                    <a href="dashboard.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
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

        // Clear success messages after 3 seconds
        const successMsg = document.querySelector('.success-message.show');
        if (successMsg) {
            setTimeout(() => {
                successMsg.style.display = 'none';
            }, 3000);
        }
    </script>
</body>
</html>
