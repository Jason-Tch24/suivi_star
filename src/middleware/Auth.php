<?php
/**
 * Authentication Middleware for STAR Volunteer Management System
 */

require_once __DIR__ . '/../models/User.php';

class Auth {
    private static $user = null;
    
    /**
     * Start session if not already started
     */
    public static function startSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Login user
     */
    public static function login($email, $password) {
        self::startSession();
        
        $userModel = new User();
        $user = $userModel->authenticate($email, $password);
        
        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            $_SESSION['last_activity'] = time();
            
            self::$user = $user;
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        self::startSession();
        
        // Clear session data
        $_SESSION = [];
        
        // Destroy session cookie
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        // Destroy session
        session_destroy();
        self::$user = null;
    }
    
    /**
     * Check if user is logged in
     */
    public static function check() {
        self::startSession();
        
        if (!isset($_SESSION['user_id'])) {
            return false;
        }
        
        // Check session timeout
        $config = require __DIR__ . '/../../config/app.php';
        $timeout = $config['session']['lifetime'] * 60; // Convert to seconds
        
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $timeout)) {
            self::logout();
            return false;
        }
        
        $_SESSION['last_activity'] = time();
        return true;
    }
    
    /**
     * Get current user
     */
    public static function user() {
        if (self::$user === null && self::check()) {
            $userModel = new User();
            self::$user = $userModel->findById($_SESSION['user_id']);
        }
        
        return self::$user;
    }
    
    /**
     * Get current user ID
     */
    public static function id() {
        return self::check() ? $_SESSION['user_id'] : null;
    }
    
    /**
     * Get current user role
     */
    public static function role() {
        return self::check() ? $_SESSION['user_role'] : null;
    }
    
    /**
     * Check if user has specific role
     */
    public static function hasRole($role) {
        return self::role() === $role;
    }
    
    /**
     * Check if user has any of the specified roles
     */
    public static function hasAnyRole($roles) {
        $userRole = self::role();
        return $userRole && in_array($userRole, $roles);
    }
    
    /**
     * Check if user has permission
     */
    public static function hasPermission($permission) {
        if (!self::check()) {
            return false;
        }
        
        $userModel = new User();
        return $userModel->hasPermission(self::id(), $permission);
    }
    
    /**
     * Require authentication
     */
    public static function requireAuth() {
        if (!self::check()) {
            self::redirectToLogin();
        }
    }
    
    /**
     * Require specific role
     */
    public static function requireRole($role) {
        self::requireAuth();
        
        if (!self::hasRole($role)) {
            self::accessDenied();
        }
    }
    
    /**
     * Require any of the specified roles
     */
    public static function requireAnyRole($roles) {
        self::requireAuth();
        
        if (!self::hasAnyRole($roles)) {
            self::accessDenied();
        }
    }
    
    /**
     * Require specific permission
     */
    public static function requirePermission($permission) {
        self::requireAuth();
        
        if (!self::hasPermission($permission)) {
            self::accessDenied();
        }
    }
    
    /**
     * Redirect to login page
     */
    public static function redirectToLogin() {
        $currentUrl = $_SERVER['REQUEST_URI'];
        header("Location: /login.php?redirect=" . urlencode($currentUrl));
        exit;
    }
    
    /**
     * Show access denied page
     */
    public static function accessDenied() {
        http_response_code(403);
        include __DIR__ . '/../views/errors/403.php';
        exit;
    }
    
    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken() {
        self::startSession();
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCsrfToken($token) {
        self::startSession();
        
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Get user's dashboard URL based on role
     */
    public static function getDashboardUrl() {
        return 'dashboard.php';
    }

    /**
     * Enhanced permission checking with role hierarchy
     */
    public static function hasEnhancedPermission($permission, $targetUserId = null)
    {
        $user = self::user();
        if (!$user) {
            return false;
        }

        $role = $user['role'];

        // Define role hierarchy (higher number = more permissions)
        $roleHierarchy = [
            'aspirant' => 1,
            'mentor' => 2,
            'mds' => 3,
            'pastor' => 4,
            'administrator' => 5
        ];

        $currentLevel = $roleHierarchy[$role] ?? 0;

        // Define permissions by role
        $permissions = [
            // User management
            'view_users' => ['administrator', 'pastor', 'mds'],
            'create_users' => ['administrator', 'pastor'],
            'edit_users' => ['administrator', 'pastor'],
            'delete_users' => ['administrator'],
            'reset_passwords' => ['administrator', 'pastor'],
            'change_user_status' => ['administrator', 'pastor'],

            // Aspirant management
            'view_all_aspirants' => ['administrator', 'pastor', 'mds'],
            'view_assigned_aspirants' => ['administrator', 'pastor', 'mds', 'mentor'],
            'edit_aspirants' => ['administrator', 'pastor', 'mds'],
            'assign_mentors' => ['administrator', 'pastor', 'mds'],
            'approve_steps' => ['administrator', 'pastor', 'mds', 'mentor'],

            // Ministry management
            'manage_ministries' => ['administrator', 'pastor'],
            'view_ministries' => ['administrator', 'pastor', 'mds', 'mentor'],

            // System administration
            'system_settings' => ['administrator'],
            'view_reports' => ['administrator', 'pastor', 'mds'],
            'export_data' => ['administrator', 'pastor'],

            // Profile management
            'edit_own_profile' => ['administrator', 'pastor', 'mds', 'mentor', 'aspirant'],
            'view_own_progress' => ['aspirant']
        ];

        // Check basic permission
        if (!isset($permissions[$permission]) || !in_array($role, $permissions[$permission])) {
            return false;
        }

        // Additional checks for user-specific actions
        if ($targetUserId && in_array($permission, ['edit_users', 'delete_users', 'reset_passwords', 'change_user_status'])) {
            // Users can always edit themselves (except delete)
            if ($targetUserId == $user['id'] && $permission !== 'delete_users') {
                return true;
            }

            // Get target user's role level
            $userModel = new User();
            $targetUser = $userModel->findById($targetUserId);
            if ($targetUser) {
                $targetLevel = $roleHierarchy[$targetUser['role']] ?? 0;

                // Can't modify users of equal or higher level (except self)
                if ($targetLevel >= $currentLevel && $targetUserId != $user['id']) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Check if user can access specific role level
     */
    public static function canAccessRole($requiredRole)
    {
        $user = self::user();
        if (!$user) {
            return false;
        }

        $roleHierarchy = [
            'aspirant' => 1,
            'mentor' => 2,
            'mds' => 3,
            'pastor' => 4,
            'administrator' => 5
        ];

        $userLevel = $roleHierarchy[$user['role']] ?? 0;
        $requiredLevel = $roleHierarchy[$requiredRole] ?? 0;

        return $userLevel >= $requiredLevel;
    }
}
