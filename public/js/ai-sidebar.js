/**
 * AI Agent Sidebar JavaScript - STAR System
 * Handles AI sidebar interactions and chat functionality
 */

class AIAgentSidebar {
    constructor() {
        this.isCollapsed = false;
        this.chatMessages = [];
        this.isLoading = false;
        this.init();
    }
    
    init() {
        this.attachEventListeners();
        this.loadUserPreferences();
        this.startPeriodicUpdates();
    }
    
    attachEventListeners() {
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            // Ctrl/Cmd + Shift + A to toggle AI sidebar
            if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'A') {
                e.preventDefault();
                this.toggle();
            }
            
            // Escape to close chat modal
            if (e.key === 'Escape') {
                this.closeChat();
            }
        });
        
        // Chat input enter key
        document.addEventListener('keydown', (e) => {
            if (e.target.id === 'aiChatInput' && e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        // Auto-resize chat input
        const chatInput = document.getElementById('aiChatInput');
        if (chatInput) {
            chatInput.addEventListener('input', this.autoResizeInput);
        }
    }
    
    toggle() {
        const sidebar = document.getElementById('aiAgentSidebar');
        const mainContent = document.querySelector('.main-content');
        
        if (!sidebar) return;
        
        this.isCollapsed = !this.isCollapsed;
        
        if (this.isCollapsed) {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('ai-collapsed');
        } else {
            sidebar.classList.remove('collapsed');
            mainContent.classList.remove('ai-collapsed');
        }
        
        // Save preference
        this.savePreference('ai_sidebar_collapsed', this.isCollapsed);
        
        // Update notification badge visibility
        this.updateNotificationBadge();
    }
    
    async refreshInsights() {
        const insightsContainer = document.querySelector('.ai-insights');
        if (!insightsContainer) return;
        
        // Show loading state
        insightsContainer.innerHTML = '<div class="ai-loading"><div class="ai-loading-spinner"></div>Refreshing insights...</div>';
        
        try {
            const response = await fetch('/api/ai-insights/refresh', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'refresh_insights'
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.renderInsights(data.insights);
                this.showNotification('Insights refreshed successfully', 'success');
            } else {
                throw new Error(data.message || 'Failed to refresh insights');
            }
        } catch (error) {
            console.error('Error refreshing insights:', error);
            this.showNotification('Failed to refresh insights', 'error');
            insightsContainer.innerHTML = '<p class="text-muted">Failed to load insights. Please try again.</p>';
        }
    }
    
    renderInsights(insights) {
        const container = document.querySelector('.ai-insights');
        if (!container) return;
        
        if (insights.length === 0) {
            container.innerHTML = '<p class="text-muted">No new insights available.</p>';
            return;
        }
        
        const html = insights.map(insight => this.renderInsightCard(insight)).join('');
        container.innerHTML = html;
        
        // Update notification badge
        this.updateNotificationBadge(insights.length);
    }
    
    renderInsightCard(insight) {
        const content = typeof insight.content === 'string' ? JSON.parse(insight.content) : insight.content;
        const priorityClass = `priority-${insight.priority}`;
        
        return `
            <div class="ai-insight-card ${priorityClass}">
                <div class="ai-insight-header">
                    <span class="ai-insight-icon">${content.icon}</span>
                    <span class="ai-insight-priority">${insight.priority}</span>
                </div>
                <div class="ai-insight-content">
                    <h5>${this.escapeHtml(content.title)}</h5>
                    <p>${this.escapeHtml(content.message)}</p>
                    <button class="ai-insight-action" onclick="aiSidebar.handleInsightAction('${insight.insight_type}', '${this.escapeHtml(content.action)}')">
                        ${this.escapeHtml(content.action)}
                    </button>
                </div>
            </div>
        `;
    }
    
    async handleInsightAction(insightType, action) {
        // Handle different insight actions based on type
        switch (insightType) {
            case 'system_health':
                window.location.href = '/admin/system-health.php';
                break;
            case 'user_growth':
                window.location.href = '/admin/users.php?filter=recent';
                break;
            case 'security_alert':
                this.openChat('Tell me more about the security recommendations');
                break;
            case 'spiritual_development':
                window.location.href = '/aspirants.php?filter=ready_for_assessment';
                break;
            case 'ministry_alignment':
                window.location.href = '/ministries.php';
                break;
            case 'process_efficiency':
                this.openChat('How can I optimize the background check process?');
                break;
            case 'mentor_matching':
                window.location.href = '/admin/mentor-matching.php';
                break;
            case 'mentee_progress':
                window.location.href = '/mentor/mentees.php';
                break;
            case 'engagement_tip':
                this.openChat('Give me tips for better mentee engagement');
                break;
            case 'progress_insight':
                window.location.href = '/aspirant/next-steps.php';
                break;
            case 'skill_development':
                window.location.href = '/aspirant/training.php';
                break;
            default:
                this.openChat(`Help me with: ${action}`);
        }
    }
    
    openChat(initialMessage = '') {
        const modal = document.getElementById('aiChatModal');
        const input = document.getElementById('aiChatInput');
        
        if (modal) {
            modal.classList.add('active');
            
            if (initialMessage && input) {
                input.value = initialMessage;
                input.focus();
            }
        }
    }
    
    closeChat() {
        const modal = document.getElementById('aiChatModal');
        if (modal) {
            modal.classList.remove('active');
        }
    }
    
    async sendMessage() {
        const input = document.getElementById('aiChatInput');
        const messagesContainer = document.getElementById('aiChatMessages');
        
        if (!input || !messagesContainer) return;
        
        const message = input.value.trim();
        if (!message) return;
        
        // Add user message to chat
        this.addMessageToChat('user', message);
        input.value = '';
        
        // Show AI typing indicator
        this.showTypingIndicator();
        
        try {
            const response = await this.sendToAI(message);
            this.hideTypingIndicator();
            this.addMessageToChat('ai', response);
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessageToChat('ai', 'I apologize, but I encountered an error. Please try again later.');
            console.error('AI Chat Error:', error);
        }
    }
    
    addMessageToChat(sender, message) {
        const messagesContainer = document.getElementById('aiChatMessages');
        if (!messagesContainer) return;
        
        const messageElement = document.createElement('div');
        messageElement.className = 'ai-message';
        
        const isUser = sender === 'user';
        const avatar = isUser ? '👤' : '🤖';
        const alignClass = isUser ? 'user-message' : 'ai-message';
        
        messageElement.innerHTML = `
            <div class="ai-message-avatar">${avatar}</div>
            <div class="ai-message-content">
                <p>${this.escapeHtml(message)}</p>
            </div>
        `;
        
        messagesContainer.appendChild(messageElement);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    showTypingIndicator() {
        const messagesContainer = document.getElementById('aiChatMessages');
        if (!messagesContainer) return;
        
        const typingElement = document.createElement('div');
        typingElement.className = 'ai-message typing-indicator';
        typingElement.innerHTML = `
            <div class="ai-message-avatar">🤖</div>
            <div class="ai-message-content">
                <p>Thinking...</p>
            </div>
        `;
        
        messagesContainer.appendChild(typingElement);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
    
    hideTypingIndicator() {
        const typingIndicator = document.querySelector('.typing-indicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }
    
    async sendToAI(message) {
        // For now, return a mock response
        // In production, this would connect to Ollama or another AI service
        await new Promise(resolve => setTimeout(resolve, 1000 + Math.random() * 2000));
        
        const responses = [
            "I understand your question. Based on your role and current system data, here's what I recommend...",
            "That's a great question! Let me analyze the current situation and provide some insights.",
            "I can help you with that. Here are some personalized recommendations based on your STAR journey.",
            "Based on the system analytics, I notice some patterns that might be helpful for your situation.",
            "Let me provide you with some data-driven insights that could improve your workflow."
        ];
        
        return responses[Math.floor(Math.random() * responses.length)];
    }
    
    updateNotificationBadge(count = null) {
        const badge = document.getElementById('aiNotificationBadge');
        if (!badge) return;
        
        if (count !== null) {
            badge.textContent = count;
            badge.style.display = count > 0 ? 'block' : 'none';
        }
        
        // Hide badge when sidebar is expanded
        if (!this.isCollapsed) {
            badge.style.display = 'none';
        }
    }
    
    async savePreference(key, value) {
        try {
            await fetch('/api/user-preferences', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    key: key,
                    value: value.toString()
                })
            });
        } catch (error) {
            console.error('Error saving preference:', error);
        }
    }
    
    async loadUserPreferences() {
        try {
            const response = await fetch('/api/user-preferences');
            const preferences = await response.json();
            
            if (preferences.ai_sidebar_collapsed === 'true') {
                this.isCollapsed = true;
                const sidebar = document.getElementById('aiAgentSidebar');
                const mainContent = document.querySelector('.main-content');
                
                if (sidebar) sidebar.classList.add('collapsed');
                if (mainContent) mainContent.classList.add('ai-collapsed');
            }
        } catch (error) {
            console.error('Error loading preferences:', error);
        }
    }
    
    startPeriodicUpdates() {
        // Refresh insights every 5 minutes
        setInterval(() => {
            if (!this.isCollapsed) {
                this.refreshInsights();
            }
        }, 5 * 60 * 1000);
        
        // Update quick stats every minute
        setInterval(() => {
            this.updateQuickStats();
        }, 60 * 1000);
    }
    
    async updateQuickStats() {
        const statsContainer = document.querySelector('.ai-quick-stats');
        if (!statsContainer) return;
        
        try {
            const response = await fetch('/api/ai-stats');
            const stats = await response.json();
            
            if (stats.success) {
                this.renderQuickStats(stats.data);
            }
        } catch (error) {
            console.error('Error updating stats:', error);
        }
    }
    
    renderQuickStats(stats) {
        const container = document.querySelector('.ai-quick-stats');
        if (!container) return;
        
        const html = stats.map(stat => `
            <div class="ai-stat-item">
                <div class="ai-stat-icon">${stat.icon}</div>
                <div class="ai-stat-content">
                    <div class="ai-stat-value">${stat.value}</div>
                    <div class="ai-stat-label">${stat.label}</div>
                </div>
            </div>
        `).join('');
        
        container.innerHTML = html;
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `ai-notification ${type}`;
        notification.textContent = message;
        
        // Style the notification
        Object.assign(notification.style, {
            position: 'fixed',
            top: '20px',
            right: '340px',
            padding: '12px 16px',
            borderRadius: '8px',
            fontSize: '14px',
            fontWeight: '500',
            zIndex: '1000',
            boxShadow: '0 10px 15px -3px rgba(0, 0, 0, 0.1)',
            backgroundColor: type === 'success' ? '#d1fae5' : type === 'error' ? '#fee2e2' : '#e0f2fe',
            color: type === 'success' ? '#065f46' : type === 'error' ? '#991b1b' : '#0c4a6e',
            border: `1px solid ${type === 'success' ? '#a7f3d0' : type === 'error' ? '#fecaca' : '#b3e5fc'}`
        });
        
        document.body.appendChild(notification);
        
        // Remove after 3 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 3000);
    }
    
    autoResizeInput(e) {
        e.target.style.height = 'auto';
        e.target.style.height = e.target.scrollHeight + 'px';
    }
    
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Global functions for onclick handlers
function toggleAISidebar() {
    if (window.aiSidebar) {
        window.aiSidebar.toggle();
    }
}

function refreshInsights() {
    if (window.aiSidebar) {
        window.aiSidebar.refreshInsights();
    }
}

function askAI() {
    if (window.aiSidebar) {
        window.aiSidebar.openChat();
    }
}

function closeAIChat() {
    if (window.aiSidebar) {
        window.aiSidebar.closeChat();
    }
}

function sendAIMessage() {
    if (window.aiSidebar) {
        window.aiSidebar.sendMessage();
    }
}

// Initialize AI sidebar when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.aiSidebar = new AIAgentSidebar();
});
