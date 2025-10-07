<?php
/**
 * Enhanced User Controller - STAR System
 * Comprehensive user management with role-specific capabilities
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Aspirant.php';
require_once __DIR__ . '/../middleware/Auth.php';

class EnhancedUserController
{
    private $userModel;
    private $aspirantModel;
    
    public function __construct()
    {
        $this->userModel = new User();
        $this->aspirantModel = new Aspirant();
    }
    
    /**
     * Get role-specific form fields
     */
    public function getRoleSpecificFields($role)
    {
        $fields = [
            'pastor' => [
                'church_position' => ['type' => 'text', 'label' => 'Church Position', 'required' => true],
                'years_of_service' => ['type' => 'number', 'label' => 'Years of Service', 'required' => false],
                'oversight_areas' => ['type' => 'textarea', 'label' => 'Oversight Areas', 'required' => false]
            ],
            'mds' => [
                'department' => ['type' => 'select', 'label' => 'Department', 'required' => true, 
                    'options' => ['Administration', 'Training', 'Coordination', 'Assessment']],
                'certification_level' => ['type' => 'select', 'label' => 'Certification Level', 'required' => true,
                    'options' => ['Basic', 'Intermediate', 'Advanced', 'Expert']],
                'specialization_areas' => ['type' => 'checkbox', 'label' => 'Specialization Areas', 'required' => false,
                    'options' => ['Youth Ministry', 'Adult Ministry', 'Music Ministry', 'Outreach', 'Administration']]
            ],
            'mentor' => [
                'experience_level' => ['type' => 'select', 'label' => 'Experience Level', 'required' => true,
                    'options' => ['Beginner', 'Intermediate', 'Advanced', 'Expert']],
                'mentoring_capacity' => ['type' => 'number', 'label' => 'Mentoring Capacity', 'required' => true, 'min' => 1, 'max' => 10],
                'available_time_slots' => ['type' => 'checkbox', 'label' => 'Available Time Slots', 'required' => false,
                    'options' => ['Monday Morning', 'Monday Evening', 'Tuesday Morning', 'Tuesday Evening', 
                                'Wednesday Morning', 'Wednesday Evening', 'Thursday Morning', 'Thursday Evening',
                                'Friday Morning', 'Friday Evening', 'Saturday Morning', 'Saturday Evening',
                                'Sunday Morning', 'Sunday Evening']]
            ],
            'aspirant' => [
                'ministry_preference_1' => ['type' => 'select', 'label' => 'First Ministry Choice', 'required' => true,
                    'options' => $this->getMinistryOptions()],
                'ministry_preference_2' => ['type' => 'select', 'label' => 'Second Ministry Choice', 'required' => false,
                    'options' => $this->getMinistryOptions()],
                'ministry_preference_3' => ['type' => 'select', 'label' => 'Third Ministry Choice', 'required' => false,
                    'options' => $this->getMinistryOptions()],
                'background_check_status' => ['type' => 'select', 'label' => 'Background Check Status', 'required' => false,
                    'options' => ['Not Started', 'In Progress', 'Completed', 'Approved', 'Rejected']],
                'training_progress' => ['type' => 'number', 'label' => 'Training Progress (%)', 'required' => false, 'min' => 0, 'max' => 100]
            ]
        ];
        
        return $fields[$role] ?? [];
    }
    
    /**
     * Get ministry options for aspirants
     */
    private function getMinistryOptions()
    {
        // This would typically come from the database
        return [
            'Youth Ministry',
            'Adult Ministry', 
            'Music Ministry',
            'Children Ministry',
            'Outreach Ministry',
            'Administration',
            'Technical Ministry',
            'Hospitality Ministry',
            'Prayer Ministry',
            'Counseling Ministry'
        ];
    }
    
    /**
     * Create user with role-specific data
     */
    public function createUserWithRoleData($userData, $roleData = [])
    {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            // Validate basic user data
            $this->validateUserData($userData);
            
            // Validate role-specific data
            $this->validateRoleSpecificData($userData['role'], $roleData);
            
            // Create base user
            $userId = $this->userModel->create($userData);
            
            // Create role-specific profile
            $this->createRoleSpecificProfile($userId, $userData['role'], $roleData);
            
            // Log user creation
            $this->logUserActivity($userId, 'user_created', 'User account created');
            
            $db->commit();
            return $userId;
            
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
    
    /**
     * Update user with role-specific data
     */
    public function updateUserWithRoleData($userId, $userData, $roleData = [])
    {
        $db = Database::getInstance();
        
        try {
            $db->beginTransaction();
            
            $user = $this->userModel->findById($userId);
            if (!$user) {
                throw new Exception("User not found");
            }
            
            // Update base user data
            if (!empty($userData)) {
                $this->userModel->update($userId, $userData);
            }
            
            // Update role-specific data
            if (!empty($roleData)) {
                $this->updateRoleSpecificProfile($userId, $user['role'], $roleData);
            }
            
            // Log user update
            $this->logUserActivity($userId, 'user_updated', 'User profile updated');
            
            $db->commit();
            return true;
            
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
    
    /**
     * Bulk operations
     */
    public function bulkUpdateUsers($userIds, $updates)
    {
        $db = Database::getInstance();
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];
        
        try {
            $db->beginTransaction();
            
            foreach ($userIds as $userId) {
                try {
                    $this->userModel->update($userId, $updates);
                    $this->logUserActivity($userId, 'bulk_update', 'Bulk update applied');
                    $results['success']++;
                } catch (Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "User ID $userId: " . $e->getMessage();
                }
            }
            
            $db->commit();
            return $results;
            
        } catch (Exception $e) {
            $db->rollback();
            throw $e;
        }
    }
    
    /**
     * Import users from CSV
     */
    public function importUsersFromCSV($csvFile)
    {
        $results = ['success' => 0, 'failed' => 0, 'errors' => []];
        
        if (($handle = fopen($csvFile, "r")) !== FALSE) {
            $header = fgetcsv($handle); // Skip header row
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                try {
                    $userData = array_combine($header, $data);
                    $this->createUserWithRoleData($userData);
                    $results['success']++;
                } catch (Exception $e) {
                    $results['failed']++;
                    $results['errors'][] = "Row " . ($results['success'] + $results['failed']) . ": " . $e->getMessage();
                }
            }
            fclose($handle);
        }
        
        return $results;
    }
    
    /**
     * Export users to CSV
     */
    public function exportUsersToCSV($filters = [])
    {
        $users = $this->getAllUsers(1, 10000, $filters['role'] ?? null, $filters['status'] ?? null);
        
        $filename = 'users_export_' . date('Y-m-d_H-i-s') . '.csv';
        $filepath = __DIR__ . '/../../exports/' . $filename;
        
        // Ensure exports directory exists
        if (!is_dir(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // Write header
        fputcsv($file, ['ID', 'Email', 'First Name', 'Last Name', 'Role', 'Status', 'Created At']);
        
        // Write data
        foreach ($users['users'] as $user) {
            fputcsv($file, [
                $user['id'],
                $user['email'],
                $user['first_name'],
                $user['last_name'],
                $user['role'],
                $user['status'],
                $user['created_at']
            ]);
        }
        
        fclose($file);
        return $filename;
    }
    
    /**
     * Validate user data
     */
    private function validateUserData($userData)
    {
        $required = ['email', 'first_name', 'last_name', 'role'];
        
        foreach ($required as $field) {
            if (empty($userData[$field])) {
                throw new Exception("Field '$field' is required");
            }
        }
        
        if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address");
        }
        
        $validRoles = ['administrator', 'pastor', 'mds', 'mentor', 'aspirant'];
        if (!in_array($userData['role'], $validRoles)) {
            throw new Exception("Invalid role specified");
        }
    }
    
    /**
     * Validate role-specific data
     */
    private function validateRoleSpecificData($role, $roleData)
    {
        $fields = $this->getRoleSpecificFields($role);
        
        foreach ($fields as $fieldName => $fieldConfig) {
            if ($fieldConfig['required'] && empty($roleData[$fieldName])) {
                throw new Exception("Field '{$fieldConfig['label']}' is required for role '$role'");
            }
        }
    }
    
    /**
     * Create role-specific profile
     */
    private function createRoleSpecificProfile($userId, $role, $roleData)
    {
        $db = Database::getInstance();
        
        // Create role-specific profile table entry
        $tableName = $role . '_profiles';
        
        if (!empty($roleData)) {
            $fields = array_keys($roleData);
            $placeholders = str_repeat('?,', count($fields) - 1) . '?';
            $values = array_values($roleData);
            
            $sql = "INSERT INTO $tableName (user_id, " . implode(', ', $fields) . ") VALUES (?, $placeholders)";
            $stmt = $db->prepare($sql);
            $stmt->execute(array_merge([$userId], $values));
        }
    }
    
    /**
     * Update role-specific profile
     */
    private function updateRoleSpecificProfile($userId, $role, $roleData)
    {
        $db = Database::getInstance();
        $tableName = $role . '_profiles';
        
        if (!empty($roleData)) {
            $setParts = [];
            $values = [];
            
            foreach ($roleData as $field => $value) {
                $setParts[] = "$field = ?";
                $values[] = $value;
            }
            
            $values[] = $userId;
            
            $sql = "UPDATE $tableName SET " . implode(', ', $setParts) . " WHERE user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute($values);
        }
    }
    
    /**
     * Log user activity
     */
    private function logUserActivity($userId, $action, $description)
    {
        $db = Database::getInstance();
        $currentUser = Auth::user();
        
        $stmt = $db->prepare("INSERT INTO user_activity_log (user_id, action, description, performed_by, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$userId, $action, $description, $currentUser['id'] ?? null]);
    }
    
    /**
     * Get all users (inherited from UserController)
     */
    public function getAllUsers($page = 1, $limit = 20, $role = null, $status = null)
    {
        // Use the existing UserController method
        $userController = new UserController();
        return $userController->getAllUsers($page, $limit, $role, $status);
    }

    /**
     * Get available roles based on current user permissions
     */
    public function getAvailableRoles()
    {
        $currentUser = Auth::user();
        $allRoles = [
            'administrator' => 'Administrator',
            'pastor' => 'Pastor',
            'mds' => 'MDS',
            'mentor' => 'Mentor',
            'aspirant' => 'Aspirant'
        ];

        // Filter roles based on current user's permissions
        switch ($currentUser['role']) {
            case 'administrator':
                return $allRoles; // Can create all roles
            case 'pastor':
                return array_slice($allRoles, 1, null, true); // Can't create administrators
            case 'mds':
                return array_slice($allRoles, 3, null, true); // Can only create mentors and aspirants
            default:
                return []; // No creation permissions
        }
    }
}
