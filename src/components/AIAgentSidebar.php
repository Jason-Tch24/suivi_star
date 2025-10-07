<?php
/**
 * AI Agent Sidebar Component - STAR System
 * Contextual AI assistant with role-specific insights
 */

class AIAgentSidebar
{
    private $user;
    private $insights;
    private $isEnabled;
    private $isCollapsed;
    
    public function __construct($user)
    {
        $this->user = $user;
        $this->loadUserPreferences();
        $this->loadInsights();
    }
    
    private function loadUserPreferences()
    {
        $db = Database::getInstance();
        
        // Get AI sidebar preferences
        $stmt = $db->prepare("SELECT preference_key, preference_value FROM user_preferences WHERE user_id = ? AND preference_key IN ('ai_sidebar_enabled', 'ai_sidebar_collapsed')");
        $stmt->execute([$this->user['id']]);
        $preferences = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        
        $this->isEnabled = ($preferences['ai_sidebar_enabled'] ?? 'true') === 'true';
        $this->isCollapsed = ($preferences['ai_sidebar_collapsed'] ?? 'false') === 'true';
    }
    
    private function loadInsights()
    {
        if (!$this->isEnabled) {
            $this->insights = [];
            return;
        }
        
        $db = Database::getInstance();
        
        // Get active insights for the user
        $stmt = $db->prepare("
            SELECT insight_type, content, priority, created_at 
            FROM ai_insights 
            WHERE user_id = ? AND status = 'active' 
            AND (expires_at IS NULL OR expires_at > NOW())
            ORDER BY priority DESC, created_at DESC 
            LIMIT 10
        ");
        $stmt->execute([$this->user['id']]);
        $this->insights = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // If no insights exist, generate some based on role
        if (empty($this->insights)) {
            $this->generateInitialInsights();
        }
    }
    
    private function generateInitialInsights()
    {
        $insights = $this->getRoleSpecificInsights($this->user['role']);
        
        $db = Database::getInstance();
        foreach ($insights as $insight) {
            $stmt = $db->prepare("
                INSERT INTO ai_insights (user_id, insight_type, content, priority, created_at) 
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $this->user['id'],
                $insight['type'],
                json_encode($insight['content']),
                $insight['priority']
            ]);
        }
        
        // Reload insights
        $this->loadInsights();
    }
    
    private function getRoleSpecificInsights($role)
    {
        $insights = [];
        
        switch ($role) {
            case 'administrator':
                $insights = [
                    [
                        'type' => 'system_health',
                        'content' => [
                            'title' => 'System Health Check',
                            'message' => 'All systems are running optimally. User activity is up 15% this month.',
                            'action' => 'View detailed metrics',
                            'icon' => '📊'
                        ],
                        'priority' => 'medium'
                    ],
                    [
                        'type' => 'user_growth',
                        'content' => [
                            'title' => 'User Growth Analytics',
                            'message' => '5 new aspirants joined this week. Consider assigning mentors.',
                            'action' => 'Manage assignments',
                            'icon' => '📈'
                        ],
                        'priority' => 'high'
                    ],
                    [
                        'type' => 'security_alert',
                        'content' => [
                            'title' => 'Security Recommendation',
                            'message' => 'Enable two-factor authentication for all admin accounts.',
                            'action' => 'Configure 2FA',
                            'icon' => '🔒'
                        ],
                        'priority' => 'medium'
                    ]
                ];
                break;
                
            case 'pastor':
                $insights = [
                    [
                        'type' => 'spiritual_development',
                        'content' => [
                            'title' => 'Aspirant Progress Insight',
                            'message' => '3 aspirants are ready for spiritual development assessment.',
                            'action' => 'Schedule assessments',
                            'icon' => '🙏'
                        ],
                        'priority' => 'high'
                    ],
                    [
                        'type' => 'ministry_alignment',
                        'content' => [
                            'title' => 'Ministry Alignment',
                            'message' => 'Youth Ministry has the highest completion rate (85%).',
                            'action' => 'View ministry stats',
                            'icon' => '⛪'
                        ],
                        'priority' => 'medium'
                    ]
                ];
                break;
                
            case 'mds':
                $insights = [
                    [
                        'type' => 'process_efficiency',
                        'content' => [
                            'title' => 'Process Bottleneck Detected',
                            'message' => 'Background checks are taking 20% longer than average.',
                            'action' => 'Optimize process',
                            'icon' => '⚡'
                        ],
                        'priority' => 'high'
                    ],
                    [
                        'type' => 'mentor_matching',
                        'content' => [
                            'title' => 'Mentor-Aspirant Matching',
                            'message' => 'AI suggests 3 optimal mentor-aspirant pairings.',
                            'action' => 'Review suggestions',
                            'icon' => '🤝'
                        ],
                        'priority' => 'medium'
                    ]
                ];
                break;
                
            case 'mentor':
                $insights = [
                    [
                        'type' => 'mentee_progress',
                        'content' => [
                            'title' => 'Mentee Progress Alert',
                            'message' => 'Sarah has been inactive for 5 days. Consider reaching out.',
                            'action' => 'Send message',
                            'icon' => '👥'
                        ],
                        'priority' => 'high'
                    ],
                    [
                        'type' => 'engagement_tip',
                        'content' => [
                            'title' => 'Engagement Improvement',
                            'message' => 'Weekly check-ins increase completion rates by 40%.',
                            'action' => 'Schedule check-ins',
                            'icon' => '💡'
                        ],
                        'priority' => 'medium'
                    ]
                ];
                break;
                
            case 'aspirant':
                $insights = [
                    [
                        'type' => 'progress_insight',
                        'content' => [
                            'title' => 'Next Step Recommendation',
                            'message' => 'Complete your background check to unlock training modules.',
                            'action' => 'Start background check',
                            'icon' => '🌟'
                        ],
                        'priority' => 'high'
                    ],
                    [
                        'type' => 'skill_development',
                        'content' => [
                            'title' => 'Skill Development',
                            'message' => 'Based on your ministry choice, consider leadership training.',
                            'action' => 'View training options',
                            'icon' => '📚'
                        ],
                        'priority' => 'medium'
                    ]
                ];
                break;
        }
        
        return $insights;
    }
    
    public function render()
    {
        if (!$this->isEnabled) {
            return '';
        }
        
        $collapsedClass = $this->isCollapsed ? 'collapsed' : '';
        $roleColor = $this->getRoleColor($this->user['role']);
        
        ob_start();
        ?>
        <div id="aiAgentSidebar" class="ai-agent-sidebar <?php echo $collapsedClass; ?>">
            <!-- Sidebar Header -->
            <div class="ai-sidebar-header">
                <div class="ai-header-content">
                    <div class="ai-avatar" style="background: <?php echo $roleColor; ?>;">
                        🤖
                    </div>
                    <div class="ai-title">
                        <h3>AI Assistant</h3>
                        <p>Personalized insights for <?php echo ucfirst($this->user['role']); ?></p>
                    </div>
                </div>
                <button class="ai-toggle-btn" onclick="toggleAISidebar()">
                    <span class="collapse-icon">→</span>
                    <span class="expand-icon">←</span>
                </button>
            </div>
            
            <!-- Sidebar Content -->
            <div class="ai-sidebar-content">
                <!-- Quick Stats -->
                <div class="ai-section">
                    <h4 class="ai-section-title">Quick Insights</h4>
                    <div class="ai-quick-stats">
                        <?php echo $this->renderQuickStats(); ?>
                    </div>
                </div>
                
                <!-- AI Insights -->
                <div class="ai-section">
                    <h4 class="ai-section-title">AI Recommendations</h4>
                    <div class="ai-insights">
                        <?php foreach ($this->insights as $insight): ?>
                            <?php echo $this->renderInsight($insight); ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Actions -->
                <div class="ai-section">
                    <div class="ai-actions">
                        <button class="ai-action-btn" onclick="refreshInsights()">
                            🔄 Refresh Insights
                        </button>
                        <button class="ai-action-btn" onclick="askAI()">
                            💬 Ask AI
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Collapsed State -->
            <div class="ai-collapsed-content">
                <div class="ai-collapsed-avatar" style="background: <?php echo $roleColor; ?>;">
                    🤖
                </div>
                <div class="ai-notification-badge" id="aiNotificationBadge">
                    <?php echo count($this->insights); ?>
                </div>
            </div>
        </div>
        
        <!-- AI Chat Modal -->
        <div id="aiChatModal" class="modal-overlay">
            <div class="modal-content ai-chat-modal">
                <div class="modal-header">
                    <h2 class="modal-title">AI Assistant Chat</h2>
                    <button class="modal-close" onclick="closeAIChat()">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="ai-chat-messages" id="aiChatMessages">
                        <div class="ai-message">
                            <div class="ai-message-avatar">🤖</div>
                            <div class="ai-message-content">
                                <p>Hello! I'm your AI assistant. How can I help you with your STAR journey today?</p>
                            </div>
                        </div>
                    </div>
                    <div class="ai-chat-input">
                        <input type="text" id="aiChatInput" placeholder="Ask me anything..." class="form-input">
                        <button onclick="sendAIMessage()" class="btn btn-primary">Send</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
    
    private function renderQuickStats()
    {
        $stats = $this->getQuickStats();
        $html = '';
        
        foreach ($stats as $stat) {
            $html .= sprintf(
                '<div class="ai-stat-item">
                    <div class="ai-stat-icon">%s</div>
                    <div class="ai-stat-content">
                        <div class="ai-stat-value">%s</div>
                        <div class="ai-stat-label">%s</div>
                    </div>
                </div>',
                $stat['icon'],
                $stat['value'],
                $stat['label']
            );
        }
        
        return $html;
    }
    
    private function getQuickStats()
    {
        $role = $this->user['role'];
        $stats = [];
        
        switch ($role) {
            case 'administrator':
                $stats = [
                    ['icon' => '👥', 'value' => '127', 'label' => 'Total Users'],
                    ['icon' => '📈', 'value' => '+15%', 'label' => 'Growth'],
                    ['icon' => '🔒', 'value' => '99.9%', 'label' => 'Uptime']
                ];
                break;
            case 'pastor':
                $stats = [
                    ['icon' => '🌟', 'value' => '23', 'label' => 'Aspirants'],
                    ['icon' => '✅', 'value' => '85%', 'label' => 'Completion'],
                    ['icon' => '⛪', 'value' => '8', 'label' => 'Ministries']
                ];
                break;
            case 'mds':
                $stats = [
                    ['icon' => '📋', 'value' => '12', 'label' => 'Pending'],
                    ['icon' => '🤝', 'value' => '18', 'label' => 'Mentors'],
                    ['icon' => '⚡', 'value' => '92%', 'label' => 'Efficiency']
                ];
                break;
            case 'mentor':
                $stats = [
                    ['icon' => '👥', 'value' => '3', 'label' => 'Mentees'],
                    ['icon' => '📊', 'value' => '78%', 'label' => 'Progress'],
                    ['icon' => '💬', 'value' => '5', 'label' => 'Messages']
                ];
                break;
            case 'aspirant':
                $stats = [
                    ['icon' => '🎯', 'value' => '43%', 'label' => 'Complete'],
                    ['icon' => '📚', 'value' => '2', 'label' => 'Tasks Left'],
                    ['icon' => '🏆', 'value' => '7', 'label' => 'Achievements']
                ];
                break;
        }
        
        return $stats;
    }
    
    private function renderInsight($insight)
    {
        $content = json_decode($insight['content'], true);
        $priorityClass = 'priority-' . $insight['priority'];
        
        return sprintf(
            '<div class="ai-insight-card %s">
                <div class="ai-insight-header">
                    <span class="ai-insight-icon">%s</span>
                    <span class="ai-insight-priority">%s</span>
                </div>
                <div class="ai-insight-content">
                    <h5>%s</h5>
                    <p>%s</p>
                    <button class="ai-insight-action" onclick="handleInsightAction(\'%s\', \'%s\')">
                        %s
                    </button>
                </div>
            </div>',
            $priorityClass,
            $content['icon'],
            ucfirst($insight['priority']),
            htmlspecialchars($content['title']),
            htmlspecialchars($content['message']),
            $insight['insight_type'],
            htmlspecialchars($content['action']),
            htmlspecialchars($content['action'])
        );
    }
    
    private function getRoleColor($role)
    {
        $colors = [
            'administrator' => '#dc2626',
            'pastor' => '#059669',
            'mds' => '#2563eb',
            'mentor' => '#d97706',
            'aspirant' => '#7c3aed'
        ];
        
        return $colors[$role] ?? '#6b7280';
    }
    
    public function updatePreference($key, $value)
    {
        $db = Database::getInstance();
        
        $stmt = $db->prepare("
            INSERT INTO user_preferences (user_id, preference_key, preference_value) 
            VALUES (?, ?, ?) 
            ON DUPLICATE KEY UPDATE preference_value = VALUES(preference_value)
        ");
        $stmt->execute([$this->user['id'], $key, $value]);
    }
}
?>
