<?php
echo "<pre>";
echo "Files in current directory:\n";
echo "==========================\n\n";

$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo "📄 " . $file;
        if (is_dir($file)) echo " (directory)";
        echo "\n";
    }
}

echo "\n\nLooking for SQL files:\n";
echo "==========================\n";
$sql_files = glob("*.sql");
if (empty($sql_files)) {
    echo "❌ No .sql files found!\n";
    echo "Make sure mamingwit_db.sql is in your GitHub repo root.\n";
} else {
    foreach ($sql_files as $sql) {
        echo "✓ Found: " . $sql . " (" . filesize($sql) . " bytes)\n";
    }
}
echo "</pre>";
?>