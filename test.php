<?php
echo "PHP is working!<br>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Current directory: " . __DIR__ . "<br>";

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

// Test database connection with MAMP settings
try {
    $host = $_ENV['DB_HOST'] ?? '127.0.0.1';
    $port = $_ENV['DB_PORT'] ?? '8889';
    $username = $_ENV['DB_USERNAME'] ?? 'root';
    $password = $_ENV['DB_PASSWORD'] ?? 'root';

    echo "Connecting to: {$host}:{$port} with user: {$username}<br>";

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
        echo "Database 'star_volunteer_system' exists<br>";
    } else {
        echo "Database 'star_volunteer_system' does not exist - will be created during setup<br>";
    }

} catch (Exception $e) {
    echo "Database connection error: " . $e->getMessage() . "<br>";
}
?>
