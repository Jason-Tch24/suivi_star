<?php
/**
 * Setup Google Authentication
 * Run this script to install dependencies and update database for Google OAuth
 */

require_once __DIR__ . '/src/models/Database.php';

echo "Setting up Google Authentication for STAR System...\n\n";

// Step 1: Install Composer dependencies
echo "1. Installing Composer dependencies...\n";
if (!file_exists('vendor/autoload.php')) {
    if (shell_exec('which composer') === null) {
        echo "   Error: Composer is not installed. Please install Composer first.\n";
        echo "   Visit: https://getcomposer.org/download/\n";
        exit(1);
    }
    
    $output = shell_exec('composer install 2>&1');
    if (strpos($output, 'error') !== false || strpos($output, 'failed') !== false) {
        echo "   Error installing dependencies:\n";
        echo "   $output\n";
        exit(1);
    }
    echo "   ✓ Dependencies installed successfully\n";
} else {
    echo "   ✓ Dependencies already installed\n";
}

// Step 2: Update database schema
echo "\n2. Updating database schema...\n";
try {
    $db = Database::getInstance();

    // Check and add Google fields individually
    $fieldsToAdd = [
        'google_id' => "ALTER TABLE users ADD COLUMN google_id VARCHAR(255) NULL UNIQUE AFTER id",
        'auth_provider' => "ALTER TABLE users ADD COLUMN auth_provider ENUM('local', 'google') DEFAULT 'local' AFTER password_hash",
        'google_avatar_url' => "ALTER TABLE users ADD COLUMN google_avatar_url VARCHAR(500) NULL AFTER profile_image"
    ];

    foreach ($fieldsToAdd as $field => $sql) {
        try {
            // Check if column exists
            $result = $db->query("SHOW COLUMNS FROM users LIKE '$field'");
            if ($result->fetch() === false) {
                $db->query($sql);
                echo "   ✓ Added column: $field\n";
            } else {
                echo "   - Column already exists: $field\n";
            }
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                echo "   - Column already exists: $field\n";
            } else {
                throw $e;
            }
        }
    }

    // Make password_hash nullable
    try {
        $db->query("ALTER TABLE users MODIFY COLUMN password_hash VARCHAR(255) NULL");
        echo "   ✓ Made password_hash nullable\n";
    } catch (Exception $e) {
        echo "   - Password hash already nullable or error: " . $e->getMessage() . "\n";
    }

    // Add indexes
    $indexesToAdd = [
        'idx_users_google_id' => "CREATE INDEX idx_users_google_id ON users(google_id)",
        'idx_users_auth_provider' => "CREATE INDEX idx_users_auth_provider ON users(auth_provider)"
    ];

    foreach ($indexesToAdd as $indexName => $sql) {
        try {
            // Check if index exists
            $result = $db->query("SHOW INDEX FROM users WHERE Key_name = '$indexName'");
            if ($result->fetch() === false) {
                $db->query($sql);
                echo "   ✓ Added index: $indexName\n";
            } else {
                echo "   - Index already exists: $indexName\n";
            }
        } catch (Exception $e) {
            if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
                echo "   - Index already exists: $indexName\n";
            } else {
                throw $e;
            }
        }
    }

    echo "   ✓ Database schema updated successfully\n";

} catch (Exception $e) {
    echo "   Error updating database: " . $e->getMessage() . "\n";
    exit(1);
}

// Step 3: Configuration instructions
echo "\n3. Configuration required:\n";
echo "   Please add the following to your .env file or environment variables:\n\n";
echo "   GOOGLE_CLIENT_ID=your_google_client_id_here\n";
echo "   GOOGLE_CLIENT_SECRET=your_google_client_secret_here\n\n";
echo "   To get these credentials:\n";
echo "   1. Go to https://console.developers.google.com/\n";
echo "   2. Create a new project or select existing one\n";
echo "   3. Enable the Google+ API\n";
echo "   4. Create OAuth 2.0 credentials\n";
echo "   5. Add your domain to authorized origins\n";
echo "   6. Add the callback URL: " . ($_ENV['APP_URL'] ?? 'http://localhost/suivie_star') . "/auth/google/callback.php\n\n";

echo "✓ Google Authentication setup completed!\n";
echo "\nNext steps:\n";
echo "1. Configure your Google OAuth credentials\n";
echo "2. Test the login functionality\n";
echo "3. Users can now register and login with Google\n\n";
?>
