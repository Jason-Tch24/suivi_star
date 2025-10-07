<?php
/**
 * Aspirant Model for STAR Volunteer Management System
 */

require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/User.php';

class Aspirant {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a new aspirant
     */
    public function create($aspirantData) {
        $aspirantData['created_at'] = date('Y-m-d H:i:s');
        $aspirantData['updated_at'] = date('Y-m-d H:i:s');
        
        return $this->db->insert('aspirants', $aspirantData);
    }
    
    /**
     * Find aspirant by ID
     */
    public function findById($id) {
        $sql = "SELECT a.*, u.first_name, u.last_name, u.email, u.phone, u.status as user_status,
                       m1.name as ministry_preference_1_name,
                       m2.name as ministry_preference_2_name,
                       m3.name as ministry_preference_3_name,
                       am.name as assigned_ministry_name,
                       mentor.first_name as mentor_first_name,
                       mentor.last_name as mentor_last_name
                FROM aspirants a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN ministries m1 ON a.ministry_preference_1 = m1.id
                LEFT JOIN ministries m2 ON a.ministry_preference_2 = m2.id
                LEFT JOIN ministries m3 ON a.ministry_preference_3 = m3.id
                LEFT JOIN ministries am ON a.assigned_ministry_id = am.id
                LEFT JOIN users mentor ON a.mentor_id = mentor.id
                WHERE a.id = ?";
        
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Find aspirant by user ID
     */
    public function findByUserId($userId) {
        $sql = "SELECT a.*, u.first_name, u.last_name, u.email, u.phone, u.status as user_status,
                       m1.name as ministry_preference_1_name,
                       m2.name as ministry_preference_2_name,
                       m3.name as ministry_preference_3_name,
                       am.name as assigned_ministry_name,
                       mentor.first_name as mentor_first_name,
                       mentor.last_name as mentor_last_name
                FROM aspirants a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN ministries m1 ON a.ministry_preference_1 = m1.id
                LEFT JOIN ministries m2 ON a.ministry_preference_2 = m2.id
                LEFT JOIN ministries m3 ON a.ministry_preference_3 = m3.id
                LEFT JOIN ministries am ON a.assigned_ministry_id = am.id
                LEFT JOIN users mentor ON a.mentor_id = mentor.id
                WHERE a.user_id = ?";
        
        return $this->db->fetch($sql, [$userId]);
    }
    
    /**
     * Get all aspirants with pagination
     */
    public function getAll($limit = 50, $offset = 0, $status = null, $currentStep = null) {
        $sql = "SELECT a.*, u.first_name, u.last_name, u.email, u.phone,
                       js.name as current_step_name,
                       am.name as assigned_ministry_name
                FROM aspirants a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN journey_steps js ON a.current_step = js.step_number
                LEFT JOIN ministries am ON a.assigned_ministry_id = am.id
                WHERE 1=1";
        
        $params = [];
        
        if ($status) {
            $sql .= " AND a.status = ?";
            $params[] = $status;
        }
        
        if ($currentStep) {
            $sql .= " AND a.current_step = ?";
            $params[] = $currentStep;
        }
        
        $sql .= " ORDER BY a.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Update aspirant
     */
    public function update($id, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        return $this->db->update('aspirants', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Get aspirant's journey progress
     */
    public function getJourneyProgress($aspirantId) {
        $sql = "SELECT sp.*, js.name as step_name, js.description as step_description,
                       js.duration_days, validator.first_name as validator_first_name,
                       validator.last_name as validator_last_name
                FROM step_progress sp
                JOIN journey_steps js ON sp.step_id = js.id
                LEFT JOIN users validator ON sp.validator_id = validator.id
                WHERE sp.aspirant_id = ?
                ORDER BY js.step_number";
        
        return $this->db->fetchAll($sql, [$aspirantId]);
    }
    
    /**
     * Update step progress
     */
    public function updateStepProgress($aspirantId, $stepId, $data) {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        // Check if progress record exists
        $existing = $this->db->fetch(
            "SELECT id FROM step_progress WHERE aspirant_id = ? AND step_id = ?",
            [$aspirantId, $stepId]
        );
        
        if ($existing) {
            return $this->db->update('step_progress', $data, 
                'aspirant_id = :aspirant_id AND step_id = :step_id',
                ['aspirant_id' => $aspirantId, 'step_id' => $stepId]
            );
        } else {
            $data['aspirant_id'] = $aspirantId;
            $data['step_id'] = $stepId;
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->insert('step_progress', $data);
        }
    }
    
    /**
     * Advance aspirant to next step
     */
    public function advanceToNextStep($aspirantId, $validatorId = null) {
        $aspirant = $this->findById($aspirantId);
        if (!$aspirant) return false;
        
        $currentStep = $aspirant['current_step'];
        $nextStep = $currentStep + 1;
        
        // Complete current step
        $this->updateStepProgress($aspirantId, $currentStep, [
            'status' => 'completed',
            'completed_at' => date('Y-m-d H:i:s'),
            'validator_id' => $validatorId
        ]);
        
        // Check if there's a next step
        $nextStepInfo = $this->db->fetch("SELECT * FROM journey_steps WHERE step_number = ?", [$nextStep]);
        
        if ($nextStepInfo) {
            // Update aspirant's current step
            $this->update($aspirantId, ['current_step' => $nextStep]);
            
            // Create progress record for next step
            $this->updateStepProgress($aspirantId, $nextStepInfo['id'], [
                'status' => 'pending',
                'started_at' => date('Y-m-d H:i:s')
            ]);
            
            return true;
        } else {
            // Journey completed
            $this->update($aspirantId, ['status' => 'completed']);
            return true;
        }
    }
    
    /**
     * Reject aspirant at current step
     */
    public function rejectAtCurrentStep($aspirantId, $validatorId, $notes = '') {
        $aspirant = $this->findById($aspirantId);
        if (!$aspirant) return false;
        
        $currentStep = $aspirant['current_step'];
        
        // Mark current step as rejected
        $this->updateStepProgress($aspirantId, $currentStep, [
            'status' => 'rejected',
            'completed_at' => date('Y-m-d H:i:s'),
            'validator_id' => $validatorId,
            'validation_notes' => $notes
        ]);
        
        // Update aspirant status
        $this->update($aspirantId, ['status' => 'rejected']);
        
        return true;
    }
    
    /**
     * Get aspirants by mentor
     */
    public function getByMentor($mentorId) {
        $sql = "SELECT a.*, u.first_name, u.last_name, u.email, u.phone,
                       js.name as current_step_name,
                       am.name as assigned_ministry_name
                FROM aspirants a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN journey_steps js ON a.current_step = js.step_number
                LEFT JOIN ministries am ON a.assigned_ministry_id = am.id
                WHERE a.mentor_id = ? AND a.status = 'active'
                ORDER BY a.created_at DESC";
        
        return $this->db->fetchAll($sql, [$mentorId]);
    }
    
    /**
     * Get statistics
     */
    public function getStatistics() {
        $stats = [];
        
        // Total aspirants
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM aspirants");
        $stats['total'] = $result['count'];
        
        // By status
        $statusStats = $this->db->fetchAll("SELECT status, COUNT(*) as count FROM aspirants GROUP BY status");
        foreach ($statusStats as $stat) {
            $stats['by_status'][$stat['status']] = $stat['count'];
        }
        
        // By current step
        $stepStats = $this->db->fetchAll(
            "SELECT a.current_step, js.name, COUNT(*) as count 
             FROM aspirants a 
             LEFT JOIN journey_steps js ON a.current_step = js.step_number 
             WHERE a.status = 'active'
             GROUP BY a.current_step, js.name 
             ORDER BY a.current_step"
        );
        foreach ($stepStats as $stat) {
            $stats['by_step'][$stat['current_step']] = [
                'name' => $stat['name'],
                'count' => $stat['count']
            ];
        }
        
        return $stats;
    }
    
    /**
     * Count aspirants
     */
    public function count($status = null, $currentStep = null) {
        $sql = "SELECT COUNT(*) as count FROM aspirants WHERE 1=1";
        $params = [];
        
        if ($status) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        
        if ($currentStep) {
            $sql .= " AND current_step = ?";
            $params[] = $currentStep;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }
}
