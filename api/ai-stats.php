<?php
/**
 * AI Stats API - STAR System
 * Provides real-time statistics for AI sidebar
 */

session_start();

require_once __DIR__ . '/../src/middleware/Auth.php';
require_once __DIR__ . '/../src/models/Database.php';

// Set JSON response header
header('Content-Type: application/json');

// Check authentication
if (!Auth::check()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Authentication required']);
    exit;
}

$user = Auth::user();

try {
    $stats = generateRoleSpecificStats($user);
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function generateRoleSpecificStats($user)
{
    $db = Database::getInstance();
    $stats = [];
    
    switch ($user['role']) {
        case 'administrator':
            $stats = getAdminStats($db);
            break;
        case 'pastor':
            $stats = getPastorStats($db);
            break;
        case 'mds':
            $stats = getMDSStats($db);
            break;
        case 'mentor':
            $stats = getMentorStats($db, $user['id']);
            break;
        case 'aspirant':
            $stats = getAspirantStats($db, $user['id']);
            break;
    }
    
    return $stats;
}

function getAdminStats($db)
{
    $stats = [];
    
    // Total users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
    $totalUsers = $stmt->fetch()['count'];
    
    // Growth percentage (last 30 days vs previous 30 days)
    $stmt = $db->query("
        SELECT 
            COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as recent,
            COUNT(CASE WHEN created_at >= DATE_SUB(NOW(), INTERVAL 60 DAY) AND created_at < DATE_SUB(NOW(), INTERVAL 30 DAY) THEN 1 END) as previous
        FROM users
    ");
    $growth = $stmt->fetch();
    $growthPercent = $growth['previous'] > 0 ? round((($growth['recent'] - $growth['previous']) / $growth['previous']) * 100) : 0;
    $growthSign = $growthPercent >= 0 ? '+' : '';
    
    // System uptime (mock data)
    $uptime = '99.9%';
    
    $stats = [
        ['icon' => '👥', 'value' => $totalUsers, 'label' => 'Total Users'],
        ['icon' => '📈', 'value' => $growthSign . $growthPercent . '%', 'label' => 'Growth'],
        ['icon' => '🔒', 'value' => $uptime, 'label' => 'Uptime']
    ];
    
    return $stats;
}

function getPastorStats($db)
{
    // Total aspirants
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM aspirant_profiles ap 
        JOIN users u ON ap.user_id = u.id 
        WHERE u.status = 'active'
    ");
    $totalAspirants = $stmt->fetch()['count'];
    
    // Completion rate
    $stmt = $db->query("
        SELECT 
            COUNT(CASE WHEN ap.current_step >= 7 THEN 1 END) as completed,
            COUNT(*) as total
        FROM aspirant_profiles ap 
        JOIN users u ON ap.user_id = u.id 
        WHERE u.status = 'active'
    ");
    $completion = $stmt->fetch();
    $completionRate = $completion['total'] > 0 ? round(($completion['completed'] / $completion['total']) * 100) : 0;
    
    // Active ministries (mock data)
    $activeMinistries = 8;
    
    return [
        ['icon' => '🌟', 'value' => $totalAspirants, 'label' => 'Aspirants'],
        ['icon' => '✅', 'value' => $completionRate . '%', 'label' => 'Completion'],
        ['icon' => '⛪', 'value' => $activeMinistries, 'label' => 'Ministries']
    ];
}

function getMDSStats($db)
{
    // Pending approvals
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM aspirant_profiles ap 
        JOIN users u ON ap.user_id = u.id 
        WHERE u.status = 'active' AND ap.current_step BETWEEN 3 AND 5
    ");
    $pending = $stmt->fetch()['count'];
    
    // Available mentors
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM mentor_profiles mp 
        JOIN users u ON mp.user_id = u.id 
        WHERE u.status = 'active' AND mp.current_mentees < mp.mentoring_capacity
    ");
    $availableMentors = $stmt->fetch()['count'];
    
    // Process efficiency (mock calculation)
    $efficiency = 92;
    
    return [
        ['icon' => '📋', 'value' => $pending, 'label' => 'Pending'],
        ['icon' => '🤝', 'value' => $availableMentors, 'label' => 'Mentors'],
        ['icon' => '⚡', 'value' => $efficiency . '%', 'label' => 'Efficiency']
    ];
}

function getMentorStats($db, $mentorId)
{
    // Current mentees
    $stmt = $db->prepare("
        SELECT COUNT(*) as count 
        FROM aspirant_profiles ap 
        JOIN users u ON ap.user_id = u.id 
        WHERE ap.mentor_id = ? AND u.status = 'active'
    ");
    $stmt->execute([$mentorId]);
    $currentMentees = $stmt->fetch()['count'];
    
    // Average progress of mentees
    $stmt = $db->prepare("
        SELECT AVG(ap.current_step / 7 * 100) as avg_progress 
        FROM aspirant_profiles ap 
        JOIN users u ON ap.user_id = u.id 
        WHERE ap.mentor_id = ? AND u.status = 'active'
    ");
    $stmt->execute([$mentorId]);
    $avgProgress = round($stmt->fetch()['avg_progress'] ?? 0);
    
    // Unread messages (mock data)
    $unreadMessages = 5;
    
    return [
        ['icon' => '👥', 'value' => $currentMentees, 'label' => 'Mentees'],
        ['icon' => '📊', 'value' => $avgProgress . '%', 'label' => 'Progress'],
        ['icon' => '💬', 'value' => $unreadMessages, 'label' => 'Messages']
    ];
}

function getAspirantStats($db, $aspirantId)
{
    // Personal progress
    $stmt = $db->prepare("
        SELECT current_step, training_progress 
        FROM aspirant_profiles 
        WHERE user_id = ?
    ");
    $stmt->execute([$aspirantId]);
    $progress = $stmt->fetch();
    
    $completionPercent = $progress ? round(($progress['current_step'] / 7) * 100) : 0;
    $tasksLeft = $progress ? max(0, 7 - $progress['current_step']) : 7;
    
    // Achievements (mock data)
    $achievements = 7;
    
    return [
        ['icon' => '🎯', 'value' => $completionPercent . '%', 'label' => 'Complete'],
        ['icon' => '📚', 'value' => $tasksLeft, 'label' => 'Tasks Left'],
        ['icon' => '🏆', 'value' => $achievements, 'label' => 'Achievements']
    ];
}
?>
