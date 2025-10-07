<?php
/**
 * AI Agent Sidebar Component - STAR System
 * Provides role-specific AI insights and recommendations
 */

class AISidebar {
    private $user;
    private $role;
    private $insights;
    
    public function __construct($user, $role) {
        $this->user = $user;
        $this->role = $role;
        $this->insights = $this->generateRoleSpecificInsights();
    }
    
    private function generateRoleSpecificInsights() {
        switch ($this->role) {
            case 'administrator':
                return $this->getAdministratorInsights();
            case 'pastor':
                return $this->getPastorInsights();
            case 'mds':
                return $this->getMDSInsights();
            case 'mentor':
                return $this->getMentorInsights();
            case 'aspirant':
                return $this->getAspirantInsights();
            default:
                return [];
        }
    }
    
    private function getAdministratorInsights() {
        return [
            [
                'priority' => 'HIGH',
                'icon' => '📈',
                'title' => 'User Growth Analytics',
                'description' => '5 new aspirants joined this week. Consider assigning mentors.',
                'action' => 'Manage assignments',
                'action_url' => 'admin/assignments.php'
            ],
            [
                'priority' => 'MEDIUM',
                'icon' => '🔒',
                'title' => 'Security Recommendation',
                'description' => 'Enable two-factor authentication for all admin accounts.',
                'action' => 'Configure 2FA',
                'action_url' => 'admin/security.php'
            ],
            [
                'priority' => 'LOW',
                'icon' => '📊',
                'title' => 'System Health Check',
                'description' => 'All systems are running optimally. User engagement is up 15%.',
                'action' => 'View metrics',
                'action_url' => 'admin/metrics.php'
            ]
        ];
    }
    
    private function getPastorInsights() {
        return [
            [
                'priority' => 'HIGH',
                'icon' => '⛪',
                'title' => 'Ministry Capacity Alert',
                'description' => 'Youth Ministry is at 95% capacity. Consider expanding or creating new opportunities.',
                'action' => 'Review capacity',
                'action_url' => 'pastor/ministry-capacity.php'
            ],
            [
                'priority' => 'MEDIUM',
                'icon' => '📋',
                'title' => 'Final Assignments Pending',
                'description' => '8 aspirants have completed all steps and are awaiting final ministry assignments.',
                'action' => 'Review assignments',
                'action_url' => 'pastor/assignments.php'
            ],
            [
                'priority' => 'MEDIUM',
                'icon' => '📈',
                'title' => 'Program Performance',
                'description' => 'STAR program completion rate is 78% - above target! Consider sharing best practices.',
                'action' => 'Generate report',
                'action_url' => 'pastor/reports.php'
            ],
            [
                'priority' => 'LOW',
                'icon' => '🎯',
                'title' => 'Seasonal Planning',
                'description' => 'Plan for increased volunteer needs during upcoming holiday season.',
                'action' => 'View calendar',
                'action_url' => 'pastor/calendar.php'
            ]
        ];
    }
    
    private function getMDSInsights() {
        return [
            [
                'priority' => 'HIGH',
                'icon' => '🎤',
                'title' => 'Interviews Scheduled',
                'description' => '6 aspirants are ready for MDS interviews this week. Review their profiles.',
                'action' => 'View interviews',
                'action_url' => 'mds/interviews.php'
            ],
            [
                'priority' => 'MEDIUM',
                'icon' => '📝',
                'title' => 'PCNC Training Review',
                'description' => '3 aspirants completed PCNC training and need validation before interviews.',
                'action' => 'Review training',
                'action_url' => 'mds/training-review.php'
            ],
            [
                'priority' => 'LOW',
                'icon' => '📊',
                'title' => 'Interview Success Rate',
                'description' => 'Your interview approval rate is 85% - excellent discernment!',
                'action' => 'View analytics',
                'action_url' => 'mds/analytics.php'
            ]
        ];
    }
    
    private function getMentorInsights() {
        return [
            [
                'priority' => 'HIGH',
                'icon' => '🤝',
                'title' => 'Mentee Check-in Due',
                'description' => '2 of your mentees haven\'t had check-ins in over 2 weeks. Schedule meetings.',
                'action' => 'Schedule check-ins',
                'action_url' => 'mentor/schedule.php'
            ],
            [
                'priority' => 'MEDIUM',
                'icon' => '📚',
                'title' => 'Training Resources',
                'description' => 'New mentoring resources available for Step 2 guidance.',
                'action' => 'View resources',
                'action_url' => 'mentor/resources.php'
            ],
            [
                'priority' => 'LOW',
                'icon' => '🌟',
                'title' => 'Mentee Progress',
                'description' => 'Your mentees show 20% faster progress than average. Great work!',
                'action' => 'View progress',
                'action_url' => 'mentor/progress.php'
            ]
        ];
    }
    
    private function getAspirantInsights() {
        return [
            [
                'priority' => 'HIGH',
                'icon' => '📋',
                'title' => 'Next Step Ready',
                'description' => 'You\'ve completed all requirements for your current step. Ready to advance!',
                'action' => 'Advance step',
                'action_url' => 'aspirant/advance.php'
            ],
            [
                'priority' => 'MEDIUM',
                'icon' => '📅',
                'title' => 'Upcoming Deadline',
                'description' => 'PCNC training session deadline is in 5 days. Don\'t miss it!',
                'action' => 'View schedule',
                'action_url' => 'aspirant/schedule.php'
            ],
            [
                'priority' => 'LOW',
                'icon' => '🎯',
                'title' => 'Ministry Matching',
                'description' => 'Based on your interests, we found 3 potential ministry matches.',
                'action' => 'Explore matches',
                'action_url' => 'aspirant/ministry-matches.php'
            ]
        ];
    }
    
    public function render() {
        $roleColors = [
            'administrator' => 'var(--role-administrator)',
            'pastor' => 'var(--role-pastor)',
            'mds' => 'var(--role-mds)',
            'mentor' => 'var(--role-mentor)',
            'aspirant' => 'var(--role-aspirant)'
        ];
        
        $roleColor = $roleColors[$this->role] ?? 'var(--primary-600)';
        $roleName = ucfirst($this->role);
        
        ob_start();
        ?>
        <div class="ai-agent-sidebar" id="aiSidebar">
            <div class="ai-sidebar-header">
                <div class="ai-header-content">
                    <div class="ai-avatar" style="background: linear-gradient(135deg, <?php echo $roleColor; ?> 0%, <?php echo $roleColor; ?>80 100%);">
                        🤖
                    </div>
                    <div class="ai-header-text">
                        <div class="ai-title">AI Assistant</div>
                        <div class="ai-subtitle">Personalized insights for <?php echo $roleName; ?></div>
                    </div>
                </div>
                <button class="ai-toggle-btn" onclick="toggleAISidebar()">
                    <span class="toggle-icon">⟨</span>
                </button>
            </div>
            
            <div class="ai-sidebar-content">
                <div class="ai-insights-section">
                    <h3 class="ai-section-title">AI RECOMMENDATIONS</h3>
                    
                    <?php foreach ($this->insights as $insight): ?>
                        <div class="ai-insight-card priority-<?php echo strtolower($insight['priority']); ?>">
                            <div class="ai-insight-header">
                                <div class="ai-insight-icon"><?php echo $insight['icon']; ?></div>
                                <div class="ai-insight-priority"><?php echo $insight['priority']; ?></div>
                            </div>
                            <div class="ai-insight-title"><?php echo htmlspecialchars($insight['title']); ?></div>
                            <div class="ai-insight-description"><?php echo htmlspecialchars($insight['description']); ?></div>
                            <button class="ai-action-btn" onclick="location.href='<?php echo $insight['action_url']; ?>'"><?php echo htmlspecialchars($insight['action']); ?></button>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="ai-actions-section">
                    <button class="ai-refresh-btn" onclick="refreshAIInsights()">🔄 Refresh insights</button>
                    <button class="ai-chat-btn" onclick="openAIChat()">💬 Ask AI</button>
                </div>
            </div>
        </div>
        
        <script>
            function toggleAISidebar() {
                const sidebar = document.getElementById('aiSidebar');
                sidebar.classList.toggle('collapsed');
            }
            
            function refreshAIInsights() {
                // Simulate refresh with loading state
                const refreshBtn = document.querySelector('.ai-refresh-btn');
                const originalText = refreshBtn.textContent;
                refreshBtn.textContent = '🔄 Refreshing...';
                refreshBtn.disabled = true;
                
                setTimeout(() => {
                    refreshBtn.textContent = originalText;
                    refreshBtn.disabled = false;
                    // In a real implementation, this would fetch new insights
                }, 2000);
            }
            
            function openAIChat() {
                // In a real implementation, this would open a chat modal
                alert('AI Chat functionality - would open chat interface for <?php echo $roleName; ?> specific assistance');
            }
        </script>
        <?php
        return ob_get_clean();
    }
}
?>
