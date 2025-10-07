<?php
/**
 * User Model for STAR Volunteer Management System
 */

require_once __DIR__ . '/Database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a new user
     */
    public function create($userData) {
        // Hash password if provided
        if (isset($userData['password'])) {
            $userData['password_hash'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            unset($userData['password']);
        }
        
        $userData['created_at'] = date('Y-m-d H:i:s');
        $userData['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('users', $userData);
    }
    
    /**
     * Find user by ID
     */
    public function findById($id) {
        return $this->db->fetch("SELECT * FROM users WHERE id = ?", [$id]);
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        return $this->db->fetch("SELECT * FROM users WHERE email = ?", [$email]);
    }
    
    /**
     * Authenticate user
     */
    public function authenticate($email, $password) {
        $user = $this->findByEmail($email);
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Update last login
            $this->updateLastLogin($user['id']);
            return $user;
        }
        
        return false;
    }
    
    /**
     * Update user's last login timestamp
     */
    public function updateLastLogin($userId) {
        return $this->db->update('users', 
            ['last_login' => date('Y-m-d H:i:s')], 
            'id = :id', 
            ['id' => $userId]
        );
    }
    
    /**
     * Update user information
     */
    public function update($id, $userData) {
        // Hash password if provided
        if (isset($userData['password'])) {
            $userData['password_hash'] = password_hash($userData['password'], PASSWORD_DEFAULT);
            unset($userData['password']);
        }
        
        $userData['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->db->update('users', $userData, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Get all users by role
     */
    public function getByRole($role) {
        return $this->db->fetchAll("SELECT * FROM users WHERE role = ? AND status = 'active' ORDER BY first_name, last_name", [$role]);
    }
    
    /**
     * Get all users with pagination
     */
    public function getAll($limit = 50, $offset = 0, $role = null) {
        $sql = "SELECT * FROM users";
        $params = [];
        
        if ($role) {
            $sql .= " WHERE role = ?";
            $params[] = $role;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Count users
     */
    public function count($role = null) {
        $sql = "SELECT COUNT(*) as count FROM users";
        $params = [];
        
        if ($role) {
            $sql .= " WHERE role = ?";
            $params[] = $role;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }
    
    /**
     * Delete user (soft delete by setting status to inactive)
     */
    public function delete($id) {
        return $this->db->update('users', ['status' => 'inactive'], 'id = :id', ['id' => $id]);
    }
    
    /**
     * Check if user has permission for a specific action
     */
    public function hasPermission($userId, $permission) {
        $user = $this->findById($userId);
        if (!$user) return false;
        
        $permissions = $this->getRolePermissions($user['role']);
        return in_array($permission, $permissions);
    }
    
    /**
     * Get permissions for a role
     */
    public function getRolePermissions($role) {
        $permissions = [
            'aspirant' => [
                'view_own_profile',
                'update_own_profile',
                'view_own_progress',
                'download_materials',
                'view_notifications'
            ],
            'mentor' => [
                'view_own_profile',
                'update_own_profile',
                'view_assigned_aspirants',
                'submit_reports',
                'view_training_materials',
                'view_notifications'
            ],
            'mds' => [
                'view_own_profile',
                'update_own_profile',
                'conduct_interviews',
                'approve_reject_aspirants',
                'view_aspirant_progress',
                'view_notifications'
            ],
            'administrator' => [
                'manage_users',
                'manage_aspirants',
                'manage_ministries',
                'validate_steps',
                'view_all_progress',
                'manage_system_settings',
                'view_analytics',
                'manage_documents',
                'send_notifications'
            ],
            'pastor' => [
                'view_dashboard',
                'view_analytics',
                'view_all_progress',
                'view_reports',
                'manage_final_assignments',
                'view_notifications'
            ]
        ];
        
        return $permissions[$role] ?? [];
    }
    
    /**
     * Get user's full name
     */
    public function getFullName($user) {
        return trim($user['first_name'] . ' ' . $user['last_name']);
    }
    
    /**
     * Check if email is unique
     */
    public function isEmailUnique($email, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] == 0;
    }
}
