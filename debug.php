<?php
echo "<h1>Debug Information</h1>";

// Check PHP version
echo "<h2>PHP Information</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";

// Check if mod_rewrite is enabled
echo "<h2>Apache Modules</h2>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    if (in_array('mod_rewrite', $modules)) {
        echo "mod_rewrite: ENABLED<br>";
    } else {
        echo "mod_rewrite: DISABLED<br>";
    }
    echo "Available modules: " . implode(', ', $modules) . "<br>";
} else {
    echo "Cannot check Apache modules (not running under Apache or function not available)<br>";
}

// Check file permissions
echo "<h2>File Permissions</h2>";
$files_to_check = ['.', 'index.php', 'setup.php', 'config', 'src', 'public'];
foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $perms = fileperms($file);
        echo "$file: " . substr(sprintf('%o', $perms), -4) . "<br>";
    } else {
        echo "$file: NOT FOUND<br>";
    }
}

// Check error reporting
echo "<h2>Error Reporting</h2>";
echo "Error Reporting Level: " . error_reporting() . "<br>";
echo "Display Errors: " . (ini_get('display_errors') ? 'ON' : 'OFF') . "<br>";
echo "Log Errors: " . (ini_get('log_errors') ? 'ON' : 'OFF') . "<br>";
echo "Error Log: " . ini_get('error_log') . "<br>";

// Test database connection
echo "<h2>Database Connection</h2>";
try {
    // Load environment variables
    if (file_exists('.env')) {
        $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
    
    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $port = $_ENV['DB_PORT'] ?? '8889';
    $username = $_ENV['DB_USERNAME'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? 'root';
    
    $dsn = "mysql:host={$host};port={$port};charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    
    echo "Database connection: SUCCESS<br>";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'star_volunteer_system'");
    if ($stmt->rowCount() > 0) {
        echo "Database 'star_volunteer_system': EXISTS<br>";
    } else {
        echo "Database 'star_volunteer_system': NOT EXISTS<br>";
    }
    
} catch (Exception $e) {
    echo "Database connection: ERROR - " . $e->getMessage() . "<br>";
}

// Check if .htaccess exists
echo "<h2>.htaccess Status</h2>";
if (file_exists('.htaccess')) {
    echo ".htaccess: EXISTS<br>";
    echo "Content:<br><pre>" . htmlspecialchars(file_get_contents('.htaccess')) . "</pre>";
} else {
    echo ".htaccess: NOT FOUND<br>";
}

echo "<h2>Current Directory Contents</h2>";
$files = scandir('.');
foreach ($files as $file) {
    if ($file != '.' && $file != '..') {
        echo $file . (is_dir($file) ? '/' : '') . "<br>";
    }
}
?>
