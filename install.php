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
        pre { background: #020b18; padding: 15px; border-radius: 5px; overflow: auto; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Database Setup</h1>
        <pre>";

// Step 1: Check if we can include db.php
echo "Step 1: Loading database configuration...\n";
$db_file = __DIR__ . '/includes/db.php';
if (file_exists($db_file)) {
    echo "✓ Found db.php at: $db_file\n";
    require_once $db_file;
    echo "✓ db.php loaded successfully\n\n";
} else {
    die("✗ Cannot find db.php at: $db_file\n");
}

// Step 2: Check environment variables
echo "Step 2: Checking environment variables...\n";
$mysql_host = getenv('MYSQLHOST');
$mysql_port = getenv('MYSQLPORT');
$mysql_user = getenv('MYSQLUSER');
$mysql_pass = getenv('MYSQLPASSWORD');
$mysql_db = getenv('MYSQLDATABASE');

echo "MYSQLHOST: " . ($mysql_host ? "✓ $mysql_host" : "✗ NOT SET") . "\n";
echo "MYSQLPORT: " . ($mysql_port ? "✓ $mysql_port" : "✗ NOT SET") . "\n";
echo "MYSQLUSER: " . ($mysql_user ? "✓ $mysql_user" : "✗ NOT SET") . "\n";
echo "MYSQLDATABASE: " . ($mysql_db ? "✓ $mysql_db" : "✗ NOT SET") . "\n\n";

// Step 3: Try to connect
echo "Step 3: Connecting to database...\n";
try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    echo "✓ Connected successfully!\n\n";
} catch (Exception $e) {
    die("✗ Connection failed: " . $e->getMessage() . "\n");
}

// Step 4: Find SQL file (any .sql file)
echo "Step 4: Looking for SQL file...\n";
$sql_files = glob(__DIR__ . "/*.sql");
if (empty($sql_files)) {
    echo "✗ No SQL files found!\n";
    echo "Files in directory:\n";
    $files = scandir(__DIR__);
    foreach ($files as $f) {
        if (!is_dir($f) && pathinfo($f, PATHINFO_EXTENSION) == 'sql') {
            echo "  - $f\n";
        }
    }
    die("\n");
}

$sql_file = $sql_files[0];
echo "✓ Found SQL file: " . basename($sql_file) . "\n";
echo "File size: " . filesize($sql_file) . " bytes\n\n";

// Step 5: Read SQL file
echo "Step 5: Reading SQL file...\n";
$sql_content = file_get_contents($sql_file);
echo "✓ Read " . strlen($sql_content) . " characters\n\n";

// Step 6: Create tables
echo "Step 6: Creating tables...\n";
// Split SQL by semicolons
$statements = explode(";", $sql_content);
$count = 0;
$errors = [];

foreach ($statements as $stmt) {
    $stmt = trim($stmt);
    if (!empty($stmt) && !preg_match('/^--/', $stmt) && !preg_match('/^\/\*/', $stmt)) {
        // Skip statements that might cause issues
        if (strpos($stmt, 'DROP DATABASE') === false && 
            strpos($stmt, 'CREATE DATABASE') === false &&
            strpos($stmt, 'USE ') === false &&
            strpos($stmt, 'SET SQL_MODE') === false &&
            strpos($stmt, 'SET time_zone') === false &&
            strpos($stmt, 'START TRANSACTION') === false &&
            strpos($stmt, 'COMMIT') === false &&
            strpos($stmt, '/*!') === false) {
            
            if ($conn->query($stmt) === TRUE) {
                $count++;
                if ($count <= 10) { // Show first 10 only
                    echo "  ✓ " . substr($stmt, 0, 60) . "...\n";
                }
            } else {
                // Ignore "already exists" errors
                if (strpos($conn->error, 'already exists') === false) {
                    $errors[] = $conn->error;
                    echo "  ⚠ " . $conn->error . "\n";
                }
            }
        }
    }
}

echo "\n✓ Executed $count statements\n";
if (!empty($errors)) {
    echo "⚠️ " . count($errors) . " warnings (may be normal if tables already exist)\n\n";
} else {
    echo "\n";
}

// Step 7: Verify tables
echo "Step 7: Verifying tables...\n";
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
    echo "<span class='success'>🎉 Your app is ready to use!</span>\n";
    
    // Check if sample data exists
    $result = $conn->query("SELECT COUNT(*) as count FROM phishing_keywords");
    if ($result) {
        $row = $result->fetch_assoc();
        if ($row['count'] == 0) {
            echo "\n<span class='info'>📝 Adding sample data...</span>\n";
            // Add sample keywords
            $keywords = [
                ['login', 8, 'auth'], ['verify', 10, 'auth'], ['password', 10, 'auth'],
                ['secure', 6, 'security'], ['account', 5, 'auth'], ['paypal', 8, 'brand']
            ];
            $stmt = $conn->prepare("INSERT IGNORE INTO phishing_keywords (keyword, weight, category) VALUES (?, ?, ?)");
            foreach ($keywords as $kw) {
                $stmt->bind_param("sis", $kw[0], $kw[1], $kw[2]);
                $stmt->execute();
            }
            echo "  ✓ Added sample keywords\n";
        }
    }
} else {
    echo "\n<span class='error'>⚠ Missing tables: " . implode(', ', $missing) . "</span>\n";
}

echo "</pre>
        <a href='/' style='display:inline-block; background:#00f5ff; color:#020b18; padding:10px 20px; text-decoration:none; margin-top:20px;'>Go to App →</a>
        <a href='debug.php' style='display:inline-block; background:#00f5ff33; color:#00f5ff; padding:10px 20px; text-decoration:none; margin-top:20px; margin-left:10px;'>Debug →</a>
    </div>
</body>
</html>";
?>
