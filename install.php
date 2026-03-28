<?php
require_once __DIR__ . '/includes/db.php';

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Setup</title>
    <style>
        body { font-family: monospace; background: #0a0e27; color: #00f5ff; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; background: #050f1c; padding: 20px; border-radius: 8px; }
        .success { color: #00e676; }
        .error { color: #ff1744; }
        pre { background: #020b18; padding: 15px; border-radius: 5px; overflow: auto; }
        .button { display: inline-block; background: #00f5ff; color: #020b18; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class='container'>
        <h1>🔧 Database Setup</h1>
        <pre>";

echo "📊 Environment Check:\n";
echo "MYSQLHOST: " . (getenv('MYSQLHOST') ? '✓ ' . getenv('MYSQLHOST') : '✗ NOT SET') . "\n";
echo "MYSQLPORT: " . (getenv('MYSQLPORT') ? '✓ ' . getenv('MYSQLPORT') : '✗ NOT SET') . "\n";
echo "MYSQLUSER: " . (getenv('MYSQLUSER') ? '✓ ' . getenv('MYSQLUSER') : '✗ NOT SET') . "\n";
echo "MYSQLDATABASE: " . (getenv('MYSQLDATABASE') ? '✓ ' . getenv('MYSQLDATABASE') : '✗ NOT SET') . "\n\n";

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    echo "<span class='success'>✓ Connected to database successfully!</span>\n\n";
    
    // Read and execute SQL
    $sql = file_get_contents(__DIR__ . '/mamingwit_db.sql');
    $statements = explode(";\n", $sql);
    $count = 0;
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            if (strpos($statement, 'DROP DATABASE') === false && 
                strpos($statement, 'CREATE DATABASE') === false &&
                strpos($statement, 'USE ') === false) {
                if ($conn->query($statement) === TRUE) {
                    $count++;
                }
            }
        }
    }
    
    echo "<span class='success'>✅ Database tables created! ($count statements)</span>\n\n";
    
    // Verify tables
    $result = $conn->query("SHOW TABLES");
    echo "📋 Tables created:\n";
    while ($row = $result->fetch_array()) {
        echo "  ✓ " . $row[0] . "\n";
    }
    
    echo "\n<span class='success'>🎉 Setup complete! Your app is ready.</span>\n";
    
} catch (Exception $e) {
    echo "<span class='error'>❌ Error: " . $e->getMessage() . "</span>\n";
}

echo "</pre>
        <a href='/' class='button'>Go to App →</a>
    </div>
</body>
</html>";
?>