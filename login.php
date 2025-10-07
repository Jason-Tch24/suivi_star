<?php
/**
 * Login Page - STAR Volunteer Management System
 */

require_once __DIR__ . '/src/middleware/Auth.php';

$appConfig = require __DIR__ . '/config/app.php';
$error = '';
$success = '';

// Auto-fill email if provided in URL
$autoEmail = $_GET['auto_email'] ?? '';

// Redirect if already logged in
if (Auth::check()) {
    header('Location: ' . Auth::getDashboardUrl());
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        if (Auth::login($email, $password)) {
            $redirectUrl = $_GET['redirect'] ?? 'dashboard.php';
            header('Location: ' . $redirectUrl);
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo $appConfig['name']; ?></title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>STAR Login</h1>
                <p>Sign in to your volunteer account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" class="auth-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="<?php echo htmlspecialchars($_POST['email'] ?? $autoEmail); ?>"
                        required 
                        autocomplete="email"
                        placeholder="Enter your email address"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        required 
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">Sign In</button>
            </form>
            
            <div class="auth-links">
                <p><a href="index.php">← Back to Home</a></p>
                <p>New to STAR? <a href="register.php">Apply to become a volunteer</a></p>
            </div>
            
            <div class="demo-credentials">
                <h4>Demo Credentials</h4>
                <div class="demo-grid">
                    <div class="demo-account">
                        <strong>Administrator</strong><br>
                        admin@star-church.org<br>
                        password123
                    </div>
                    <div class="demo-account">
                        <strong>Pastor</strong><br>
                        pastor@star-church.org<br>
                        password123
                    </div>
                    <div class="demo-account">
                        <strong>MDS</strong><br>
                        mds@star-church.org<br>
                        password123
                    </div>
                    <div class="demo-account">
                        <strong>Mentor</strong><br>
                        mentor1@star-church.org<br>
                        password123
                    </div>
                    <div class="demo-account">
                        <strong>Aspirant</strong><br>
                        aspirant1@example.com<br>
                        password123
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
