<?php
/**
 * Database Connection Module
 * Mengelola koneksi ke database dengan prepared statements untuk keamanan
 */

require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $connection = null;

    private function __construct() {
        $this->connect();
    }

    /**
     * Singleton: Dapatkan instance Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Koneksi ke database
     */
    private function connect() {
        try {
            $this->connection = new mysqli(
                Config::get('db.host'),
                Config::get('db.user'),
                Config::get('db.pass'),
                Config::get('db.name')
            );

            if ($this->connection->connect_error) {
                throw new Exception('Database Connection Error: ' . $this->connection->connect_error);
            }

            // Set charset UTF-8
            $this->connection->set_charset(Config::get('db.charset'));
        } catch (Exception $e) {
            error_log($e->getMessage());
            die('Database connection failed. Please try again later.');
        }
    }

    /**
     * Dapatkan koneksi database
     */
    public function getConnection() {
        return $this->connection;
    }

    /**
     * Prepared statement execution (aman dari SQL injection)
     */
    public function query($sql, $params = [], $types = '') {
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            throw new Exception('Prepare Error: ' . $this->connection->error);
        }

        if (!empty($params)) {
            // Ensure types provided when binding parameters
            if (empty($types)) {
                throw new Exception('Database::query - bind types string is required when params are provided.');
            }
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        return $stmt;
    }

    /**
     * Fetch single row
     */
    public function fetchOne($sql, $params = [], $types = '') {
        $stmt = $this->query($sql, $params, $types);
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Fetch all rows
     */
    public function fetchAll($sql, $params = [], $types = '') {
        $stmt = $this->query($sql, $params, $types);
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get last insert ID
     */
    public function lastInsertId() {
        return $this->connection->insert_id;
    }

    /**
     * Get affected rows
     */
    public function affectedRows() {
        return $this->connection->affected_rows;
    }

    /**
     * Close connection
     */
    public function closeConnection() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
}
