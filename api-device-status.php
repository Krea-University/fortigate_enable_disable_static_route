<?php
/**
 * API endpoint for fetching device status
 * Returns JSON with current device internet status
 */
require_once 'session.php';
require_once 'config.php';
require_once 'api.php';

header('Content-Type: application/json');

Session::requireLogin();

$deviceId = isset($_GET['device_id']) ? $_GET['device_id'] : null;

if (!$deviceId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Device ID required']);
    exit;
}

$api = new FirewallAPI();
$result = $api->getDeviceStatus($deviceId);

// Parse the status from nested results array
$status = 'unknown';
if ($result['success'] && isset($result['data']['results']) && is_array($result['data']['results']) && count($result['data']['results']) > 0) {
    $status = $result['data']['results'][0]['status'] ?? 'unknown';
}

echo json_encode([
    'success' => $result['success'],
    'device_id' => $deviceId,
    'data' => $result['data'],
    'status' => $status,
    'error' => $result['error'] ?? null,
    'httpCode' => $result['httpCode']
]);
