<?php
// classes/Database.php
class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $config = require_once 'config/database.php';

        try {
            // Create connection based on preferred method (MySQLi or PDO)
            if ($config['type'] === 'mysqli') {
                $this->conn = new mysqli(
                    $config['host'],
                    $config['username'],
                    $config['password'],
                    $config['database']
                );

                if ($this->conn->connect_error) {
                    throw new Exception("Connection failed: " . $this->conn->connect_error);
                }
            } else {
                $dsn = "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4";
                $this->conn = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
            }
        } catch (Exception $e) {
            die("Database connection error: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    // Prevent cloning of the instance
    private function __clone() {}
}
