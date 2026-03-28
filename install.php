<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Setup - Mamingwit Checker</title>
    <style>
        body { 
            font-family: monospace; 
            background: #0a0e27; 
            color: #00f5ff; 
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #050f1c;
            padding: 20px;
            border-radius: 8px;
        }
        .success { color: #00e676; }
        .error { color: #ff1744; }
        .warning { color: #ffd600; }
        pre { background: #020b18; padding: 15px; border-radius: 5px; overflow: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Database Setup</h1>
        <pre>";

// Step 1: Load database config
echo "Step 1: Loading database configuration...\n";
require_once __DIR__ . '/includes/db.php';
echo "✓ db.php loaded successfully\n\n";

// Step 2: Check environment
echo "Step 2: Checking environment...\n";
$mysql_host = getenv('MYSQLHOST');
$mysql_port = getenv('MYSQLPORT');
$mysql_user = getenv('MYSQLUSER');
$mysql_pass = getenv('MYSQLPASSWORD');
$mysql_db = getenv('MYSQLDATABASE');

echo "MYSQLHOST: " . ($mysql_host ? "✓ $mysql_host" : "✗ NOT SET") . "\n";
echo "MYSQLPORT: " . ($mysql_port ? "✓ $mysql_port" : "✗ NOT SET") . "\n";
echo "MYSQLUSER: " . ($mysql_user ? "✓ $mysql_user" : "✗ NOT SET") . "\n";
echo "MYSQLDATABASE: " . ($mysql_db ? "✓ $mysql_db" : "✗ NOT SET") . "\n\n";

// Step 3: Connect to database
echo "Step 3: Connecting to database...\n";
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    echo "✓ Connected successfully!\n\n";
} catch (Exception $e) {
    die("✗ Connection failed: " . $e->getMessage() . "\n");
}

// Step 4: Create tables directly
echo "Step 4: Creating tables...\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Create blacklist table
$sql = "CREATE TABLE IF NOT EXISTS blacklist (
    id INT(11) NOT NULL AUTO_INCREMENT,
    domain VARCHAR(255) NOT NULL,
    reason VARCHAR(500) DEFAULT NULL,
    severity ENUM('medium','high','critical') DEFAULT 'high',
    added_at DATETIME DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (id),
    UNIQUE KEY domain (domain)
)";
if ($conn->query($sql)) {
    echo "✓ blacklist table created\n";
} else {
    echo "✗ Error creating blacklist: " . $conn->error . "\n";
}

// Create phishing_keywords table
$sql = "CREATE TABLE IF NOT EXISTS phishing_keywords (
    id INT(11) NOT NULL AUTO_INCREMENT,
    keyword VARCHAR(100) NOT NULL,
    weight INT(11) DEFAULT 10,
    category VARCHAR(50) DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY keyword (keyword)
)";
if ($conn->query($sql)) {
    echo "✓ phishing_keywords table created\n";
} else {
    echo "✗ Error creating phishing_keywords: " . $conn->error . "\n";
}

// Create url_checks table
$sql = "CREATE TABLE IF NOT EXISTS url_checks (
    id INT(11) NOT NULL AUTO_INCREMENT,
    url TEXT NOT NULL,
    url_hash VARCHAR(64) NOT NULL,
    domain VARCHAR(255) DEFAULT NULL,
    protocol VARCHAR(10) DEFAULT NULL,
    risk_score INT(11) DEFAULT 0,
    risk_level ENUM('low','medium','high') DEFAULT 'low',
    flags_triggered JSON DEFAULT NULL,
    is_https TINYINT(1) DEFAULT 0,
    url_length INT(11) DEFAULT 0,
    param_count INT(11) DEFAULT 0,
    uses_ip TINYINT(1) DEFAULT 0,
    has_phishing_keywords TINYINT(1) DEFAULT 0,
    suspicious_domain TINYINT(1) DEFAULT 0,
    check_count INT(11) DEFAULT 1,
    first_seen DATETIME DEFAULT CURRENT_TIMESTAMP(),
    last_checked DATETIME DEFAULT CURRENT_TIMESTAMP(),
    ip_address VARCHAR(45) DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_url_hash (url_hash),
    KEY idx_risk_level (risk_level)
)";
if ($conn->query($sql)) {
    echo "✓ url_checks table created\n";
} else {
    echo "✗ Error creating url_checks: " . $conn->error . "\n";
}

// Create community_reports table
$sql = "CREATE TABLE IF NOT EXISTS community_reports (
    id INT(11) NOT NULL AUTO_INCREMENT,
    url_hash VARCHAR(64) NOT NULL,
    url TEXT NOT NULL,
    reporter_ip VARCHAR(45) DEFAULT NULL,
    report_reason VARCHAR(255) DEFAULT NULL,
    reported_at DATETIME DEFAULT CURRENT_TIMESTAMP(),
    PRIMARY KEY (id),
    KEY idx_url_hash (url_hash)
)";
if ($conn->query($sql)) {
    echo "✓ community_reports table created\n";
} else {
    echo "✗ Error creating community_reports: " . $conn->error . "\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Step 5: Add sample data
echo "\nStep 5: Adding sample data...\n";

// Check if keywords exist
$result = $conn->query("SELECT COUNT(*) as count FROM phishing_keywords");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $keywords = [
        ['login', 8, 'auth'],
        ['verify', 10, 'auth'],
        ['update', 7, 'auth'],
        ['secure', 6, 'security'],
        ['account', 5, 'auth'],
        ['password', 10, 'auth'],
        ['confirm', 8, 'auth'],
        ['urgent', 8, 'pressure'],
        ['suspended', 9, 'pressure'],
        ['paypal', 8, 'brand'],
        ['amazon', 6, 'brand'],
        ['netflix', 6, 'brand'],
        ['apple', 5, 'brand'],
        ['google', 5, 'brand'],
        ['bank', 7, 'finance'],
        ['verify now', 10, 'pressure'],
        ['sign in', 7, 'auth']
    ];
    
    $stmt = $conn->prepare("INSERT INTO phishing_keywords (keyword, weight, category) VALUES (?, ?, ?)");
    foreach ($keywords as $kw) {
        $stmt->bind_param("sis", $kw[0], $kw[1], $kw[2]);
        $stmt->execute();
    }
    echo "✓ Added " . count($keywords) . " phishing keywords\n";
} else {
    echo "✓ Phishing keywords already exist (" . $row['count'] . " records)\n";
}

// Add sample blacklist if empty
$result = $conn->query("SELECT COUNT(*) as count FROM blacklist");
$row = $result->fetch_assoc();
if ($row['count'] == 0) {
    $blacklist = [
        ['paypa1.com', 'PayPal phishing clone', 'critical'],
        ['arnazon.com', 'Amazon phishing clone', 'critical'],
        ['g00gle.com', 'Google phishing clone', 'critical'],
        ['secure-login-portal.tk', 'Generic login phishing', 'high']
    ];
    
    $stmt = $conn->prepare("INSERT INTO blacklist (domain, reason, severity) VALUES (?, ?, ?)");
    foreach ($blacklist as $bl) {
        $stmt->bind_param("sss", $bl[0], $bl[1], $bl[2]);
        $stmt->execute();
    }
    echo "✓ Added " . count($blacklist) . " blacklist entries\n";
} else {
    echo "✓ Blacklist entries already exist (" . $row['count'] . " records)\n";
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

// Step 6: Verify all tables
echo "\nStep 6: Verifying tables...\n";
$result = $conn->query("SHOW TABLES");
$tables = [];
while ($row = $result->fetch_array()) {
    $tables[] = $row[0];
    echo "  ✓ " . $row[0] . "\n";
}

$required = ['url_checks', 'blacklist', 'phishing_keywords', 'community_reports'];
$missing = array_diff($required, $tables);

if (empty($missing)) {
    echo "\n<span class='success'>✅ DATABASE SETUP COMPLETE!</span>\n";
    echo "<span class='success'>🎉 Your Mamingwit Checker is ready to use!</span>\n";
    
    // Test API
    echo "\nTesting API...\n";
    echo "Visit: <a href='api.php?action=stats'>api.php?action=stats</a>\n";
} else {
    echo "\n<span class='error'>⚠ Missing tables: " . implode(', ', $missing) . "</span>\n";
}

echo "</pre>
        <a href='/' style='display:inline-block; background:#00f5ff; color:#020b18; padding:10px 20px; text-decoration:none; margin-top:20px;'>Go to App →</a>
        <a href='api.php?action=stats' style='display:inline-block; background:#00f5ff33; color:#00f5ff; padding:10px 20px; text-decoration:none; margin-top:20px; margin-left:10px;'>Test API →</a>
    </div>
</body>
</html>";
?>
