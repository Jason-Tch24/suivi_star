<?php
/**
 * Journey Step Model for STAR Volunteer Management System
 */

require_once __DIR__ . '/Database.php';

class JourneyStep {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all journey steps
     */
    public function getAll() {
        return $this->db->fetchAll("SELECT * FROM journey_steps WHERE is_active = 1 ORDER BY step_number");
    }
    
    /**
     * Find step by ID
     */
    public function findById($id) {
        return $this->db->fetch("SELECT * FROM journey_steps WHERE id = ?", [$id]);
    }
    
    /**
     * Find step by step number
     */
    public function findByStepNumber($stepNumber) {
        return $this->db->fetch("SELECT * FROM journey_steps WHERE step_number = ? AND is_active = 1", [$stepNumber]);
    }
    
    /**
     * Get step progress for aspirant
     */
    public function getProgressForAspirant($aspirantId) {
        $sql = "SELECT js.*, sp.status as progress_status, sp.started_at, sp.completed_at,
                       sp.deadline, sp.validation_notes,
                       validator.first_name as validator_first_name,
                       validator.last_name as validator_last_name
                FROM journey_steps js
                LEFT JOIN step_progress sp ON js.id = sp.step_id AND sp.aspirant_id = ?
                LEFT JOIN users validator ON sp.validator_id = validator.id
                WHERE js.is_active = 1
                ORDER BY js.step_number";
        
        return $this->db->fetchAll($sql, [$aspirantId]);
    }
    
    /**
     * Get current step for aspirant
     */
    public function getCurrentStepForAspirant($aspirantId) {
        $sql = "SELECT js.*, sp.status as progress_status, sp.started_at, sp.completed_at,
                       sp.deadline, sp.validation_notes
                FROM aspirants a
                JOIN journey_steps js ON a.current_step = js.step_number
                LEFT JOIN step_progress sp ON js.id = sp.step_id AND sp.aspirant_id = a.id
                WHERE a.id = ? AND js.is_active = 1";
        
        return $this->db->fetch($sql, [$aspirantId]);
    }
    
    /**
     * Get next step for aspirant
     */
    public function getNextStepForAspirant($aspirantId) {
        $sql = "SELECT js.*
                FROM aspirants a
                JOIN journey_steps js ON (a.current_step + 1) = js.step_number
                WHERE a.id = ? AND js.is_active = 1";
        
        return $this->db->fetch($sql, [$aspirantId]);
    }
    
    /**
     * Check if step can be advanced
     */
    public function canAdvanceStep($aspirantId, $stepNumber) {
        // Check if current step is completed
        $sql = "SELECT sp.status
                FROM step_progress sp
                JOIN journey_steps js ON sp.step_id = js.id
                WHERE sp.aspirant_id = ? AND js.step_number = ?";
        
        $progress = $this->db->fetch($sql, [$aspirantId, $stepNumber]);
        
        return $progress && $progress['status'] === 'completed';
    }
    
    /**
     * Get step requirements based on step number
     */
    public function getStepRequirements($stepNumber) {
        $requirements = [
            1 => [
                'title' => 'Application Submission',
                'description' => 'Complete and submit the STAR volunteer application form',
                'actions' => ['Submit application form', 'Provide required documents'],
                'validator_roles' => ['administrator']
            ],
            2 => [
                'title' => 'PCNC Training Completion',
                'description' => 'Complete the 6-month Pastoral Care and Nurture Course',
                'actions' => ['Attend training sessions', 'Complete assignments', 'Pass final assessment'],
                'validator_roles' => ['administrator']
            ],
            3 => [
                'title' => 'MDS Interview',
                'description' => 'Successfully complete interview with Ministry of STAR team',
                'actions' => ['Schedule interview', 'Attend interview', 'Receive approval'],
                'validator_roles' => ['mds']
            ],
            4 => [
                'title' => 'Ministry Training',
                'description' => 'Complete one-month training in chosen ministry with mentor',
                'actions' => ['Ministry assignment', 'Work with mentor', 'Complete training tasks'],
                'validator_roles' => ['mentor', 'administrator']
            ],
            5 => [
                'title' => 'Mentor Evaluation',
                'description' => 'Receive favorable report from assigned mentor',
                'actions' => ['Mentor assessment', 'Performance review', 'Final recommendation'],
                'validator_roles' => ['mentor', 'administrator']
            ],
            6 => [
                'title' => 'Final Assignment',
                'description' => 'Receive official assignment as active STAR volunteer',
                'actions' => ['Role assignment', 'Ministry placement', 'Begin active service'],
                'validator_roles' => ['administrator', 'pastor']
            ]
        ];
        
        return $requirements[$stepNumber] ?? null;
    }
    
    /**
     * Get step statistics
     */
    public function getStepStatistics() {
        $sql = "SELECT js.step_number, js.name,
                       COUNT(CASE WHEN sp.status = 'pending' THEN 1 END) as pending_count,
                       COUNT(CASE WHEN sp.status = 'in_progress' THEN 1 END) as in_progress_count,
                       COUNT(CASE WHEN sp.status = 'completed' THEN 1 END) as completed_count,
                       COUNT(CASE WHEN sp.status = 'rejected' THEN 1 END) as rejected_count,
                       COUNT(CASE WHEN sp.status = 'extended' THEN 1 END) as extended_count
                FROM journey_steps js
                LEFT JOIN step_progress sp ON js.id = sp.step_id
                LEFT JOIN aspirants a ON sp.aspirant_id = a.id AND a.status = 'active'
                WHERE js.is_active = 1
                GROUP BY js.id, js.step_number, js.name
                ORDER BY js.step_number";
        
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get aspirants at specific step
     */
    public function getAspirantsAtStep($stepNumber, $status = null) {
        $sql = "SELECT a.*, u.first_name, u.last_name, u.email, u.phone,
                       sp.status as step_status, sp.started_at, sp.deadline,
                       m.name as assigned_ministry_name
                FROM aspirants a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN step_progress sp ON a.id = sp.aspirant_id
                LEFT JOIN journey_steps js ON sp.step_id = js.id AND js.step_number = ?
                LEFT JOIN ministries m ON a.assigned_ministry_id = m.id
                WHERE a.current_step = ? AND a.status = 'active'";
        
        $params = [$stepNumber, $stepNumber];
        
        if ($status) {
            $sql .= " AND sp.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY sp.started_at ASC, a.created_at ASC";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Calculate step completion rate
     */
    public function getCompletionRate($stepNumber) {
        $sql = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN sp.status = 'completed' THEN 1 END) as completed
                FROM step_progress sp
                JOIN journey_steps js ON sp.step_id = js.id
                JOIN aspirants a ON sp.aspirant_id = a.id
                WHERE js.step_number = ? AND a.status IN ('active', 'completed')";
        
        $result = $this->db->fetch($sql, [$stepNumber]);
        
        if ($result['total'] > 0) {
            return round(($result['completed'] / $result['total']) * 100, 2);
        }
        
        return 0;
    }
    
    /**
     * Get average time to complete step
     */
    public function getAverageCompletionTime($stepNumber) {
        $sql = "SELECT AVG(DATEDIFF(sp.completed_at, sp.started_at)) as avg_days
                FROM step_progress sp
                JOIN journey_steps js ON sp.step_id = js.id
                WHERE js.step_number = ? AND sp.status = 'completed'
                  AND sp.started_at IS NOT NULL AND sp.completed_at IS NOT NULL";
        
        $result = $this->db->fetch($sql, [$stepNumber]);
        
        return $result['avg_days'] ? round($result['avg_days'], 1) : 0;
    }
    
    /**
     * Get overdue steps
     */
    public function getOverdueSteps() {
        $sql = "SELECT a.*, u.first_name, u.last_name, u.email,
                       js.name as step_name, js.step_number,
                       sp.started_at, sp.deadline,
                       DATEDIFF(CURDATE(), sp.deadline) as days_overdue
                FROM step_progress sp
                JOIN journey_steps js ON sp.step_id = js.id
                JOIN aspirants a ON sp.aspirant_id = a.id
                JOIN users u ON a.user_id = u.id
                WHERE sp.status IN ('pending', 'in_progress')
                  AND sp.deadline IS NOT NULL
                  AND sp.deadline < CURDATE()
                  AND a.status = 'active'
                ORDER BY days_overdue DESC";
        
        return $this->db->fetchAll($sql);
    }
}
