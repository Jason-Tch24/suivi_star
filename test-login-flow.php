<?php
/**
 * Test Login Flow - STAR System
 */

session_start();

// Load required files
require_once __DIR__ . '/src/middleware/Auth.php';

echo "<h1>Login Flow Test</h1>";

// Test 1: Check if user is already logged in
echo "<h2>1. Current Session Status</h2>";
if (Auth::check()) {
    $user = Auth::user();
    echo "✅ User is logged in: " . $user['first_name'] . " " . $user['last_name'] . " (" . $user['role'] . ")<br>";
    echo "<a href='dashboard.php' style='color: blue;'>Go to Dashboard</a> | ";
    echo "<a href='logout.php' style='color: red;'>Logout</a><br>";
} else {
    echo "ℹ️ No user logged in<br>";
}

// Test 2: Login form
echo "<h2>2. Login Test Form</h2>";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login_test'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        if (Auth::login($email, $password)) {
            $success = 'Login successful! Redirecting to dashboard...';
            echo "<script>setTimeout(function(){ window.location.href = 'dashboard.php'; }, 2000);</script>";
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

if ($error) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin: 10px 0;'>$error</div>";
}

if ($success) {
    echo "<div style='background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0;'>$success</div>";
}

if (!Auth::check()) {
    echo "<form method='POST' style='background: #f8f9fa; padding: 20px; border-radius: 8px; max-width: 400px;'>";
    echo "<input type='hidden' name='login_test' value='1'>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Email:</label>";
    echo "<input type='email' name='email' value='admin@star-church.org' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;' required>";
    echo "</div>";
    echo "<div style='margin-bottom: 15px;'>";
    echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Password:</label>";
    echo "<input type='password' name='password' value='password123' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;' required>";
    echo "</div>";
    echo "<button type='submit' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>Test Login</button>";
    echo "</form>";
}

// Test 3: Dashboard access test
echo "<h2>3. Dashboard Access Test</h2>";
if (Auth::check()) {
    echo "<a href='dashboard.php' style='background: #28a745; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block;'>Access Dashboard</a><br>";
} else {
    echo "⚠️ Must be logged in to access dashboard<br>";
}

// Test 4: Form submission test
echo "<h2>4. Form Submission Test</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['form_test'])) {
    echo "<div style='background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin: 10px 0;'>";
    echo "✅ Form submission working! Received data:<br>";
    echo "Name: " . htmlspecialchars($_POST['test_name'] ?? '') . "<br>";
    echo "Email: " . htmlspecialchars($_POST['test_email'] ?? '') . "<br>";
    echo "</div>";
}

echo "<form method='POST' style='background: #f8f9fa; padding: 20px; border-radius: 8px; max-width: 400px; margin-top: 10px;'>";
echo "<input type='hidden' name='form_test' value='1'>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Test Name:</label>";
echo "<input type='text' name='test_name' value='Test User' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;'>";
echo "</div>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Test Email:</label>";
echo "<input type='email' name='test_email' value='test@example.com' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;'>";
echo "</div>";
echo "<button type='submit' style='background: #17a2b8; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer;'>Test Form Submission</button>";
echo "</form>";

// Test 5: Session information
echo "<h2>5. Session Information</h2>";
echo "<pre style='background: #f8f9fa; padding: 15px; border-radius: 5px; overflow-x: auto;'>";
echo "Session ID: " . session_id() . "\n";
echo "Session Data:\n";
print_r($_SESSION);
echo "</pre>";

// Test 6: Available demo accounts
echo "<h2>6. Demo Accounts Available</h2>";
echo "<div style='background: #e2e3e5; padding: 15px; border-radius: 5px;'>";
echo "<strong>Available demo accounts:</strong><br>";
echo "• Administrator: admin@star-church.org / password123<br>";
echo "• Pastor: pastor@star-church.org / password123<br>";
echo "• MDS: mds@star-church.org / password123<br>";
echo "• Mentor: mentor1@star-church.org / password123<br>";
echo "• Aspirant: aspirant1@example.com / password123<br>";
echo "</div>";

echo "<hr>";
echo "<p><a href='index.php'>← Back to Home</a> | <a href='login.php'>Login Page</a> | <a href='register.php'>Register Page</a> | <a href='test-forms.php'>Form Tests</a></p>";
?>
