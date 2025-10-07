<?php
/**
 * Logout Page - STAR Volunteer Management System
 */

require_once __DIR__ . '/src/middleware/Auth.php';

Auth::logout();
header('Location: index.php');
exit;
?>
