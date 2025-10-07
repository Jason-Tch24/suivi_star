<?php
/**
 * Dashboard Router - STAR Volunteer Management System
 */

require_once __DIR__ . '/src/middleware/Auth.php';

Auth::requireAuth();

$user = Auth::user();
$role = $user['role'];

// Route to appropriate dashboard based on role
switch ($role) {
    case 'aspirant':
        include __DIR__ . '/src/views/dashboard/aspirant.php';
        break;
        
    case 'mentor':
        include __DIR__ . '/src/views/dashboard/mentor.php';
        break;
        
    case 'mds':
        include __DIR__ . '/src/views/dashboard/mds.php';
        break;
        
    case 'administrator':
        include __DIR__ . '/src/views/dashboard/admin.php';
        break;
        
    case 'pastor':
        include __DIR__ . '/src/views/dashboard/pastor.php';
        break;
        
    default:
        http_response_code(403);
        echo "Access denied: Unknown role";
        exit;
}
?>
