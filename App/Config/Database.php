<?php
// Menggunakan __DIR__ untuk mendapatkan path file saat ini (Database.php)
require_once __DIR__ . '/config.php';

class Database {
    public static function connect() {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die('DB Connect Error: ' . $conn->connect_error);
        }
        return $conn;
    }
}
