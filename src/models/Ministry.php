<?php
/**
 * Ministry Model for STAR Volunteer Management System
 */

require_once __DIR__ . '/Database.php';

class Ministry {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a new ministry
     */
    public function create($ministryData) {
        $ministryData['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('ministries', $ministryData);
    }
    
    /**
     * Find ministry by ID
     */
    public function findById($id) {
        $sql = "SELECT m.*, 
                       coordinator.first_name as coordinator_first_name,
                       coordinator.last_name as coordinator_last_name,
                       coordinator.email as coordinator_email
                FROM ministries m
                LEFT JOIN users coordinator ON m.coordinator_id = coordinator.id
                WHERE m.id = ?";
        
        return $this->db->fetch($sql, [$id]);
    }
    
    /**
     * Get all ministries
     */
    public function getAll($status = 'active') {
        $sql = "SELECT m.*, 
                       coordinator.first_name as coordinator_first_name,
                       coordinator.last_name as coordinator_last_name,
                       coordinator.email as coordinator_email
                FROM ministries m
                LEFT JOIN users coordinator ON m.coordinator_id = coordinator.id";
        
        $params = [];
        
        if ($status) {
            $sql .= " WHERE m.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY m.name";
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Update ministry
     */
    public function update($id, $data) {
        return $this->db->update('ministries', $data, 'id = :id', ['id' => $id]);
    }
    
    /**
     * Delete ministry (soft delete)
     */
    public function delete($id) {
        return $this->update($id, ['status' => 'inactive']);
    }
    
    /**
     * Get ministry statistics
     */
    public function getStatistics($ministryId = null) {
        $stats = [];
        
        if ($ministryId) {
            // Statistics for specific ministry
            $sql = "SELECT 
                        COUNT(CASE WHEN a.status = 'active' THEN 1 END) as active_aspirants,
                        COUNT(CASE WHEN a.status = 'completed' THEN 1 END) as completed_aspirants,
                        COUNT(CASE WHEN a.assigned_ministry_id = ? THEN 1 END) as assigned_aspirants
                    FROM aspirants a
                    WHERE a.ministry_preference_1 = ? OR a.ministry_preference_2 = ? OR a.ministry_preference_3 = ? OR a.assigned_ministry_id = ?";
            
            $result = $this->db->fetch($sql, [$ministryId, $ministryId, $ministryId, $ministryId, $ministryId]);
            $stats = $result;
        } else {
            // Overall ministry statistics
            $sql = "SELECT m.id, m.name,
                           COUNT(CASE WHEN a.status = 'active' AND (a.ministry_preference_1 = m.id OR a.ministry_preference_2 = m.id OR a.ministry_preference_3 = m.id) THEN 1 END) as interested_aspirants,
                           COUNT(CASE WHEN a.assigned_ministry_id = m.id THEN 1 END) as assigned_aspirants
                    FROM ministries m
                    LEFT JOIN aspirants a ON (a.ministry_preference_1 = m.id OR a.ministry_preference_2 = m.id OR a.ministry_preference_3 = m.id OR a.assigned_ministry_id = m.id)
                    WHERE m.status = 'active'
                    GROUP BY m.id, m.name
                    ORDER BY m.name";
            
            $stats = $this->db->fetchAll($sql);
        }
        
        return $stats;
    }
    
    /**
     * Get aspirants interested in ministry
     */
    public function getInterestedAspirants($ministryId) {
        $sql = "SELECT DISTINCT a.*, u.first_name, u.last_name, u.email, u.phone,
                       js.name as current_step_name,
                       CASE 
                           WHEN a.ministry_preference_1 = ? THEN 1
                           WHEN a.ministry_preference_2 = ? THEN 2
                           WHEN a.ministry_preference_3 = ? THEN 3
                           ELSE 0
                       END as preference_order
                FROM aspirants a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN journey_steps js ON a.current_step = js.step_number
                WHERE (a.ministry_preference_1 = ? OR a.ministry_preference_2 = ? OR a.ministry_preference_3 = ?)
                  AND a.status = 'active'
                ORDER BY preference_order, a.created_at";
        
        return $this->db->fetchAll($sql, [$ministryId, $ministryId, $ministryId, $ministryId, $ministryId, $ministryId]);
    }
    
    /**
     * Get assigned aspirants/volunteers
     */
    public function getAssignedVolunteers($ministryId) {
        $sql = "SELECT a.*, u.first_name, u.last_name, u.email, u.phone,
                       fa.assigned_role, fa.assignment_date, fa.status as assignment_status
                FROM aspirants a
                JOIN users u ON a.user_id = u.id
                LEFT JOIN final_assignments fa ON a.id = fa.aspirant_id
                WHERE a.assigned_ministry_id = ?
                ORDER BY fa.assignment_date DESC, a.created_at DESC";
        
        return $this->db->fetchAll($sql, [$ministryId]);
    }
    
    /**
     * Count ministries
     */
    public function count($status = 'active') {
        $sql = "SELECT COUNT(*) as count FROM ministries";
        $params = [];
        
        if ($status) {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'];
    }
    
    /**
     * Check if ministry name is unique
     */
    public function isNameUnique($name, $excludeId = null) {
        $sql = "SELECT COUNT(*) as count FROM ministries WHERE name = ?";
        $params = [$name];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] == 0;
    }
    
    /**
     * Get ministry options for forms
     */
    public function getOptions() {
        $ministries = $this->getAll('active');
        $options = [];

        foreach ($ministries as $ministry) {
            $options[$ministry['id']] = $ministry['name'];
        }

        return $options;
    }

    /**
     * Get all ministries with statistics
     */
    public function getAllWithStats() {
        $sql = "SELECT m.*,
                       coordinator.first_name as coordinator_first_name,
                       coordinator.last_name as coordinator_last_name,
                       coordinator.email as coordinator_email,
                       (SELECT COUNT(*) FROM aspirants a WHERE a.ministry_preference_1 = m.id OR a.ministry_preference_2 = m.id OR a.ministry_preference_3 = m.id) as interested_aspirants,
                       (SELECT COUNT(*) FROM aspirants a WHERE a.assigned_ministry_id = m.id AND a.status = 'active') as assigned_aspirants,
                       (SELECT COUNT(*) FROM aspirants a WHERE a.assigned_ministry_id = m.id AND a.status = 'completed') as completed_aspirants
                FROM ministries m
                LEFT JOIN users coordinator ON m.coordinator_id = coordinator.id
                ORDER BY m.name";

        return $this->db->fetchAll($sql);
    }
}
