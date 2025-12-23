<?php
/**
 * Fortigate Enable and Disable Static Route Application
 * Session Manager
 * Handles authentication and sessions
 */

class Session {
    public static function start() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $value) {
        self::start();
        $_SESSION[$key] = $value;
    }

    public static function get($key, $default = null) {
        self::start();
        return isset($_SESSION[$key]) ? $_SESSION[$key] : $default;
    }

    public static function isAuthenticated() {
        return self::get('authenticated', false) === true;
    }

    public static function login($username, $password) {
        require_once __DIR__ . '/config.php';

        $validUsername = Config::get('ADMIN_USERNAME');
        $validPassword = Config::get('ADMIN_PASSWORD');

        if ($username === $validUsername && $password === $validPassword) {
            self::set('authenticated', true);
            self::set('username', $username);
            return true;
        }

        return false;
    }

    public static function logout() {
        self::start();
        session_destroy();
        $_SESSION = [];
    }

    public static function requireLogin() {
        if (!self::isAuthenticated()) {
            header('Location: login.php');
            exit;
        }
    }

    public static function updatePassword($newPassword) {
        $envFile = __DIR__ . '/.env';
        
        if (!file_exists($envFile)) {
            return false;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES);
        $updated = false;

        foreach ($lines as &$line) {
            if (strpos($line, 'ADMIN_PASSWORD=') === 0) {
                $line = 'ADMIN_PASSWORD=' . $newPassword;
                $updated = true;
            }
        }

        if ($updated) {
            return file_put_contents($envFile, implode("\n", $lines)) !== false;
        }

        return false;
    }

    public static function setPasswordChangedFlag($changed) {
        $envFile = __DIR__ . '/.env';
        
        if (!file_exists($envFile)) {
            return false;
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES);
        $found = false;
        $value = $changed ? 'true' : 'false';

        foreach ($lines as &$line) {
            if (strpos($line, 'PASSWORD_CHANGED=') === 0) {
                $line = 'PASSWORD_CHANGED=' . $value;
                $found = true;
            }
        }

        // Add the line if not found
        if (!$found) {
            $lines[] = 'PASSWORD_CHANGED=' . $value;
        }

        return file_put_contents($envFile, implode("\n", $lines)) !== false;
    }
}
