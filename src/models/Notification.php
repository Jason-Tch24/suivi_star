<?php
/**
 * Notification Model for STAR Volunteer Management System
 */

require_once __DIR__ . '/Database.php';

class Notification {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Create a new notification
     */
    public function create($notificationData) {
        $notificationData['created_at'] = date('Y-m-d H:i:s');
        return $this->db->insert('notifications', $notificationData);
    }
    
    /**
     * Send notification to user
     */
    public function sendToUser($userId, $title, $message, $type = 'info', $actionUrl = null) {
        return $this->create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'type' => $type,
            'action_url' => $actionUrl,
            'is_read' => false
        ]);
    }
    
    /**
     * Send notification to multiple users
     */
    public function sendToUsers($userIds, $title, $message, $type = 'info', $actionUrl = null) {
        $results = [];
        foreach ($userIds as $userId) {
            $results[] = $this->sendToUser($userId, $title, $message, $type, $actionUrl);
        }
        return $results;
    }
    
    /**
     * Send notification to all users with specific role
     */
    public function sendToRole($role, $title, $message, $type = 'info', $actionUrl = null) {
        $sql = "SELECT id FROM users WHERE role = ? AND status = 'active'";
        $users = $this->db->fetchAll($sql, [$role]);
        
        $userIds = array_column($users, 'id');
        return $this->sendToUsers($userIds, $title, $message, $type, $actionUrl);
    }
    
    /**
     * Get notifications for user
     */
    public function getForUser($userId, $limit = 50, $unreadOnly = false) {
        $sql = "SELECT * FROM notifications WHERE user_id = ?";
        $params = [$userId];
        
        if ($unreadOnly) {
            $sql .= " AND is_read = 0";
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ?";
        $params[] = $limit;
        
        return $this->db->fetchAll($sql, $params);
    }
    
    /**
     * Get unread count for user
     */
    public function getUnreadCount($userId) {
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0",
            [$userId]
        );
        return $result['count'];
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId = null) {
        $sql = "UPDATE notifications SET is_read = 1, read_at = ? WHERE id = ?";
        $params = [date('Y-m-d H:i:s'), $notificationId];
        
        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * Mark all notifications as read for user
     */
    public function markAllAsRead($userId) {
        return $this->db->update('notifications', 
            ['is_read' => 1, 'read_at' => date('Y-m-d H:i:s')], 
            'user_id = :user_id AND is_read = 0', 
            ['user_id' => $userId]
        );
    }
    
    /**
     * Delete notification
     */
    public function delete($notificationId, $userId = null) {
        $sql = "DELETE FROM notifications WHERE id = ?";
        $params = [$notificationId];
        
        if ($userId) {
            $sql .= " AND user_id = ?";
            $params[] = $userId;
        }
        
        return $this->db->query($sql, $params);
    }
    
    /**
     * Clean up old notifications (older than specified days)
     */
    public function cleanup($daysOld = 30) {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$daysOld} days"));
        return $this->db->query(
            "DELETE FROM notifications WHERE created_at < ? AND is_read = 1",
            [$cutoffDate]
        );
    }
    
    /**
     * Get notification statistics
     */
    public function getStatistics() {
        $stats = [];
        
        // Total notifications
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM notifications");
        $stats['total'] = $result['count'];
        
        // Unread notifications
        $result = $this->db->fetch("SELECT COUNT(*) as count FROM notifications WHERE is_read = 0");
        $stats['unread'] = $result['count'];
        
        // By type
        $typeStats = $this->db->fetchAll("SELECT type, COUNT(*) as count FROM notifications GROUP BY type");
        foreach ($typeStats as $stat) {
            $stats['by_type'][$stat['type']] = $stat['count'];
        }
        
        // Recent activity (last 7 days)
        $result = $this->db->fetch(
            "SELECT COUNT(*) as count FROM notifications WHERE created_at >= ?",
            [date('Y-m-d H:i:s', strtotime('-7 days'))]
        );
        $stats['recent'] = $result['count'];
        
        return $stats;
    }
    
    /**
     * Send system-wide announcement
     */
    public function sendAnnouncement($title, $message, $excludeRoles = []) {
        $sql = "SELECT id FROM users WHERE status = 'active'";
        $params = [];
        
        if (!empty($excludeRoles)) {
            $placeholders = str_repeat('?,', count($excludeRoles) - 1) . '?';
            $sql .= " AND role NOT IN ($placeholders)";
            $params = $excludeRoles;
        }
        
        $users = $this->db->fetchAll($sql, $params);
        $userIds = array_column($users, 'id');
        
        return $this->sendToUsers($userIds, $title, $message, 'info');
    }
    
    /**
     * Send step progression notifications
     */
    public function sendStepProgressionNotification($aspirantId, $stepName, $status) {
        // Get aspirant user ID
        $aspirant = $this->db->fetch("SELECT user_id FROM aspirants WHERE id = ?", [$aspirantId]);
        if (!$aspirant) return false;
        
        $messages = [
            'completed' => "Congratulations! You have completed the '{$stepName}' step of your STAR journey.",
            'rejected' => "Your '{$stepName}' step requires attention. Please contact your administrator.",
            'in_progress' => "You have started the '{$stepName}' step of your STAR journey.",
            'extended' => "The deadline for your '{$stepName}' step has been extended."
        ];
        
        $types = [
            'completed' => 'success',
            'rejected' => 'error',
            'in_progress' => 'info',
            'extended' => 'warning'
        ];
        
        $message = $messages[$status] ?? "Your '{$stepName}' step status has been updated.";
        $type = $types[$status] ?? 'info';
        
        return $this->sendToUser(
            $aspirant['user_id'],
            'STAR Journey Update',
            $message,
            $type,
            '/dashboard/aspirant'
        );
    }
    
    /**
     * Send mentor assignment notification
     */
    public function sendMentorAssignmentNotification($aspirantId, $mentorId) {
        // Get aspirant and mentor info
        $aspirant = $this->db->fetch(
            "SELECT u.first_name, u.last_name, a.user_id FROM aspirants a JOIN users u ON a.user_id = u.id WHERE a.id = ?",
            [$aspirantId]
        );
        
        $mentor = $this->db->fetch("SELECT first_name, last_name FROM users WHERE id = ?", [$mentorId]);
        
        if (!$aspirant || !$mentor) return false;
        
        // Notify aspirant
        $this->sendToUser(
            $aspirant['user_id'],
            'Mentor Assigned',
            "You have been assigned a mentor: {$mentor['first_name']} {$mentor['last_name']}. They will guide you through your ministry training.",
            'success',
            '/dashboard/aspirant'
        );
        
        // Notify mentor
        $this->sendToUser(
            $mentorId,
            'New Aspirant Assigned',
            "You have been assigned a new aspirant: {$aspirant['first_name']} {$aspirant['last_name']}. Please begin their ministry training.",
            'info',
            '/dashboard/mentor'
        );
        
        return true;
    }
}
