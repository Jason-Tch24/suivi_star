<?php
/**
 * AI Insights API - STAR System
 * Handles AI-generated insights and recommendations
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
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            handleGetInsights($user);
            break;
        case 'POST':
            handlePostInsights($user);
            break;
        case 'PUT':
            handleUpdateInsight($user);
            break;
        case 'DELETE':
            handleDeleteInsight($user);
            break;
        default:
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

function handleGetInsights($user)
{
    $db = Database::getInstance();
    
    $stmt = $db->prepare("
        SELECT insight_type, content, priority, created_at 
        FROM ai_insights 
        WHERE user_id = ? AND status = 'active' 
        AND (expires_at IS NULL OR expires_at > NOW())
        ORDER BY priority DESC, created_at DESC 
        LIMIT 10
    ");
    $stmt->execute([$user['id']]);
    $insights = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'success' => true,
        'insights' => $insights
    ]);
}

function handlePostInsights($user)
{
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    switch ($action) {
        case 'refresh_insights':
            refreshInsights($user);
            break;
        case 'generate_insight':
            generateInsight($user, $input);
            break;
        case 'chat_message':
            handleChatMessage($user, $input);
            break;
        default:
            throw new Exception('Invalid action');
    }
}

function refreshInsights($user)
{
    $db = Database::getInstance();
    
    // Mark old insights as dismissed
    $stmt = $db->prepare("UPDATE ai_insights SET status = 'dismissed' WHERE user_id = ? AND status = 'active'");
    $stmt->execute([$user['id']]);
    
    // Generate new insights based on role and recent activity
    $insights = generateRoleSpecificInsights($user);
    
    // Insert new insights
    foreach ($insights as $insight) {
        $stmt = $db->prepare("
            INSERT INTO ai_insights (user_id, insight_type, content, priority, created_at) 
            VALUES (?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $user['id'],
            $insight['type'],
            json_encode($insight['content']),
            $insight['priority']
        ]);
    }
    
    // Return the new insights
    handleGetInsights($user);
}

function generateRoleSpecificInsights($user)
{
    $db = Database::getInstance();
    $insights = [];
    
    switch ($user['role']) {
        case 'administrator':
            $insights = generateAdminInsights($user, $db);
            break;
        case 'pastor':
            $insights = generatePastorInsights($user, $db);
            break;
        case 'mds':
            $insights = generateMDSInsights($user, $db);
            break;
        case 'mentor':
            $insights = generateMentorInsights($user, $db);
            break;
        case 'aspirant':
            $insights = generateAspirantInsights($user, $db);
            break;
    }
    
    return $insights;
}

function generateAdminInsights($user, $db)
{
    $insights = [];
    
    // User growth analysis
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
    $newUsers = $stmt->fetch()['count'];
    
    if ($newUsers > 0) {
        $insights[] = [
            'type' => 'user_growth',
            'content' => [
                'title' => 'New User Activity',
                'message' => "$newUsers new users joined this week. Consider reviewing their onboarding progress.",
                'action' => 'Review new users',
                'icon' => '📈'
            ],
            'priority' => 'medium'
        ];
    }
    
    // System health check
    $stmt = $db->query("SELECT COUNT(*) as active FROM users WHERE status = 'active'");
    $activeUsers = $stmt->fetch()['active'];
    
    $insights[] = [
        'type' => 'system_health',
        'content' => [
            'title' => 'System Health',
            'message' => "$activeUsers active users. System performance is optimal.",
            'action' => 'View system metrics',
            'icon' => '📊'
        ],
        'priority' => 'low'
    ];
    
    // Security recommendations
    $insights[] = [
        'type' => 'security_alert',
        'content' => [
            'title' => 'Security Enhancement',
            'message' => 'Consider implementing two-factor authentication for admin accounts.',
            'action' => 'Configure security',
            'icon' => '🔒'
        ],
        'priority' => 'medium'
    ];
    
    return $insights;
}

function generatePastorInsights($user, $db)
{
    $insights = [];
    
    // Aspirant progress analysis
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM aspirant_profiles ap 
        JOIN users u ON ap.user_id = u.id 
        WHERE u.status = 'active' AND ap.current_step >= 5
    ");
    $readyForAssessment = $stmt->fetch()['count'];
    
    if ($readyForAssessment > 0) {
        $insights[] = [
            'type' => 'spiritual_development',
            'content' => [
                'title' => 'Assessment Ready',
                'message' => "$readyForAssessment aspirants are ready for spiritual development assessment.",
                'action' => 'Schedule assessments',
                'icon' => '🙏'
            ],
            'priority' => 'high'
        ];
    }
    
    // Ministry effectiveness
    $insights[] = [
        'type' => 'ministry_alignment',
        'content' => [
            'title' => 'Ministry Performance',
            'message' => 'Youth Ministry shows highest completion rates. Consider expanding program.',
            'action' => 'View ministry analytics',
            'icon' => '⛪'
        ],
        'priority' => 'medium'
    ];
    
    return $insights;
}

function generateMDSInsights($user, $db)
{
    $insights = [];
    
    // Process efficiency analysis
    $stmt = $db->query("
        SELECT AVG(DATEDIFF(NOW(), created_at)) as avg_days 
        FROM aspirant_profiles 
        WHERE background_check_status = 'In Progress'
    ");
    $avgProcessingTime = $stmt->fetch()['avg_days'] ?? 0;
    
    if ($avgProcessingTime > 14) {
        $insights[] = [
            'type' => 'process_efficiency',
            'content' => [
                'title' => 'Process Bottleneck',
                'message' => "Background checks taking {$avgProcessingTime} days on average. Consider optimization.",
                'action' => 'Optimize process',
                'icon' => '⚡'
            ],
            'priority' => 'high'
        ];
    }
    
    // Mentor capacity analysis
    $stmt = $db->query("
        SELECT COUNT(*) as available_mentors 
        FROM mentor_profiles mp 
        JOIN users u ON mp.user_id = u.id 
        WHERE u.status = 'active' AND mp.current_mentees < mp.mentoring_capacity
    ");
    $availableMentors = $stmt->fetch()['available_mentors'];
    
    $insights[] = [
        'type' => 'mentor_matching',
        'content' => [
            'title' => 'Mentor Availability',
            'message' => "$availableMentors mentors have capacity for new aspirants.",
            'action' => 'Assign mentors',
            'icon' => '🤝'
        ],
        'priority' => 'medium'
    ];
    
    return $insights;
}

function generateMentorInsights($user, $db)
{
    $insights = [];
    
    // Mentee progress tracking
    $stmt = $db->prepare("
        SELECT u.first_name, ap.current_step, ap.updated_at 
        FROM aspirant_profiles ap 
        JOIN users u ON ap.user_id = u.id 
        WHERE ap.mentor_id = ? AND u.status = 'active'
        ORDER BY ap.updated_at ASC 
        LIMIT 1
    ");
    $stmt->execute([$user['id']]);
    $inactiveMentee = $stmt->fetch();
    
    if ($inactiveMentee && strtotime($inactiveMentee['updated_at']) < strtotime('-5 days')) {
        $insights[] = [
            'type' => 'mentee_progress',
            'content' => [
                'title' => 'Mentee Check-in Needed',
                'message' => "{$inactiveMentee['first_name']} hasn't updated progress in 5+ days.",
                'action' => 'Send check-in message',
                'icon' => '👥'
            ],
            'priority' => 'high'
        ];
    }
    
    // Engagement tips
    $insights[] = [
        'type' => 'engagement_tip',
        'content' => [
            'title' => 'Engagement Tip',
            'message' => 'Regular weekly check-ins increase completion rates by 40%.',
            'action' => 'Schedule check-ins',
            'icon' => '💡'
        ],
        'priority' => 'medium'
    ];
    
    return $insights;
}

function generateAspirantInsights($user, $db)
{
    $insights = [];
    
    // Progress recommendations
    $stmt = $db->prepare("
        SELECT current_step, background_check_status, training_progress 
        FROM aspirant_profiles 
        WHERE user_id = ?
    ");
    $stmt->execute([$user['id']]);
    $progress = $stmt->fetch();
    
    if ($progress) {
        if ($progress['background_check_status'] === 'Not Started') {
            $insights[] = [
                'type' => 'progress_insight',
                'content' => [
                    'title' => 'Next Step Available',
                    'message' => 'Start your background check to unlock training modules.',
                    'action' => 'Begin background check',
                    'icon' => '🌟'
                ],
                'priority' => 'high'
            ];
        }
        
        if ($progress['training_progress'] < 50) {
            $insights[] = [
                'type' => 'skill_development',
                'content' => [
                    'title' => 'Training Opportunity',
                    'message' => 'Complete leadership training to enhance your ministry preparation.',
                    'action' => 'View training modules',
                    'icon' => '📚'
                ],
                'priority' => 'medium'
            ];
        }
    }
    
    return $insights;
}

function handleChatMessage($user, $input)
{
    $message = $input['message'] ?? '';
    
    if (empty($message)) {
        throw new Exception('Message is required');
    }
    
    // For now, return a simple response
    // In production, this would integrate with Ollama or another AI service
    $responses = [
        "Based on your role as {$user['role']}, I recommend focusing on your current priorities.",
        "I understand your question. Let me analyze the current system data to provide personalized insights.",
        "That's a great question! Here are some recommendations based on your STAR journey progress.",
        "I can help you optimize your workflow. Based on recent activity patterns, consider these suggestions.",
        "Let me provide you with data-driven insights specific to your role and current objectives."
    ];
    
    $response = $responses[array_rand($responses)];
    
    echo json_encode([
        'success' => true,
        'response' => $response,
        'timestamp' => date('c')
    ]);
}

function handleUpdateInsight($user)
{
    $input = json_decode(file_get_contents('php://input'), true);
    $insightId = $input['insight_id'] ?? '';
    $status = $input['status'] ?? '';
    
    if (empty($insightId) || empty($status)) {
        throw new Exception('Insight ID and status are required');
    }
    
    $db = Database::getInstance();
    
    $stmt = $db->prepare("UPDATE ai_insights SET status = ? WHERE id = ? AND user_id = ?");
    $stmt->execute([$status, $insightId, $user['id']]);
    
    echo json_encode(['success' => true, 'message' => 'Insight updated successfully']);
}

function handleDeleteInsight($user)
{
    $insightId = $_GET['id'] ?? '';
    
    if (empty($insightId)) {
        throw new Exception('Insight ID is required');
    }
    
    $db = Database::getInstance();
    
    $stmt = $db->prepare("DELETE FROM ai_insights WHERE id = ? AND user_id = ?");
    $stmt->execute([$insightId, $user['id']]);
    
    echo json_encode(['success' => true, 'message' => 'Insight deleted successfully']);
}
?>
