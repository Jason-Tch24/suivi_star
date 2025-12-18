<?php
/**
 * Google OAuth Login Initiation
 */

require_once __DIR__ . '/../../src/helpers/EnvLoader.php';
require_once __DIR__ . '/../../src/services/GoogleAuthService.php';
require_once __DIR__ . '/../../src/middleware/Auth.php';

// Load environment variables
EnvLoader::load();

// Redirect if already logged in
if (Auth::check()) {
    header('Location: ' . Auth::getDashboardUrl());
    exit;
}

try {
    $googleAuth = new GoogleAuthService();
    
    if (!$googleAuth->isConfigured()) {
        throw new Exception('Google OAuth is not configured');
    }
    
    // Store the intended redirect URL in session
    session_start();
    if (isset($_GET['redirect'])) {
        $_SESSION['google_redirect'] = $_GET['redirect'];
    }
    
    // Redirect to Google OAuth
    $authUrl = $googleAuth->getAuthUrl();
    header('Location: ' . $authUrl);
    exit;
    
} catch (Exception $e) {
    error_log('Google OAuth Login Error: ' . $e->getMessage());
    header('Location: /login.php?error=' . urlencode('Google login is currently unavailable'));
    exit;
}
