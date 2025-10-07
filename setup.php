<?php
/**
 * Database Setup Script for STAR Volunteer Management System
 */

// Load configuration
$config = require 'config/database.php';
$appConfig = require 'config/app.php';

$message = '';
$error = '';

if ($_POST['action'] ?? '' === 'setup') {
    try {
        // Connect to MySQL server (without database)
        $dbConfig = $config['connections']['mysql'];
        $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};charset={$dbConfig['charset']}";
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
        
        // Read and execute migration file
        $migrationFile = __DIR__ . '/database/migrations/001_create_star_database.sql';
        if (file_exists($migrationFile)) {
            $sql = file_get_contents($migrationFile);
            $pdo->exec($sql);
            $message .= "Database schema created successfully.<br>";
        }
        
        // Read and execute seed file
        $seedFile = __DIR__ . '/database/seeds/001_initial_data.sql';
        if (file_exists($seedFile)) {
            $sql = file_get_contents($seedFile);
            $pdo->exec($sql);
            $message .= "Initial data seeded successfully.<br>";
        }
        
        $message .= "<br><strong>Setup completed!</strong><br>";
        $message .= "Default login credentials:<br>";
        $message .= "Administrator: admin@star-church.org / password123<br>";
        $message .= "Pastor: pastor@star-church.org / password123<br>";
        $message .= "MDS: mds@star-church.org / password123<br>";
        
    } catch (Exception $e) {
        $error = "Setup failed: " . $e->getMessage();
    }
}

// Check if database exists
$dbExists = false;
try {
    $dbConfig = $config['connections']['mysql'];
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['database']};charset={$dbConfig['charset']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
    $dbExists = true;
} catch (Exception $e) {
    // Database doesn't exist or connection failed
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup - <?php echo $appConfig['name']; ?></title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        .message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            border: 1px solid #c3e6cb;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            border: 1px solid #f5c6cb;
        }
        .btn {
            background: #007bff;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-success {
            background: #28a745;
        }
        .btn-success:hover {
            background: #1e7e34;
        }
        .status {
            margin: 20px 0;
            padding: 15px;
            border-radius: 4px;
            background: #e9ecef;
        }
        .requirements {
            margin: 20px 0;
        }
        .requirement {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .check {
            color: #28a745;
            margin-right: 10px;
        }
        .cross {
            color: #dc3545;
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><?php echo $appConfig['name']; ?></h1>
        <h2>System Setup</h2>
        
        <?php if ($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <div class="requirements">
            <h3>System Requirements</h3>
            <div class="requirement">
                <span class="<?php echo version_compare(PHP_VERSION, '7.4.0', '>=') ? 'check' : 'cross'; ?>">
                    <?php echo version_compare(PHP_VERSION, '7.4.0', '>=') ? '✓' : '✗'; ?>
                </span>
                PHP 7.4+ (Current: <?php echo PHP_VERSION; ?>)
            </div>
            <div class="requirement">
                <span class="<?php echo extension_loaded('pdo') ? 'check' : 'cross'; ?>">
                    <?php echo extension_loaded('pdo') ? '✓' : '✗'; ?>
                </span>
                PDO Extension
            </div>
            <div class="requirement">
                <span class="<?php echo extension_loaded('pdo_mysql') ? 'check' : 'cross'; ?>">
                    <?php echo extension_loaded('pdo_mysql') ? '✓' : '✗'; ?>
                </span>
                PDO MySQL Extension
            </div>
        </div>
        
        <div class="status">
            <h3>Database Status</h3>
            <?php if ($dbExists): ?>
                <p><span class="check">✓</span> Database exists and is accessible</p>
                <p><a href="/" class="btn btn-success">Go to Application</a></p>
            <?php else: ?>
                <p><span class="cross">✗</span> Database not found or not accessible</p>
                <p>Click the button below to create the database and initial data:</p>
                
                <form method="post">
                    <input type="hidden" name="action" value="setup">
                    <button type="submit" class="btn">Setup Database</button>
                </form>
            <?php endif; ?>
        </div>
        
        <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #dee2e6;">
            <h3>Configuration</h3>
            <p><strong>Database:</strong> <?php echo $config['connections']['mysql']['database']; ?></p>
            <p><strong>Host:</strong> <?php echo $config['connections']['mysql']['host']; ?>:<?php echo $config['connections']['mysql']['port']; ?></p>
            <p><strong>Environment:</strong> <?php echo $appConfig['env']; ?></p>
        </div>
    </div>
</body>
</html>
