<?php
/**
 * Main Entry Point for STAR Volunteer Management System
 */

// Start session and load dependencies
session_start();
require_once 'src/middleware/Auth.php';
require_once 'src/models/User.php';

// Load environment variables if .env file exists
if (file_exists('.env')) {
    $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $_ENV[trim($key)] = trim($value);
        }
    }
}

// Simple routing - works without mod_rewrite
$path = $_GET['path'] ?? '/';

// Clean the path
$path = '/' . trim($path, '/');
if ($path === '/') {
    // Default home page
}

// Route handling
switch ($path) {
    case '/':
        if (Auth::check()) {
            header('Location: ' . Auth::getDashboardUrl());
            exit;
        } else {
            include 'public/home.php';
        }
        break;
        
    case '/login':
        include 'public/login.php';
        break;
        
    case '/logout':
        Auth::logout();
        header('Location: /');
        exit;
        break;
        
    case '/register':
        include 'public/register.php';
        break;
        
    case '/dashboard':
        Auth::requireAuth();
        header('Location: ' . Auth::getDashboardUrl());
        exit;
        break;
        
    case '/dashboard/aspirant':
        Auth::requireRole('aspirant');
        include 'src/views/dashboard/aspirant.php';
        break;
        
    case '/dashboard/mentor':
        Auth::requireRole('mentor');
        include 'src/views/dashboard/mentor.php';
        break;
        
    case '/dashboard/mds':
        Auth::requireRole('mds');
        include 'src/views/dashboard/mds.php';
        break;
        
    case '/dashboard/admin':
        Auth::requireRole('administrator');
        include 'src/views/dashboard/admin.php';
        break;
        
    case '/dashboard/pastor':
        Auth::requireRole('pastor');
        include 'src/views/dashboard/pastor.php';
        break;
        
    case '/profile':
        Auth::requireAuth();
        include 'src/views/profile.php';
        break;
        
    case '/aspirants':
        Auth::requireAnyRole(['administrator', 'mds', 'pastor']);
        include 'src/views/aspirants/index.php';
        break;
        
    case '/setup':
        include 'setup.php';
        break;
        
    default:
        // Check if it's a static file request
        if (preg_match('/\.(css|js|png|jpg|jpeg|gif|ico|svg)$/', $path)) {
            return false; // Let the web server handle static files
        }
        
        // 404 Not Found
        http_response_code(404);
        include 'src/views/errors/404.php';
        break;
}
