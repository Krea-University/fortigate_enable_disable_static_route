<?php
// ALL PHP LOGIC MUST BE HERE BEFORE ANY HTML OUTPUT
require_once 'session.php';
require_once 'config.php';
require_once 'api.php';

Session::requireLogin();

$username = Session::get('username', 'User');
$devices = Config::getDevices();

// Handle logout
if (isset($_GET['logout'])) {
    Session::logout();
    header('Location: login.php');
    exit;
}

// Handle API calls
$apiResponse = null;
$actionMessage = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $deviceId = isset($_POST['device_id']) ? $_POST['device_id'] : '';

    if ($action && $deviceId) {
        $api = new FirewallAPI();
        $result = $api->setDeviceStatus($deviceId, $action);

        $actionMessage = [
            'device_id' => $deviceId,
            'action' => $action,
            'success' => $result['success'],
            'message' => $result['success'] ? 
                "Successfully set device {$deviceId} to {$action}" : 
                'Failed to update device: ' . ($result['error'] ?? 'Unknown error'),
            'code' => $result['httpCode']
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Firewall Internet Control</title>
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
        }

        .btn-logout:hover {
            background-color: rgba(255, 255, 255, 0.3);
        }

        .container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 0 20px;
        }

        .dashboard-header {
            margin-bottom: 30px;
        }

        .dashboard-header h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .dashboard-header p {
            color: #666;
            font-size: 14px;
        }

        .devices-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .device-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            transition: box-shadow 0.3s, transform 0.3s;
        }

        .device-card:hover {
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .device-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 15px;
        }

        .device-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        .device-id {
            background-color: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .device-description {
            color: #666;
            font-size: 14px;
            margin-bottom: 20px;
            line-height: 1.5;
        }

        .device-status {
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            font-weight: 600;
        }

        .status-enable {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .status-disable {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .status-loading {
            background-color: #e2e3e5;
            color: #383d41;
            border: 1px solid #d6d8db;
        }

        .device-actions {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        .btn-device {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-device:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-device:active {
            transform: translateY(0);
        }

        .btn-enable {
            background-color: #28a745;
            color: white;
        }

        .btn-enable:hover {
            background-color: #218838;
        }

        .btn-disable {
            background-color: #dc3545;
            color: white;
        }

        .btn-disable:hover {
            background-color: #c82333;
        }

        .btn-device:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .device-message {
            font-size: 12px;
            padding: 8px;
            border-radius: 5px;
            margin-top: 10px;
            display: none;
        }

        .device-message.show {
            display: block;
        }

        .message-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .refresh-btn {
            padding: 8px 16px;
            background-color: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: background-color 0.3s;
        }

        .refresh-btn:hover {
            background-color: #764ba2;
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

            .dashboard-header h1 {
                font-size: 24px;
            }

            .devices-grid {
                grid-template-columns: 1fr;
            }

            .device-actions {
                flex-direction: column;
            }
        }

        .no-devices {
            text-align: center;
            padding: 40px 20px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-title">🔐 Firewall Internet Control</div>
        <div class="navbar-right">
            <div class="user-info">Welcome, <strong><?php echo htmlspecialchars($username); ?></strong></div>
            <a href="settings.php" class="btn-logout" style="background-color: rgba(102, 126, 234, 0.3); border-color: rgba(102, 126, 234, 0.5);">⚙️ Settings</a>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>
    </div>

    <div class="container">
        <div class="dashboard-header">
            <h1>Internet Management Dashboard</h1>
            <p>Manage and control internet access for lab devices</p>
        </div>

        <?php if (count($devices) === 0): ?>
            <div class="no-devices">
                <p>No devices configured. Please update the .env file.</p>
            </div>
        <?php else: ?>
            <div class="devices-grid">
                <?php foreach ($devices as $device): ?>
                    <div class="device-card">
                        <div class="device-header">
                            <div class="device-title"><?php echo htmlspecialchars($device['name']); ?></div>
                        </div>

                        <form method="POST" class="device-form-<?php echo htmlspecialchars($device['id']); ?>">
                            <div class="device-status status-loading" id="status-<?php echo htmlspecialchars($device['id']); ?>">Loading...</div>

                            <div class="device-actions" id="actions-<?php echo htmlspecialchars($device['id']); ?>">
                                <button type="submit" name="action" value="enable" class="btn-device btn-enable btn-enable-<?php echo htmlspecialchars($device['id']); ?>" style="display:none;">
                                    ✓ Enable
                                </button>
                                <button type="submit" name="action" value="disable" class="btn-device btn-disable btn-disable-<?php echo htmlspecialchars($device['id']); ?>" style="display:none;">
                                    ✗ Disable
                                </button>
                            </div>

                            <input type="hidden" name="device_id" value="<?php echo htmlspecialchars($device['id']); ?>">

                            <?php if ($actionMessage && $actionMessage['device_id'] == $device['id']): ?>
                                <div class="device-message show <?php echo $actionMessage['success'] ? 'message-success' : 'message-error'; ?>" id="message-<?php echo htmlspecialchars($device['id']); ?>">
                                    <?php echo htmlspecialchars($actionMessage['message']); ?>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        const devices = <?php echo json_encode(array_keys($devices)); ?>;

        // Fetch device status for all devices
        function fetchDeviceStatus(deviceId) {
            fetch(`api-device-status.php?device_id=${deviceId}`)
                .then(response => response.json())
                .then(data => {
                    updateDeviceUI(deviceId, data);
                })
                .catch(error => {
                    console.error(`Error fetching status for device ${deviceId}:`, error);
                    updateDeviceUI(deviceId, { success: false, error: 'Failed to load status' });
                });
        }

        // Update UI based on device status
        function updateDeviceUI(deviceId, data) {
            const statusDiv = document.getElementById(`status-${deviceId}`);
            const actionsDiv = document.getElementById(`actions-${deviceId}`);
            const enableBtn = document.querySelector(`.btn-enable-${deviceId}`);
            const disableBtn = document.querySelector(`.btn-disable-${deviceId}`);

            if (!statusDiv) return;

            if (!data.success) {
                statusDiv.textContent = 'Error: Could not load status';
                statusDiv.className = 'device-status status-loading';
                // Show both buttons in error state
                enableBtn.style.display = 'block';
                disableBtn.style.display = 'block';
                return;
            }

            // Parse status from response - API returns data.results[0].status
            let currentStatus = 'unknown';
            try {
                if (data.data && data.data.results && data.data.results.length > 0) {
                    currentStatus = data.data.results[0].status.toLowerCase();
                } else if (data.data && data.data.status) {
                    // Fallback for different response format
                    currentStatus = data.data.status.toLowerCase();
                }
            } catch (e) {
                console.error('Error parsing status:', e);
            }

            // Update status display
            if (currentStatus === 'enable' || currentStatus === 'enabled') {
                statusDiv.textContent = '✓ Internet: ENABLED';
                statusDiv.className = 'device-status status-enable';
                // Show only disable button
                enableBtn.style.display = 'none';
                disableBtn.style.display = 'block';
            } else if (currentStatus === 'disable' || currentStatus === 'disabled') {
                statusDiv.textContent = '✗ Internet: DISABLED';
                statusDiv.className = 'device-status status-disable';
                // Show only enable button
                enableBtn.style.display = 'block';
                disableBtn.style.display = 'none';
            } else {
                statusDiv.textContent = 'Status: Unknown';
                statusDiv.className = 'device-status status-loading';
                // Show both buttons if unknown
                enableBtn.style.display = 'block';
                disableBtn.style.display = 'block';
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Fetch status for all devices
            devices.forEach(deviceId => {
                fetchDeviceStatus(deviceId);
            });

            // Clear messages after 5 seconds and refresh status
            const messages = document.querySelectorAll('.device-message.show');
            messages.forEach(msg => {
                setTimeout(() => {
                    msg.classList.remove('show');
                }, 5000);
            });

            // Handle form submissions
            document.querySelectorAll('[class*="device-form-"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const deviceId = this.querySelector('[name="device_id"]').value;
                    const statusDiv = document.getElementById(`status-${deviceId}`);
                    if (statusDiv) {
                        statusDiv.textContent = 'Updating...';
                        statusDiv.className = 'device-status status-loading';
                    }
                    
                    // Refresh status after 2 seconds
                    setTimeout(() => {
                        fetchDeviceStatus(deviceId);
                    }, 2000);
                });
            });

            // Auto-refresh status every 30 seconds
            setInterval(function() {
                devices.forEach(deviceId => {
                    fetchDeviceStatus(deviceId);
                });
            }, 30000);
        });
    </script>
</body>
</html>
