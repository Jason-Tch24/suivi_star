<?php
/**
 * Google OAuth Callback Handler
 */

require_once __DIR__ . '/../../src/helpers/EnvLoader.php';
require_once __DIR__ . '/../../src/services/GoogleAuthService.php';
require_once __DIR__ . '/../../src/middleware/Auth.php';
require_once __DIR__ . '/../../src/models/Aspirant.php';

// Load environment variables
EnvLoader::load();

session_start();

// Check for error from Google
if (isset($_GET['error'])) {
    $error = $_GET['error'];
    error_log('Google OAuth Error: ' . $error);
    header('Location: /login.php?error=' . urlencode('Google login was cancelled or failed'));
    exit;
}

// Check for authorization code
if (!isset($_GET['code'])) {
    header('Location: /login.php?error=' . urlencode('Invalid Google login response'));
    exit;
}

try {
    $googleAuth = new GoogleAuthService();
    $user = $googleAuth->handleCallback($_GET['code']);
    
    if (!$user) {
        throw new Exception('Failed to authenticate with Google');
    }
    
    // Log the user in
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
    $_SESSION['last_activity'] = time();
    
    // If this is a new aspirant user, create aspirant record
    if ($user['role'] === 'aspirant') {
        $aspirantModel = new Aspirant();
        $existingAspirant = $aspirantModel->findByUserId($user['id']);
        
        if (!$existingAspirant) {
            // Create aspirant record for new Google user
            $aspirantData = [
                'user_id' => $user['id'],
                'application_date' => date('Y-m-d'),
                'current_step' => 1,
                'status' => 'active',
                'notes' => 'Account created via Google OAuth'
            ];
            
            $aspirantId = $aspirantModel->create($aspirantData);
            
            // Initialize first step progress
            if ($aspirantId) {
                $aspirantModel->updateStepProgress($aspirantId, 1, [
                    'status' => 'in_progress',
                    'started_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
    
    // Determine redirect URL
    $redirectUrl = $_SESSION['google_redirect'] ?? Auth::getDashboardUrl();
    unset($_SESSION['google_redirect']);
    
    // Redirect to dashboard or intended page
    header('Location: ' . $redirectUrl);
    exit;
    
} catch (Exception $e) {
    error_log('Google OAuth Callback Error: ' . $e->getMessage());
    header('Location: /login.php?error=' . urlencode('Google login failed. Please try again.'));
    exit;
}
