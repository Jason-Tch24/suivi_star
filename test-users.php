<?php
/**
 * Test script to check and fix user authentication issues
 */

require_once __DIR__ . '/src/models/Database.php';
require_once __DIR__ . '/src/models/User.php';

try {
    $db = Database::getInstance();
    
    echo "<h1>🔍 User Authentication Test</h1>";
    echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 8px;'>";
    
    // Check all users in database
    echo "<h2>📋 All Users in Database:</h2>";
    $users = $db->fetchAll("SELECT id, email, first_name, last_name, role, status FROM users ORDER BY id");
    
    if (empty($users)) {
        echo "<div style='background: #fee; color: #c00; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ <strong>No users found in database!</strong><br>";
        echo "The database seeding may not have been executed properly.";
        echo "</div>";
        
        // Re-run the seeding
        echo "<h2>🌱 Re-seeding Database:</h2>";
        $seedFile = __DIR__ . '/database/seeds/001_initial_data.sql';
        if (file_exists($seedFile)) {
            $sql = file_get_contents($seedFile);
            $db->exec($sql);
            echo "<div style='background: #dfd; color: #060; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
            echo "✅ <strong>Database re-seeded successfully!</strong>";
            echo "</div>";
            
            // Re-fetch users
            $users = $db->fetchAll("SELECT id, email, first_name, last_name, role, status FROM users ORDER BY id");
        }
    }
    
    echo "<table border='1' style='border-collapse: collapse; width: 100%; margin: 10px 0;'>";
    echo "<tr style='background: #eee;'><th>ID</th><th>Email</th><th>Name</th><th>Role</th><th>Status</th></tr>";
    
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['first_name']} {$user['last_name']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "<td>{$user['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test authentication for each demo account
    echo "<h2>🔐 Authentication Tests:</h2>";
    
    $testAccounts = [
        ['email' => 'admin@star-church.org', 'password' => 'password123', 'role' => 'administrator'],
        ['email' => 'pastor@star-church.org', 'password' => 'password123', 'role' => 'pastor'],
        ['email' => 'mds@star-church.org', 'password' => 'password123', 'role' => 'mds'],
        ['email' => 'mentor1@star-church.org', 'password' => 'password123', 'role' => 'mentor'],
        ['email' => 'aspirant1@example.com', 'password' => 'password123', 'role' => 'aspirant']
    ];
    
    $userModel = new User();
    
    foreach ($testAccounts as $account) {
        echo "<div style='margin: 10px 0; padding: 10px; border: 1px solid #ccc; border-radius: 5px;'>";
        echo "<strong>Testing: {$account['email']} ({$account['role']})</strong><br>";
        
        $user = $userModel->authenticate($account['email'], $account['password']);
        
        if ($user) {
            echo "<div style='color: #060;'>✅ Authentication successful!</div>";
        } else {
            echo "<div style='color: #c00;'>❌ Authentication failed!</div>";
            
            // Check if user exists
            $existingUser = $db->fetch("SELECT * FROM users WHERE email = ?", [$account['email']]);
            if ($existingUser) {
                echo "<div style='color: #f60;'>⚠️ User exists but password verification failed</div>";
                echo "<div style='font-size: 12px; color: #666;'>Stored hash: " . substr($existingUser['password_hash'], 0, 20) . "...</div>";
                
                // Fix the password hash
                $correctHash = password_hash($account['password'], PASSWORD_DEFAULT);
                $db->query("UPDATE users SET password_hash = ? WHERE email = ?", [$correctHash, $account['email']]);
                echo "<div style='color: #060;'>🔧 Password hash updated!</div>";
                
                // Test again
                $user = $userModel->authenticate($account['email'], $account['password']);
                if ($user) {
                    echo "<div style='color: #060;'>✅ Authentication now works!</div>";
                } else {
                    echo "<div style='color: #c00;'>❌ Still failing after hash update</div>";
                }
            } else {
                echo "<div style='color: #c00;'>❌ User does not exist in database</div>";
            }
        }
        echo "</div>";
    }
    
    echo "<h2>✅ Test Complete</h2>";
    echo "<p><a href='login.php'>Go to Login Page</a></p>";
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #fee; color: #c00; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>❌ Error:</strong> " . $e->getMessage();
    echo "</div>";
}
?>
