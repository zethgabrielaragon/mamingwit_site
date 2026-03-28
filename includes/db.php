<?php
// ============================================================
// MAMINGWIT CHECKER - Database Configuration
// ============================================================

// Get Railway MySQL variables
$db_host = getenv('MYSQLHOST') ?: 'mysql.railway.internal';
$db_port = getenv('MYSQLPORT') ?: '3306';
$db_user = getenv('MYSQLUSER') ?: 'root';
$db_pass = getenv('MYSQLPASSWORD') ?: '';
$db_name = getenv('MYSQLDATABASE') ?: 'railway';

class Database {
    private static $instance = null;
    private $connection;

    private function __construct() {
        global $db_host, $db_port, $db_user, $db_pass, $db_name;
        
        // Create connection
        $this->connection = new mysqli($db_host, $db_user, $db_pass, $db_name, (int)$db_port);
        
        // Check connection
        if ($this->connection->connect_error) {
            error_log("Database connection failed: " . $this->connection->connect_error);
            throw new Exception('Database connection failed: ' . $this->connection->connect_error);
        }
        
        // Set charset
        $this->connection->set_charset('utf8mb4');
    }

    public static function getInstance() {
        if (self::$instance === null) {
            try {
                self::$instance = new Database();
            } catch (Exception $e) {
                error_log("Database instance creation failed: " . $e->getMessage());
                return null;
            }
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->connection;
    }

    public function query($sql) {
        if (!$this->connection) return false;
        $result = $this->connection->query($sql);
        if ($this->connection->error) {
            error_log('DB Error: ' . $this->connection->error . ' | SQL: ' . $sql);
        }
        return $result;
    }

    public function prepare($sql) {
        if (!$this->connection) return false;
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

// Database helper functions
function submitCommunityReport($url, $reason) {
    $db = Database::getInstance();
    if (!$db) return ['error' => true, 'message' => 'Database connection failed'];
    
    $conn = $db->getConnection();
    $url_hash = hash('sha256', $url);
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    
    $stmt = $conn->prepare("INSERT INTO community_reports (url_hash, url, reporter_ip, report_reason) VALUES (?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssss", $url_hash, $url, $ip, $reason);
        if ($stmt->execute()) {
            return ['success' => true, 'message' => 'Report submitted successfully'];
        }
        $stmt->close();
    }
    return ['success' => false, 'message' => 'Failed to submit report'];
}

function getURLHistory($limit = 25) {
    $db = Database::getInstance();
    if (!$db) return [];
    
    $conn = $db->getConnection();
    $result = $conn->query("SELECT * FROM url_checks ORDER BY last_checked DESC LIMIT $limit");
    $history = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $history[] = $row;
        }
    }
    return $history;
}

function getDashboardStats() {
    $db = Database::getInstance();
    if (!$db) return ['total_checked' => 0, 'high_risk' => 0, 'total_reports' => 0, 'repeated_urls' => 0];
    
    $conn = $db->getConnection();
    $stats = [];
    
    $result = $conn->query("SELECT COUNT(*) as total FROM url_checks");
    $stats['total_checked'] = $result ? $result->fetch_assoc()['total'] : 0;
    
    $result = $conn->query("SELECT COUNT(*) as total FROM url_checks WHERE risk_level = 'high'");
    $stats['high_risk'] = $result ? $result->fetch_assoc()['total'] : 0;
    
    $result = $conn->query("SELECT COUNT(*) as total FROM community_reports");
    $stats['total_reports'] = $result ? $result->fetch_assoc()['total'] : 0;
    
    $result = $conn->query("SELECT COUNT(*) as total FROM url_checks WHERE check_count > 1");
    $stats['repeated_urls'] = $result ? $result->fetch_assoc()['total'] : 0;
    
    return $stats;
}
?>
