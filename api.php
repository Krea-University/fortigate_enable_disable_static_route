<?php
/**
 * Fortigate Enable and Disable Static Route Application
 * API Client
 * Handles communication with Firewall API
 */

class FirewallAPI {
    private $baseUrl;
    private $token;
    private $timeout;
    private $sslVerify;

    public function __construct() {
        require_once __DIR__ . '/config.php';

        $this->baseUrl = Config::get('API_BASE_URL');
        $this->token = Config::get('API_BEARER_TOKEN');
        $this->timeout = (int)Config::get('API_TIMEOUT', 30);
        $this->sslVerify = Config::get('SSL_VERIFY') === 'true' ? true : false;
    }

    /**
     * Get device status
     */
    public function getDeviceStatus($deviceId) {
        $endpoint = Config::get('API_ENDPOINT') . $deviceId;
        return $this->makeRequest('GET', $endpoint);
    }

    /**
     * Enable/Disable internet for device
     */
    public function setDeviceStatus($deviceId, $status) {
        $endpoint = Config::get('API_ENDPOINT') . $deviceId;
        $data = json_encode([
            "seq-num" => $deviceId,
            "status" => strtolower($status) === 'enable' ? 'enable' : 'disable'
        ]);

        return $this->makeRequest('PUT', $endpoint, $data);
    }

    /**
     * Make HTTP request to API
     */
    private function makeRequest($method, $endpoint, $data = null) {
        $url = $this->baseUrl . $endpoint;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $this->sslVerify);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, $this->sslVerify ? 2 : 0);

        $headers = [
            'Authorization: Bearer ' . $this->token,
            'Content-Type: application/json'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);

        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'error' => $error,
                'httpCode' => $httpCode
            ];
        }

        return [
            'success' => $httpCode >= 200 && $httpCode < 300,
            'data' => json_decode($response, true),
            'httpCode' => $httpCode,
            'raw' => $response
        ];
    }
}
