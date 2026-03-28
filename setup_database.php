<?php
// Run this once to set up the database schema on Railway
require_once __DIR__ . '/includes/db.php';

echo "Setting up Mamingwit Checker database...\n";

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    // Read SQL file
    $sql = file_get_contents(__DIR__ . '/mamingwit_db.sql');
    
    // Split SQL statements (basic splitting - handles simple cases)
    $statements = explode(";\n", $sql);
    
    $count = 0;
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^--/', $statement)) {
            // Skip certain statements that might fail
            if (strpos($statement, 'DROP DATABASE') === false && 
                strpos($statement, 'CREATE DATABASE') === false &&
                strpos($statement, 'USE ') === false) {
                if ($conn->query($statement) === TRUE) {
                    $count++;
                    echo "✓ Executed statement\n";
                } else {
                    echo "⚠ Warning: " . $conn->error . "\n";
                }
            }
        }
    }
    
    echo "\n✅ Database setup complete! $count statements executed.\n";
    echo "Database: " . DB_NAME . "\n";
    echo "Host: " . DB_HOST . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>