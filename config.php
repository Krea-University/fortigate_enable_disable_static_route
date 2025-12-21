<?php
/**
 * Fortigate Enable and Disable Static Route Application
 * Configuration Loader
 * 
 * Loads environment variables from .env file for institutional deployment
 */

class Config {
    private static $config = [];
    private static $loaded = false;

    public static function load() {
        if (self::$loaded) {
            return;
        }

        $envFile = __DIR__ . '/.env';
        
        if (!file_exists($envFile)) {
            die('Error: .env file not found');
        }

        $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos($line, '#') === 0 || strpos($line, '=') === false) {
                continue;
            }

            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            self::$config[$key] = $value;
        }

        self::$loaded = true;
    }

    public static function get($key, $default = null) {
        if (!self::$loaded) {
            self::load();
        }

        return isset(self::$config[$key]) ? self::$config[$key] : $default;
    }

    public static function getDevices() {
        if (!self::$loaded) {
            self::load();
        }

        $devices = [];
        foreach (self::$config as $key => $value) {
            if (strpos($key, 'DEVICE_') === 0) {
                $id = str_replace('DEVICE_', '', $key);
                $parts = explode(':', $value);
                $devices[$id] = [
                    'id' => $id,
                    'name' => trim($parts[0]),
                    'description' => isset($parts[1]) ? trim($parts[1]) : '',
                ];
            }
        }
        return $devices;
    }
}

// Load configuration on include
Config::load();
