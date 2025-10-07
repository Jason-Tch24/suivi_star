<?php
/**
 * Database and Model Testing - STAR System
 */

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

require_once __DIR__ . '/src/models/Database.php';
require_once __DIR__ . '/src/models/User.php';
require_once __DIR__ . '/src/models/Aspirant.php';
require_once __DIR__ . '/src/models/Ministry.php';

echo "<h1>Database and Model Testing</h1>";

// Test Database Connection
echo "<h2>1. Database Connection Test</h2>";
try {
    $db = Database::getInstance();
    echo "✅ Database connection successful<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
    exit;
}

// Test User Model
echo "<h2>2. User Model Test</h2>";
try {
    $userModel = new User();
    
    // Test getting a user
    $testUser = $userModel->findByEmail('admin@star-church.org');
    if ($testUser) {
        echo "✅ User model working - Found admin user<br>";
        echo "User ID: " . $testUser['id'] . "<br>";
        echo "Name: " . $testUser['first_name'] . " " . $testUser['last_name'] . "<br>";
    } else {
        echo "⚠️ Admin user not found - database may need setup<br>";
    }
    
    // Test email uniqueness check
    $isUnique = $userModel->isEmailUnique('nonexistent@example.com');
    echo $isUnique ? "✅ Email uniqueness check working<br>" : "❌ Email uniqueness check failed<br>";
    
} catch (Exception $e) {
    echo "❌ User model error: " . $e->getMessage() . "<br>";
}

// Test Ministry Model
echo "<h2>3. Ministry Model Test</h2>";
try {
    $ministryModel = new Ministry();
    $ministries = $ministryModel->getAll('active');
    
    if (!empty($ministries)) {
        echo "✅ Ministry model working - Found " . count($ministries) . " ministries<br>";
        foreach ($ministries as $ministry) {
            echo "- " . htmlspecialchars($ministry['name']) . "<br>";
        }
    } else {
        echo "⚠️ No ministries found - database may need setup<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Ministry model error: " . $e->getMessage() . "<br>";
}

// Test Aspirant Model
echo "<h2>4. Aspirant Model Test</h2>";
try {
    $aspirantModel = new Aspirant();
    
    // Test getting aspirants
    $aspirants = $aspirantModel->getAll(5);
    echo "✅ Aspirant model working - Found " . count($aspirants) . " aspirants<br>";
    
} catch (Exception $e) {
    echo "❌ Aspirant model error: " . $e->getMessage() . "<br>";
}

// Test Form Processing
echo "<h2>5. Form Processing Test</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>POST Data Received:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    // Test user creation
    if (isset($_POST['test_user'])) {
        try {
            $userModel = new User();
            $testEmail = 'formtest_' . time() . '@example.com';
            
            $userId = $userModel->create([
                'email' => $testEmail,
                'password' => 'testpass123',
                'first_name' => 'Form',
                'last_name' => 'Test',
                'role' => 'aspirant',
                'status' => 'active'
            ]);
            
            echo "✅ User creation successful - ID: $userId<br>";
            
            // Clean up test user
            $userModel->delete($userId);
            echo "✅ Test user cleaned up<br>";
            
        } catch (Exception $e) {
            echo "❌ User creation failed: " . $e->getMessage() . "<br>";
        }
    }
} else {
    echo "<form method='POST'>";
    echo "<input type='hidden' name='test_user' value='1'>";
    echo "<button type='submit' style='padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;'>Test Form Submission</button>";
    echo "</form>";
}

// Test Session
echo "<h2>6. Session Test</h2>";
session_start();
if (isset($_SESSION['user_id'])) {
    echo "✅ Session active - User ID: " . $_SESSION['user_id'] . "<br>";
} else {
    echo "ℹ️ No active session<br>";
}

// Test Authentication
echo "<h2>7. Authentication Test</h2>";
try {
    require_once __DIR__ . '/src/middleware/Auth.php';
    
    if (Auth::check()) {
        $user = Auth::user();
        echo "✅ User authenticated: " . $user['first_name'] . " " . $user['last_name'] . "<br>";
        echo "Role: " . $user['role'] . "<br>";
    } else {
        echo "ℹ️ No user authenticated<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Authentication error: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Back to Home</a> | <a href='login.php'>Login</a> | <a href='register.php'>Register</a></p>";
?>
