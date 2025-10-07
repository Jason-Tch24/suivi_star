<?php
/**
 * Mentor Dashboard - STAR Volunteer Management System
 */

require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../models/Aspirant.php';
require_once __DIR__ . '/../../models/Ministry.php';
require_once __DIR__ . '/../../components/AISidebar.php';

Auth::requireRole('mentor');

$user = Auth::user();
$aspirantModel = new Aspirant();
$ministryModel = new Ministry();

// Get mentor's assigned aspirants
$assignedAspirants = $aspirantModel->getByMentor($user['id']);

// Initialize AI Sidebar with Mentor-specific insights
$aiSidebar = new AISidebar($user, 'mentor');

$appConfig = require __DIR__ . '/../../../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Dashboard - <?php echo $appConfig['name']; ?></title>
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
                    <span style="font-size: 1.5rem;">🤝</span>
                    <span>STAR System</span>
                </div>
                <div class="sidebar-user">
                    <div class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                    <div class="user-role">Mentor</div>
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
                    <div class="nav-section-title">Mentoring</div>
                    <a href="mentor/aspirants.php" class="nav-item">
                        <span class="nav-icon">🌟</span>
                        My Aspirants
                    </a>
                    <a href="mentor/schedule.php" class="nav-item">
                        <span class="nav-icon">📅</span>
                        Schedule
                    </a>
                    <a href="mentor/reports.php" class="nav-item">
                        <span class="nav-icon">📝</span>
                        Reports
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Resources</div>
                    <a href="mentor/resources.php" class="nav-item">
                        <span class="nav-icon">📚</span>
                        Resources
                    </a>
                    <a href="mentor/progress.php" class="nav-item">
                        <span class="nav-icon">📈</span>
                        Progress
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
                    <h1 class="mb-0">🤝 Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
                    <p class="text-muted mb-0">Guide aspirants through their STAR journey</p>
                </div>
                <div class="flex gap-4">
                    <a href="mentor/schedule.php" class="btn btn-primary">
                        <span>📅</span>
                        Schedule Check-in
                    </a>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="content-body">
                <!-- Statistics Overview -->
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Assigned Aspirants</div>
                            <div class="stat-icon" style="background: var(--role-mentor)20; color: var(--role-mentor);">
                                🌟
                            </div>
                        </div>
                        <div class="stat-value"><?php echo count($assignedAspirants); ?></div>
                        <div class="stat-change positive">
                            <span>Total mentees</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">In Ministry Training</div>
                            <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                📚
                            </div>
                        </div>
                        <div class="stat-value">
                            <?php
                            $inTraining = array_filter($assignedAspirants, function($a) { return $a['current_step'] == 4; });
                            echo count($inTraining);
                            ?>
                        </div>
                        <div class="stat-change positive">
                            <span>Step 4 progress</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Awaiting Reports</div>
                            <div class="stat-icon" style="background: var(--status-warning)20; color: var(--status-warning);">
                                📝
                            </div>
                        </div>
                        <div class="stat-value">
                            <?php
                            $needingReports = array_filter($assignedAspirants, function($a) { return $a['current_step'] == 5; });
                            echo count($needingReports);
                            ?>
                        </div>
                        <div class="stat-change <?php echo count($needingReports) > 0 ? 'negative' : 'positive'; ?>">
                            <span><?php echo count($needingReports) > 0 ? 'Need attention' : 'All up to date'; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Assigned Aspirants -->
                <div class="data-table" style="margin-top: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            🌟 Your Assigned Aspirants
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Guide and support your mentees through their STAR journey
                        </p>
                    </div>

                    <?php if (empty($assignedAspirants)): ?>
                        <div style="padding: var(--space-8); text-align: center;">
                            <div style="font-size: 3rem; margin-bottom: var(--space-4);">🤝</div>
                            <h3 style="margin: 0 0 var(--space-2) 0; color: var(--gray-900);">No Aspirants Assigned</h3>
                            <p style="margin: 0; color: var(--gray-600);">You don't have any aspirants assigned to you at the moment. Check back later or contact your administrator.</p>
                        </div>
                    <?php else: ?>
                        <div style="padding: var(--space-6);">
                            <div class="dashboard-grid">
                                <?php foreach ($assignedAspirants as $aspirant): ?>
                                    <div class="stat-card" style="border-left: 4px solid var(--role-mentor);">
                                        <div class="stat-header">
                                            <div class="stat-title"><?php echo htmlspecialchars($aspirant['first_name'] . ' ' . $aspirant['last_name']); ?></div>
                                            <div class="stat-icon" style="background: var(--role-mentor)20; color: var(--role-mentor);">
                                                🌟
                                            </div>
                                        </div>

                                        <div style="margin-bottom: var(--space-4);">
                                            <span class="badge badge-primary">
                                                Step <?php echo $aspirant['current_step']; ?>: <?php echo htmlspecialchars($aspirant['current_step_name']); ?>
                                            </span>
                                        </div>

                                        <div style="font-size: var(--text-sm); color: var(--gray-600); margin-bottom: var(--space-4);">
                                            <div style="margin-bottom: var(--space-1);">
                                                <strong>Email:</strong> <?php echo htmlspecialchars($aspirant['email']); ?>
                                            </div>
                                            <div style="margin-bottom: var(--space-1);">
                                                <strong>Ministry:</strong> <?php echo htmlspecialchars($aspirant['assigned_ministry_name'] ?: 'Not assigned'); ?>
                                            </div>
                                            <div style="margin-bottom: var(--space-1);">
                                                <strong>Applied:</strong> <?php echo date('M j, Y', strtotime($aspirant['application_date'])); ?>
                                            </div>
                                        </div>

                                        <div style="display: flex; gap: var(--space-2);">
                                            <a href="mentor/aspirants.php?id=<?php echo $aspirant['id']; ?>" class="btn btn-sm btn-primary" style="flex: 1;">View Details</a>
                                            <?php if ($aspirant['current_step'] == 5): ?>
                                                <a href="mentor/reports.php?id=<?php echo $aspirant['id']; ?>" class="btn btn-sm btn-success" style="flex: 1;">Submit Report</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-grid" style="margin-top: var(--space-6);">
                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='mentor/resources.php'">
                        <div class="stat-header">
                            <div class="stat-title">Training Materials</div>
                            <div class="stat-icon" style="background: var(--role-mentor)20; color: var(--role-mentor);">
                                📚
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Access resources and guides for mentoring aspirants
                        </div>
                        <div class="btn btn-sm btn-primary" style="width: 100%;">View Resources</div>
                    </div>

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='mentor/reports.php'">
                        <div class="stat-header">
                            <div class="stat-title">Submit Reports</div>
                            <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                📝
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Complete evaluation reports for your assigned aspirants
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">Submit Reports</div>
                    </div>

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='mentor/schedule.php'">
                        <div class="stat-header">
                            <div class="stat-title">Schedule Sessions</div>
                            <div class="stat-icon" style="background: var(--status-warning)20; color: var(--status-warning);">
                                📅
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Plan and schedule training sessions with aspirants
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">Schedule Sessions</div>
                    </div>

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='contact.php'">
                        <div class="stat-header">
                            <div class="stat-title">Get Support</div>
                            <div class="stat-icon" style="background: var(--role-mentor)20; color: var(--role-mentor);">
                                🆘
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Contact administrators for help or guidance
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">Get Support</div>
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
