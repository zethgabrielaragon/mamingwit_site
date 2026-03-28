<?php
// ============================================================
// MAMINGWIT CHECKER - Database Configuration
// ============================================================

define('DB_HOST', getenv('DB_HOST') ?: 'db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: 'root');
define('DB_NAME', getenv('DB_NAME') ?: 'mamingwit_db');
define('DB_PORT', getenv('DB_PORT') ?: 3306);

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        $this->connection = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        if ($this->connection->connect_error) {
            die(json_encode([
                'error' => true,
                'message' => 'Database connection failed: ' . $this->connection->connect_error
            ]));
        }
        $this->connection->set_charset('utf8mb4');
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql) {
        $result = $this->connection->query($sql);
        if ($this->connection->error) {
            error_log('DB Error: ' . $this->connection->error . ' | SQL: ' . $sql);
        }
        return $result;
    }

    public function prepare($sql) {
        return $this->connection->prepare($sql);
    }

    public function escape($value) {
        return $this->connection->real_escape_string($value);
    }

    public function getLastId() {
        return $this->connection->insert_id;
    }

    public function getAffectedRows() {
        return $this->connection->affected_rows;
    }
}
?>