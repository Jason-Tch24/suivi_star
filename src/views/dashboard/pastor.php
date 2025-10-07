<?php
/**
 * Pastor Dashboard - STAR Volunteer Management System
 */

require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../models/Aspirant.php';
require_once __DIR__ . '/../../models/Ministry.php';
require_once __DIR__ . '/../../models/JourneyStep.php';
require_once __DIR__ . '/../../models/User.php';
require_once __DIR__ . '/../../components/AISidebar.php';

Auth::requireRole('pastor');

$user = Auth::user();
$aspirantModel = new Aspirant();
$ministryModel = new Ministry();
$journeyStepModel = new JourneyStep();
$userModel = new User();

// Get comprehensive statistics
$aspirantStats = $aspirantModel->getStatistics();
$stepStats = $journeyStepModel->getStepStatistics();
$ministryStats = $ministryModel->getStatistics();

// Calculate completion rates
$completionRates = [];
for ($i = 1; $i <= 6; $i++) {
    $completionRates[$i] = $journeyStepModel->getCompletionRate($i);
}

// Get recent activity
$recentAspirants = $aspirantModel->getAll(5, 0, 'active');
$overdueTasks = $journeyStepModel->getOverdueSteps();

// Initialize AI Sidebar with Pastor-specific insights
$aiSidebar = new AISidebar($user, 'pastor');

$appConfig = require __DIR__ . '/../../../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pastor Dashboard - <?php echo $appConfig['name']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/modern-design-system.css">
    <link rel="stylesheet" href="public/css/ai-sidebar.css">
    <link rel="stylesheet" href="public/css/dashboard-override.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <span style="font-size: 1.5rem;">⛪</span>
                    <span>STAR System</span>
                </div>
                <div class="sidebar-user">
                    <div class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                    <div class="user-role">Pastor</div>
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
                    <div class="nav-section-title">STAR Process</div>
                    <a href="aspirants.php" class="nav-item">
                        <span class="nav-icon">🌟</span>
                        Aspirants
                    </a>
                    <a href="ministries.php" class="nav-item">
                        <span class="nav-icon">⛪</span>
                        Ministries
                    </a>
                    <a href="pastor/assignments.php" class="nav-item">
                        <span class="nav-icon">📋</span>
                        Final Assignments
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Reports</div>
                    <a href="pastor/reports.php" class="nav-item">
                        <span class="nav-icon">📈</span>
                        Analytics
                    </a>
                    <a href="pastor/performance.php" class="nav-item">
                        <span class="nav-icon">🎯</span>
                        Performance
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
                    <h1 class="mb-0">⛪ Pastor Dashboard</h1>
                    <p class="text-muted mb-0">STAR Program Overview & Analytics</p>
                </div>
                <div class="flex gap-4">
                    <a href="pastor/reports.php" class="btn btn-primary">
                        <span>📈</span>
                        Generate Reports
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
                            <div class="stat-icon" style="background: var(--role-pastor)20; color: var(--role-pastor);">
                                🌟
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $aspirantStats['total'] ?? 0; ?></div>
                        <div class="stat-change positive">
                            <span>All time</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Active Journey</div>
                            <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                🚀
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $aspirantStats['by_status']['active'] ?? 0; ?></div>
                        <div class="stat-change positive">
                            <span>Currently progressing</span>
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
                            <span>Active volunteers</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Active Ministries</div>
                            <div class="stat-icon" style="background: var(--role-pastor)20; color: var(--role-pastor);">
                                ⛪
                            </div>
                        </div>
                        <div class="stat-value"><?php echo count($ministryStats); ?></div>
                        <div class="stat-change positive">
                            <span>Departments</span>
                        </div>
                    </div>
                </div>

                <!-- Journey Progress Analytics -->
                <div class="data-table" style="margin-top: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            📊 STAR Journey Analytics
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Step completion rates and current distribution
                        </p>
                    </div>

                    <div style="padding: var(--space-6);">
                        <div class="dashboard-grid" style="margin-bottom: var(--space-6);">
                            <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-4); border: 1px solid var(--gray-200);">
                                <h3 style="margin: 0 0 var(--space-4) 0; font-size: var(--text-lg); font-weight: 600; color: var(--gray-900); text-align: center;">Step Completion Rates</h3>
                                <canvas id="completionChart" width="400" height="200"></canvas>
                            </div>

                            <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-4); border: 1px solid var(--gray-200);">
                                <h3 style="margin: 0 0 var(--space-4) 0; font-size: var(--text-lg); font-weight: 600; color: var(--gray-900); text-align: center;">Current Step Distribution</h3>
                                <canvas id="distributionChart" width="400" height="200"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ministry Performance -->
                <div class="data-table" style="margin-top: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            ⛪ Ministry Performance
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Aspirant interest and assignment conversion rates
                        </p>
                    </div>

                    <div style="padding: var(--space-6);">
                        <div class="dashboard-grid">
                            <?php foreach (array_slice($ministryStats, 0, 6) as $ministry): ?>
                                <div class="stat-card">
                                    <div class="stat-header">
                                        <div class="stat-title"><?php echo htmlspecialchars($ministry['name']); ?></div>
                                        <div class="stat-icon" style="background: var(--role-pastor)20; color: var(--role-pastor);">
                                            ⛪
                                        </div>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; margin-bottom: var(--space-4);">
                                        <div style="text-align: center;">
                                            <div style="font-size: var(--text-lg); font-weight: 700; color: var(--gray-900);">
                                                <?php echo $ministry['interested_aspirants']; ?>
                                            </div>
                                            <div style="font-size: var(--text-xs); color: var(--gray-600);">Interested</div>
                                        </div>
                                        <div style="text-align: center;">
                                            <div style="font-size: var(--text-lg); font-weight: 700; color: var(--gray-900);">
                                                <?php echo $ministry['assigned_aspirants']; ?>
                                            </div>
                                            <div style="font-size: var(--text-xs); color: var(--gray-600);">Assigned</div>
                                        </div>
                                        <div style="text-align: center;">
                                            <div style="font-size: var(--text-lg); font-weight: 700; color: var(--role-pastor);">
                                                <?php
                                                $conversion = $ministry['interested_aspirants'] > 0
                                                    ? round(($ministry['assigned_aspirants'] / $ministry['interested_aspirants']) * 100)
                                                    : 0;
                                                echo $conversion . '%';
                                                ?>
                                            </div>
                                            <div style="font-size: var(--text-xs); color: var(--gray-600);">Conversion</div>
                                        </div>
                                    </div>
                                    <a href="ministries.php?id=<?php echo $ministry['id']; ?>" class="btn btn-sm btn-outline" style="width: 100%;">View Details</a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Alerts & Attention Items -->
                <?php if (!empty($overdueTasks)): ?>
                    <div class="data-table" style="margin-top: var(--space-6); border-left: 4px solid var(--status-warning);">
                        <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200); background: var(--status-warning)10;">
                            <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--status-warning);">
                                ⚠️ Items Requiring Attention
                            </h2>
                            <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                                Overdue tasks that need immediate attention
                            </p>
                        </div>

                        <div style="padding: var(--space-6);">
                            <?php foreach (array_slice($overdueTasks, 0, 5) as $task): ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-4); background: white; border-radius: var(--radius-md); margin-bottom: var(--space-3); border: 1px solid var(--gray-200);">
                                    <div>
                                        <div style="font-weight: 600; color: var(--gray-900); margin-bottom: var(--space-1);">
                                            <?php echo htmlspecialchars($task['first_name'] . ' ' . $task['last_name']); ?>
                                        </div>
                                        <div style="color: var(--gray-600); font-size: var(--text-sm);">
                                            <?php echo htmlspecialchars($task['step_name']); ?>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="badge" style="background: var(--status-warning); color: white;">
                                            <?php echo $task['days_overdue']; ?> days overdue
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div style="text-align: center; margin-top: var(--space-4);">
                                <a href="pastor/overdue.php" class="btn btn-outline">Review All Overdue Items</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <!-- Recent Activity -->
                <div class="data-table" style="margin-top: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            🕒 Recent Activity
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Latest aspirant progress and updates
                        </p>
                    </div>

                    <div style="padding: var(--space-6);">
                        <?php foreach ($recentAspirants as $aspirant): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: var(--space-4); background: var(--gray-50); border-radius: var(--radius-md); margin-bottom: var(--space-3);">
                                <div>
                                    <div style="font-weight: 600; color: var(--gray-900); margin-bottom: var(--space-1);">
                                        <?php echo htmlspecialchars($aspirant['first_name'] . ' ' . $aspirant['last_name']); ?>
                                    </div>
                                    <div style="color: var(--gray-600); font-size: var(--text-sm);">
                                        Currently at Step <?php echo $aspirant['current_step']; ?>:
                                        <?php echo htmlspecialchars($aspirant['current_step_name']); ?>
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm);">
                                    Applied <?php echo date('M j, Y', strtotime($aspirant['application_date'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>

                        <div style="text-align: center; margin-top: var(--space-4);">
                            <a href="aspirants.php" class="btn btn-outline">View All Aspirants</a>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-grid" style="margin-top: var(--space-6);">
                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='pastor/reports.php'">
                        <div class="stat-header">
                            <div class="stat-title">Generate Reports</div>
                            <div class="stat-icon" style="background: var(--role-pastor)20; color: var(--role-pastor);">
                                📈
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Create detailed reports on program performance
                        </div>
                        <div class="btn btn-sm btn-primary" style="width: 100%;">Generate Reports</div>
                    </div>

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='aspirants.php'">
                        <div class="stat-header">
                            <div class="stat-title">Review Aspirants</div>
                            <div class="stat-icon" style="background: var(--role-aspirant)20; color: var(--role-aspirant);">
                                🌟
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            View and manage all aspirants in the STAR program
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">Review Aspirants</div>
                    </div>

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='pastor/assignments.php'">
                        <div class="stat-header">
                            <div class="stat-title">Final Assignments</div>
                            <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                📋
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Approve final ministry assignments
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">Manage Assignments</div>
                    </div>

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='ministries.php'">
                        <div class="stat-header">
                            <div class="stat-title">Ministry Overview</div>
                            <div class="stat-icon" style="background: var(--role-pastor)20; color: var(--role-pastor);">
                                ⛪
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Review ministry performance and distribution
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">Ministry Overview</div>
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

        // Completion Rates Chart
        const completionCtx = document.getElementById('completionChart').getContext('2d');
        new Chart(completionCtx, {
            type: 'bar',
            data: {
                labels: ['Step 1', 'Step 2', 'Step 3', 'Step 4', 'Step 5', 'Step 6'],
                datasets: [{
                    label: 'Completion Rate (%)',
                    data: [
                        <?php echo implode(',', array_values($completionRates)); ?>
                    ],
                    backgroundColor: [
                        'var(--role-pastor)', 'var(--status-active)', 'var(--status-warning)',
                        'var(--status-error)', 'var(--role-mentor)', 'var(--role-mds)'
                    ],
                    borderColor: [
                        'var(--role-pastor)', 'var(--status-active)', 'var(--status-warning)',
                        'var(--status-error)', 'var(--role-mentor)', 'var(--role-mds)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });

        // Current Step Distribution Chart
        const distributionCtx = document.getElementById('distributionChart').getContext('2d');
        new Chart(distributionCtx, {
            type: 'doughnut',
            data: {
                labels: [
                    <?php
                    $labels = [];
                    foreach ($aspirantStats['by_step'] ?? [] as $step => $data) {
                        $labels[] = "'Step {$step}'";
                    }
                    echo implode(',', $labels);
                    ?>
                ],
                datasets: [{
                    data: [
                        <?php
                        $counts = [];
                        foreach ($aspirantStats['by_step'] ?? [] as $step => $data) {
                            $counts[] = $data['count'];
                        }
                        echo implode(',', $counts);
                        ?>
                    ],
                    backgroundColor: [
                        'var(--role-pastor)', 'var(--status-active)', 'var(--status-warning)',
                        'var(--status-error)', 'var(--role-mentor)', 'var(--role-mds)'
                    ]
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    </script>
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
