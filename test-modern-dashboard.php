<?php
/**
 * Test Modern Dashboard - STAR System
 * Direct test of the modern dashboard design
 */

session_start();

// Mock user data for testing
$user = [
    'id' => 1,
    'first_name' => 'John',
    'last_name' => 'Doe',
    'email' => 'admin@star-church.org',
    'role' => 'administrator'
];

// Mock statistics
$aspirantStats = [
    'total' => 42,
    'by_status' => [
        'active' => 28,
        'completed' => 14
    ]
];

$stepStats = [
    [
        'step_number' => 1,
        'name' => 'Application',
        'pending_count' => 5,
        'in_progress_count' => 8,
        'completed_count' => 15,
        'rejected_count' => 2
    ],
    [
        'step_number' => 2,
        'name' => 'PCNC Training',
        'pending_count' => 3,
        'in_progress_count' => 6,
        'completed_count' => 12,
        'rejected_count' => 1
    ],
    [
        'step_number' => 3,
        'name' => 'MDS Interview',
        'pending_count' => 2,
        'in_progress_count' => 4,
        'completed_count' => 8,
        'rejected_count' => 0
    ]
];

$overdueTasks = [
    [
        'id' => 1,
        'first_name' => 'Jane',
        'last_name' => 'Smith',
        'step_name' => 'Application Review',
        'days_overdue' => 5,
        'deadline' => '2024-01-15'
    ],
    [
        'id' => 2,
        'first_name' => 'Bob',
        'last_name' => 'Johnson',
        'step_name' => 'PCNC Training',
        'days_overdue' => 3,
        'deadline' => '2024-01-18'
    ]
];

$recentAspirants = [
    [
        'id' => 1,
        'first_name' => 'Alice',
        'last_name' => 'Brown',
        'email' => 'alice@example.com',
        'current_step' => 2,
        'current_step_name' => 'PCNC Training',
        'application_date' => '2024-01-10'
    ],
    [
        'id' => 2,
        'first_name' => 'Charlie',
        'last_name' => 'Wilson',
        'email' => 'charlie@example.com',
        'current_step' => 1,
        'current_step_name' => 'Application',
        'application_date' => '2024-01-12'
    ]
];

$appConfig = ['name' => 'STAR Volunteer Management System'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Modern Dashboard - <?php echo $appConfig['name']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/modern-design-system.css">
    <link rel="stylesheet" href="public/css/ai-sidebar.css">
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <span style="font-size: 1.5rem;">🌟</span>
                    <span>STAR System</span>
                </div>
                <div class="sidebar-user">
                    <div class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                    <div class="user-role"><?php echo ucfirst($user['role']); ?></div>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Overview</div>
                    <a href="dashboard.php" class="nav-item active">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="admin/users.php" class="nav-item">
                        <span class="nav-icon">👥</span>
                        Users
                    </a>
                    <a href="admin/user-wizard.php" class="nav-item">
                        <span class="nav-icon">➕</span>
                        Add User
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">STAR Process</div>
                    <a href="aspirants.php" class="nav-item">
                        <span class="nav-icon">🌟</span>
                        Aspirants
                    </a>
                    <a href="ministries.php" class="nav-item">
                        <span class="nav-icon">⛪</span>
                        Ministries
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Account</div>
                    <a href="logout.php" class="nav-item">
                        <span class="nav-icon">🚪</span>
                        Sign Out
                    </a>
                </div>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content with-ai-sidebar">
            <!-- Header -->
            <header class="content-header">
                <div>
                    <h1 class="mb-0">👑 Administrator Dashboard</h1>
                    <p class="text-muted mb-0">Manage the STAR volunteer program</p>
                </div>
                <div class="flex gap-4">
                    <a href="admin/user-wizard.php" class="btn btn-primary">
                        <span>➕</span>
                        Add User
                    </a>
                </div>
            </header>
            
            <!-- Dashboard Content -->
            <div class="content-body">
                <!-- Statistics Overview -->
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Total Aspirants</div>
                            <div class="stat-icon" style="background: var(--primary-100); color: var(--primary-600);">
                                🌟
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $aspirantStats['total']; ?></div>
                        <div class="stat-change positive">
                            <span>↗️</span>
                            <span>All aspirants</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Active Aspirants</div>
                            <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                ✅
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $aspirantStats['by_status']['active']; ?></div>
                        <div class="stat-change positive">
                            <span>Currently active</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Completed</div>
                            <div class="stat-icon" style="background: #d1fae5; color: #065f46;">
                                🎯
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $aspirantStats['by_status']['completed']; ?></div>
                        <div class="stat-change positive">
                            <span>Successfully completed</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Overdue Tasks</div>
                            <div class="stat-icon" style="background: #fee2e2; color: #991b1b;">
                                ⚠️
                            </div>
                        </div>
                        <div class="stat-value"><?php echo count($overdueTasks); ?></div>
                        <div class="stat-change negative">
                            <span>Need attention</span>
                        </div>
                    </div>
                </div>
                
                <!-- Recent Aspirants -->
                <div class="data-table" style="margin-top: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            👥 Recent Aspirants
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Latest aspirant applications and updates
                        </p>
                    </div>
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Current Step</th>
                                <th>Applied</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentAspirants as $aspirant): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: var(--gray-900);">
                                            <?php echo htmlspecialchars($aspirant['first_name'] . ' ' . $aspirant['last_name']); ?>
                                        </div>
                                    </td>
                                    <td style="color: var(--gray-600);">
                                        <?php echo htmlspecialchars($aspirant['email']); ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">Step <?php echo $aspirant['current_step']; ?></span>
                                    </td>
                                    <td style="color: var(--gray-600);">
                                        <?php echo date('M j, Y', strtotime($aspirant['application_date'])); ?>
                                    </td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-outline">View</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Quick Actions -->
                <div class="dashboard-grid" style="margin-top: var(--space-6);">
                    <div class="stat-card" style="cursor: pointer;">
                        <div class="stat-header">
                            <div class="stat-title">Add New User</div>
                            <div class="stat-icon" style="background: var(--primary-100); color: var(--primary-600);">
                                ➕
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Create new system users with role-specific profiles
                        </div>
                        <div class="btn btn-sm btn-primary" style="width: 100%;">Create User</div>
                    </div>
                    
                    <div class="stat-card" style="cursor: pointer;">
                        <div class="stat-header">
                            <div class="stat-title">Manage Users</div>
                            <div class="stat-icon" style="background: var(--role-administrator)20; color: var(--role-administrator);">
                                👥
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Edit, update, and manage all system users
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">User Management</div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- AI Agent Sidebar -->
    <div class="ai-agent-sidebar" id="aiSidebar">
        <div class="ai-sidebar-header">
            <div class="ai-header-content">
                <div class="ai-avatar" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    🤖
                </div>
                <div class="ai-header-text">
                    <div class="ai-title">AI Assistant</div>
                    <div class="ai-subtitle">Personalized insights for Administrator</div>
                </div>
            </div>
            <button class="ai-toggle-btn" onclick="toggleAISidebar()">
                <span class="toggle-icon">⟨</span>
            </button>
        </div>
        
        <div class="ai-sidebar-content">
            <div class="ai-insights-section">
                <h3 class="ai-section-title">AI RECOMMENDATIONS</h3>
                
                <div class="ai-insight-card priority-high">
                    <div class="ai-insight-header">
                        <div class="ai-insight-icon">📈</div>
                        <div class="ai-insight-priority">HIGH</div>
                    </div>
                    <div class="ai-insight-title">User Growth Analytics</div>
                    <div class="ai-insight-description">5 new aspirants joined this week. Consider assigning mentors.</div>
                    <button class="ai-action-btn">Manage assignments</button>
                </div>
                
                <div class="ai-insight-card priority-medium">
                    <div class="ai-insight-header">
                        <div class="ai-insight-icon">🔒</div>
                        <div class="ai-insight-priority">MEDIUM</div>
                    </div>
                    <div class="ai-insight-title">Security Recommendation</div>
                    <div class="ai-insight-description">Enable two-factor authentication for all admin accounts.</div>
                    <button class="ai-action-btn">Configure 2FA</button>
                </div>
            </div>
            
            <div class="ai-actions-section">
                <button class="ai-refresh-btn">🔄 Refresh insights</button>
                <button class="ai-chat-btn" onclick="openAIChat()">💬 Ask AI</button>
            </div>
        </div>
    </div>
    
    <script>
        function toggleAISidebar() {
            const sidebar = document.getElementById('aiSidebar');
            sidebar.classList.toggle('collapsed');
        }
        
        function openAIChat() {
            alert('AI Chat functionality - would open chat modal');
        }
    </script>
</body>
</html>
