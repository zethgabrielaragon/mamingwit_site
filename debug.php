<?php
echo "<!DOCTYPE html>
<html>
<head>
    <title>Debug - Mamingwit Checker</title>
    <style>
        body { font-family: monospace; background: #0a0e27; color: #00f5ff; padding: 20px; }
        .success { color: #00e676; }
        .error { color: #ff1744; }
        .info { color: #ffd600; }
        pre { background: #020b18; padding: 15px; border-radius: 5px; overflow: auto; }
    </style>
</head>
<body>
    <h1>🔍 Mamingwit Checker - Debug Info</h1>
    <pre>";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "1. ENVIRONMENT VARIABLES\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "MYSQLHOST: " . (getenv('MYSQLHOST') ? getenv('MYSQLHOST') : 'NOT SET') . "\n";
echo "MYSQLPORT: " . (getenv('MYSQLPORT') ? getenv('MYSQLPORT') : 'NOT SET') . "\n";
echo "MYSQLUSER: " . (getenv('MYSQLUSER') ? getenv('MYSQLUSER') : 'NOT SET') . "\n";
echo "MYSQLPASSWORD: " . (getenv('MYSQLPASSWORD') ? '***SET***' : 'NOT SET') . "\n";
echo "MYSQLDATABASE: " . (getenv('MYSQLDATABASE') ? getenv('MYSQLDATABASE') : 'NOT SET') . "\n\n";

echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "2. ATTEMPT DIRECT MYSQL CONNECTION\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";

$host = getenv('MYSQLHOST');
$port = getenv('MYSQLPORT');
$user = getenv('MYSQLUSER');
$pass = getenv('MYSQLPASSWORD');
$db = getenv('MYSQLDATABASE');

if (!$host || !$port || !$user || !$db) {
    echo "<span class='error'>❌ Missing required environment variables!</span>\n";
    echo "Please make sure all MySQL variables are set in Railway.\n";
} else {
    echo "Attempting connection to: $host:$port\n";
    echo "Database: $db\n";
    echo "User: $user\n\n";
    
    // Try mysqli connection
    $conn = @new mysqli($host, $user, $pass, $db, $port);
    
    if ($conn->connect_error) {
        echo "<span class='error'>❌ MySQLi Connection Failed: " . $conn->connect_error . "</span>\n\n";
        
        // Try with different host (without port in host)
        echo "Trying alternative connection method...\n";
        $conn2 = @new mysqli($host, $user, $pass, $db);
        if ($conn2->connect_error) {
            echo "<span class='error'>❌ Alternative also failed: " . $conn2->connect_error . "</span>\n";
        } else {
            echo "<span class='success'>✓ Alternative connection successful!</span>\n";
            $conn2->close();
        }
        
        // Try PDO
        echo "\nTrying PDO connection...\n";
        try {
            $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
            echo "<span class='success'>✓ PDO connection successful!</span>\n";
        } catch (PDOException $e) {
            echo "<span class='error'>❌ PDO failed: " . $e->getMessage() . "</span>\n";
        }
        
    } else {
        echo "<span class='success'>✓ MySQLi Connection Successful!</span>\n";
        echo "Server info: " . $conn->server_info . "\n";
        
        // Test query
        $result = $conn->query("SELECT NOW() as now");
        if ($result) {
            $row = $result->fetch_assoc();
            echo "Server time: " . $row['now'] . "\n";
        }
        
        $conn->close();
    }
}

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "3. CHECK PHP EXTENSIONS\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "MySQLi extension: " . (extension_loaded('mysqli') ? '<span class="success">✓ Loaded</span>' : '<span class="error">✗ NOT Loaded</span>') . "\n";
echo "PDO MySQL extension: " . (extension_loaded('pdo_mysql') ? '<span class="success">✓ Loaded</span>' : '<span class="error">✗ NOT Loaded</span>') . "\n";
echo "JSON extension: " . (extension_loaded('json') ? '<span class="success">✓ Loaded</span>' : '<span class="error">✗ NOT Loaded</span>') . "\n";

echo "\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "4. NETWORK CHECK\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
if ($host) {
    echo "Pinging $host...\n";
    $ping = @fsockopen($host, $port, $errno, $errstr, 5);
    if ($ping) {
        echo "<span class='success'>✓ Can reach $host:$port</span>\n";
        fclose($ping);
    } else {
        echo "<span class='error'>✗ Cannot reach $host:$port - Error: $errstr ($errno)</span>\n";
    }
}

echo "</pre>
    <h3>What to do next:</h3>
    <ul>
        <li>If MySQLi extension is missing: Railway needs to enable it</li>
        <li>If connection fails: The MySQL service might not be accessible from PHP service</li>
        <li>If variables are missing: Add them to Railway variables</li>
    </ul>
    <a href='/' style='color: #00f5ff;'>Back to App</a>
</body>
</html>";
?>