<?php
/**
 * Application Configuration
 * Database settings and other configuration
 */

class Config {
    private static $config = [
        'db' => [
            'host' => 'localhost',
            'user' => 'root',
            'pass' => '',
            'name' => 'perijinan_siswa',
            'charset' => 'utf8mb4'
        ],
        'app' => [
            'name' => 'SISWA IZIN SYSTEM',
            'version' => '1.0.0',
            'timezone' => 'Asia/Jakarta'
        ]
    ];

    /**
     * Get configuration value
     * Usage: Config::get('db.host') or Config::get('db')
     */
    public static function get($key) {
        $keys = explode('.', $key);
        $value = self::$config;

        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return null;
            }
            $value = $value[$k];
        }

        return $value;
    }

    /**
     * Set configuration value
     */
    public static function set($key, $value) {
        $keys = explode('.', $key);
        $config = &self::$config;

        foreach ($keys as $k) {
            if (!isset($config[$k])) {
                $config[$k] = [];
            }
            $config = &$config[$k];
        }

        $config = $value;
    }
}
