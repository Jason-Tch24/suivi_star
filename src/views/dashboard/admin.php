<?php
/**
 * Administrator Dashboard - STAR Volunteer Management System
 */

require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../models/Aspirant.php';
require_once __DIR__ . '/../../models/Ministry.php';
require_once __DIR__ . '/../../models/JourneyStep.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../components/AIAgentSidebar.php';

Auth::requireRole('administrator');

$user = Auth::user();
$aspirantModel = new Aspirant();
$ministryModel = new Ministry();
$journeyStepModel = new JourneyStep();
$userModel = new User();

// Get dashboard statistics
$aspirantStats = $aspirantModel->getStatistics();
$stepStats = $journeyStepModel->getStepStatistics();
$ministryStats = $ministryModel->getStatistics();
$overdueTasks = $journeyStepModel->getOverdueSteps();

// Get recent aspirants
$recentAspirants = $aspirantModel->getAll(10, 0, 'active');

// Initialize AI Agent Sidebar
$aiSidebar = new AIAgentSidebar($user);

$appConfig = require __DIR__ . '/../../../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator Dashboard - <?php echo $appConfig['name']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/modern-design-system.css">
    <link rel="stylesheet" href="public/css/ai-sidebar.css">
    <link rel="stylesheet" href="public/css/dashboard-override.css">
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
                        <div class="stat-value"><?php echo $aspirantStats['total'] ?? 0; ?></div>
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
                        <div class="stat-value"><?php echo $aspirantStats['by_status']['active'] ?? 0; ?></div>
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
                        <div class="stat-value"><?php echo $aspirantStats['by_status']['completed'] ?? 0; ?></div>
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
                        <div class="stat-change <?php echo count($overdueTasks) > 0 ? 'negative' : 'neutral'; ?>">
                            <span>Need attention</span>
                        </div>
                    </div>
                </div>
        
                <!-- Journey Step Progress -->
                <div class="data-table">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            📋 Journey Step Progress
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Track aspirant progress through each STAR step
                        </p>
                    </div>

                    <div style="padding: var(--space-6);">
                        <div class="dashboard-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); margin-bottom: 0;">
                            <?php foreach ($stepStats as $step): ?>
                                <div class="stat-card" style="margin: 0;">
                                    <div class="stat-header">
                                        <div class="stat-title">Step <?php echo $step['step_number']; ?>: <?php echo htmlspecialchars($step['name']); ?></div>
                                        <div class="stat-icon" style="background: var(--primary-100); color: var(--primary-600);">
                                            📝
                                        </div>
                                    </div>
                                    <div style="display: flex; gap: var(--space-4); margin: var(--space-4) 0;">
                                        <div style="text-align: center;">
                                            <div style="font-size: var(--text-lg); font-weight: 700; color: var(--status-pending);">
                                                <?php echo $step['pending_count']; ?>
                                            </div>
                                            <div style="font-size: var(--text-xs); color: var(--gray-500);">Pending</div>
                                        </div>
                                        <div style="text-align: center;">
                                            <div style="font-size: var(--text-lg); font-weight: 700; color: var(--primary-600);">
                                                <?php echo $step['in_progress_count']; ?>
                                            </div>
                                            <div style="font-size: var(--text-xs); color: var(--gray-500);">In Progress</div>
                                        </div>
                                        <div style="text-align: center;">
                                            <div style="font-size: var(--text-lg); font-weight: 700; color: var(--status-active);">
                                                <?php echo $step['completed_count']; ?>
                                            </div>
                                            <div style="font-size: var(--text-xs); color: var(--gray-500);">Completed</div>
                                        </div>
                                        <?php if ($step['rejected_count'] > 0): ?>
                                        <div style="text-align: center;">
                                            <div style="font-size: var(--text-lg); font-weight: 700; color: var(--status-suspended);">
                                                <?php echo $step['rejected_count']; ?>
                                            </div>
                                            <div style="font-size: var(--text-xs); color: var(--gray-500);">Rejected</div>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <a href="/admin/steps/<?php echo $step['step_number']; ?>" class="btn btn-sm btn-outline" style="width: 100%;">
                                        Manage Step <?php echo $step['step_number']; ?>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
        
        <!-- Overdue Tasks Alert -->
        <?php if (!empty($overdueTasks)): ?>
            <div class="section-card alert-card">
                <h2>⚠️ Overdue Tasks</h2>
                <div class="overdue-list">
                    <?php foreach (array_slice($overdueTasks, 0, 5) as $task): ?>
                        <div class="overdue-item">
                            <div class="aspirant-info">
                                <strong><?php echo htmlspecialchars($task['first_name'] . ' ' . $task['last_name']); ?></strong>
                                <span class="step-name"><?php echo htmlspecialchars($task['step_name']); ?></span>
                            </div>
                            <div class="overdue-info">
                                <span class="days-overdue"><?php echo $task['days_overdue']; ?> days overdue</span>
                                <span class="deadline">Due: <?php echo date('M j, Y', strtotime($task['deadline'])); ?></span>
                            </div>
                            <div class="task-actions">
                                <a href="/admin/aspirants/<?php echo $task['id']; ?>" class="btn btn-sm">Review</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (count($overdueTasks) > 5): ?>
                    <div class="view-all">
                        <a href="/admin/overdue" class="btn btn-secondary">View All Overdue Tasks</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <!-- Recent Aspirants -->
        <div class="section-card">
            <h2>Recent Aspirants</h2>
            <div class="aspirants-table">
                <table>
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
                                <td><?php echo htmlspecialchars($aspirant['first_name'] . ' ' . $aspirant['last_name']); ?></td>
                                <td><?php echo htmlspecialchars($aspirant['email']); ?></td>
                                <td>
                                    <span class="step-badge">
                                        Step <?php echo $aspirant['current_step']; ?>: <?php echo htmlspecialchars($aspirant['current_step_name']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($aspirant['application_date'])); ?></td>
                                <td>
                                    <a href="/admin/aspirants/<?php echo $aspirant['id']; ?>" class="btn btn-sm">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
                    <div style="text-align: center; margin-top: var(--space-4);">
                        <a href="/admin/aspirants" class="btn btn-outline">View All Aspirants</a>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-grid" style="margin-top: var(--space-6);">
                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='admin/user-wizard.php'">
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

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='admin/users.php'">
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

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='ministries.php'">
                        <div class="stat-header">
                            <div class="stat-title">Manage Ministries</div>
                            <div class="stat-icon" style="background: var(--role-pastor)20; color: var(--role-pastor);">
                                ⛪
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Add or modify ministry departments
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">Manage Ministries</div>
                    </div>

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='aspirants.php'">
                        <div class="stat-header">
                            <div class="stat-title">View Aspirants</div>
                            <div class="stat-icon" style="background: var(--role-aspirant)20; color: var(--role-aspirant);">
                                🌟
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Monitor aspirant progress and status
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">View Aspirants</div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- AI Agent Sidebar -->
    <?php echo $aiSidebar->render(); ?>

    <!-- Scripts -->
    <script src="public/js/ai-sidebar.js"></script>
    <script>
        // Initialize main content with AI sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const mainContent = document.querySelector('.main-content');
            if (mainContent) {
                mainContent.classList.add('with-ai-sidebar');
            }
        });
    </script>

    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
