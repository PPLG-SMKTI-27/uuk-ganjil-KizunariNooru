<?php
/**
 * Database Connection Module
 * Mengelola koneksi ke database dengan prepared statements untuk keamanan
 */

require_once __DIR__ . '/config.php';

class Database {
    private static $instance = null;
    private $connection = null;
    private $lastAffectedRows = 0;
    private $lastInsertId = 0;

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
            throw new Exception('Prepare Error: ' . $this->connection->error . ' SQL: ' . $sql);
        }

        if (!empty($params)) {
            // Ensure types provided when binding parameters
            if (empty($types)) {
                throw new Exception('Database::query - bind types string is required when params are provided.');
            }
            $stmt->bind_param($types, ...$params);
        }

        $ok = $stmt->execute();
        if ($ok === false) {
            $err = $stmt->error ?: $this->connection->error;
            error_log('Database Execute Error: ' . $err . ' SQL: ' . $sql);
            throw new Exception('Execute Error: ' . $err . ' SQL: ' . $sql);
        }

        // Store affected rows and insert ID immediately after execute
        $this->lastAffectedRows = $this->connection->affected_rows;
        $this->lastInsertId = $this->connection->insert_id;

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
        return $this->lastInsertId;
    }

    /**
     * Get affected rows
     */
    public function affectedRows() {
        return $this->lastAffectedRows;
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
