<?php
// ============================================================
// MAMINGWIT CHECKER - Database Configuration
// ============================================================

define('DB_HOST', getenv('MYSQL_HOST') ?: 'localhost');
define('DB_USER', getenv('MYSQL_USER') ?: 'root');
define('DB_PASS', getenv('MYSQL_PASSWORD') ?: '');
define('DB_NAME', getenv('MYSQL_DATABASE') ?: 'mamingwit_db');
define('DB_PORT', getenv('MYSQL_PORT') ?: 3306);

class Database {
    private static $instance = null;
    private $connection;
    private $connection_error = false;

    private function __construct() {
        // Create connection with error suppression
        $this->connection = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);
        
        // Check connection
        if ($this->connection->connect_error) {
            error_log('Database connection failed: ' . $this->connection->connect_error);
            $this->connection_error = true;
            // Don't die - let the app show a friendly error
        } else {
            $this->connection->set_charset('utf8mb4');
        }
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
    
    public function isConnected() {
        return !$this->connection_error && $this->connection && !$this->connection->connect_error;
    }

    public function query($sql) {
        if (!$this->isConnected()) {
            return false;
        }
        return $this->connection->query($sql);
    }

    public function prepare($sql) {
        if (!$this->isConnected()) {
            return false;
        }
        return $this->connection->prepare($sql);
    }
}
?>
