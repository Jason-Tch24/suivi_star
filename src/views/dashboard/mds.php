<?php
/**
 * MDS Dashboard - STAR Volunteer Management System
 */

require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../models/Aspirant.php';
require_once __DIR__ . '/../../models/JourneyStep.php';
require_once __DIR__ . '/../../components/AISidebar.php';
require_once __DIR__ . '/../../helpers/AssetHelper.php';

Auth::requireRole('mds');

$user = Auth::user();
$aspirantModel = new Aspirant();
$journeyStepModel = new JourneyStep();

// Get aspirants at interview step (Step 3)
$interviewAspirants = $journeyStepModel->getAspirantsAtStep(3);

// Get aspirants ready for interview (completed Step 2)
$readyForInterview = $journeyStepModel->getAspirantsAtStep(3, 'pending');

// Initialize AI Sidebar with MDS-specific insights
$aiSidebar = new AISidebar($user, 'mds');

$appConfig = require __DIR__ . '/../../../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MDS Dashboard - <?php echo $appConfig['name']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo AssetHelper::asset('css/modern-design-system.css'); ?>">
    <link rel="stylesheet" href="<?php echo AssetHelper::asset('css/ai-sidebar.css'); ?>">
    <link rel="stylesheet" href="<?php echo AssetHelper::asset('css/dashboard-override.css'); ?>">
    <link rel="stylesheet" href="<?php echo AssetHelper::asset('css/layout-fixes.css'); ?>">
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <span style="font-size: 1.5rem;">👥</span>
                    <span>STAR System</span>
                </div>
                <div class="sidebar-user">
                    <div class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                    <div class="user-role">MDS</div>
                </div>
            </div>

            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Overview</div>
                    <a href="<?php echo AssetHelper::url('/dashboard'); ?>" class="nav-item active">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">MDS Process</div>
                    <a href="mds/interviews.php" class="nav-item">
                        <span class="nav-icon">🎤</span>
                        Interviews
                    </a>
                    <a href="mds/training-review.php" class="nav-item">
                        <span class="nav-icon">📝</span>
                        Training Review
                    </a>
                    <a href="<?php echo AssetHelper::url('/aspirants'); ?>" class="nav-item">
                        <span class="nav-icon">🌟</span>
                        All Aspirants
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Reports</div>
                    <a href="mds/analytics.php" class="nav-item">
                        <span class="nav-icon">📈</span>
                        Analytics
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Account</div>
                    <a href="<?php echo AssetHelper::url('/logout'); ?>" class="nav-item">
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
                    <h1 class="mb-0">👥 MDS Dashboard</h1>
                    <p class="text-muted mb-0">Ministry of STAR - Interview & Validation</p>
                </div>
                <div class="flex gap-4">
                    <a href="mds/interviews.php" class="btn btn-primary">
                        <span>🎤</span>
                        Schedule Interview
                    </a>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="content-body">
                <!-- Statistics Overview -->
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">At Interview Step</div>
                            <div class="stat-icon" style="background: var(--role-mds)20; color: var(--role-mds);">
                                🎤
                            </div>
                        </div>
                        <div class="stat-value"><?php echo count($interviewAspirants); ?></div>
                        <div class="stat-change positive">
                            <span>Total aspirants</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Ready for Interview</div>
                            <div class="stat-icon" style="background: var(--status-pending)20; color: var(--status-pending);">
                                📋
                            </div>
                        </div>
                        <div class="stat-value"><?php echo count($readyForInterview); ?></div>
                        <div class="stat-change positive">
                            <span>Awaiting scheduling</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Interviews Scheduled</div>
                            <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                📅
                            </div>
                        </div>
                        <div class="stat-value">
                            <?php
                            $inProgress = array_filter($interviewAspirants, function($a) { return $a['step_status'] == 'in_progress'; });
                            echo count($inProgress);
                            ?>
                        </div>
                        <div class="stat-change positive">
                            <span>In progress</span>
                        </div>
                    </div>

                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Interviews Completed</div>
                            <div class="stat-icon" style="background: #d1fae5; color: #065f46;">
                                ✅
                            </div>
                        </div>
                        <div class="stat-value">
                            <?php
                            $completed = array_filter($interviewAspirants, function($a) { return $a['step_status'] == 'completed'; });
                            echo count($completed);
                            ?>
                        </div>
                        <div class="stat-change positive">
                            <span>Successfully completed</span>
                        </div>
                    </div>
                </div>

                <!-- Pending Interviews -->
                <div class="data-table" style="margin-top: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            🎤 Aspirants Ready for Interview
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Aspirants who have completed PCNC training and are ready for MDS interviews
                        </p>
                    </div>

                    <?php if (empty($readyForInterview)): ?>
                        <div style="padding: var(--space-8); text-align: center;">
                            <div style="font-size: 3rem; margin-bottom: var(--space-4);">🎉</div>
                            <h3 style="margin: 0 0 var(--space-2) 0; color: var(--gray-900);">No Pending Interviews</h3>
                            <p style="margin: 0; color: var(--gray-600);">All aspirants at the interview step have been processed. Great work!</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>PCNC Completed</th>
                                    <th>Ministry Preference</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($readyForInterview as $aspirant): ?>
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
                                            <span class="badge badge-success">
                                                <?php echo $aspirant['started_at'] ? date('M j, Y', strtotime($aspirant['started_at'])) : 'N/A'; ?>
                                            </span>
                                        </td>
                                        <td style="color: var(--gray-600);">
                                            <?php echo htmlspecialchars($aspirant['assigned_ministry_name'] ?: 'Not assigned'); ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: var(--space-2);">
                                                <a href="mds/interview.php?id=<?php echo $aspirant['id']; ?>" class="btn btn-sm btn-primary">Schedule</a>
                                                <a href="aspirants.php?id=<?php echo $aspirant['id']; ?>" class="btn btn-sm btn-outline">View</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>

                <!-- All Interview Candidates -->
                <div class="data-table" style="margin-top: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            👥 All Interview Candidates
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Complete overview of all aspirants at the interview stage
                        </p>
                    </div>

                    <div style="padding: var(--space-6);">
                        <div class="dashboard-grid">
                            <?php foreach ($interviewAspirants as $aspirant): ?>
                                <div class="stat-card" style="border-left: 4px solid <?php
                                    echo $aspirant['step_status'] == 'pending' ? 'var(--status-pending)' :
                                        ($aspirant['step_status'] == 'in_progress' ? 'var(--status-active)' :
                                        ($aspirant['step_status'] == 'completed' ? 'var(--status-success)' : 'var(--status-error)'));
                                ?>;">
                                    <div class="stat-header">
                                        <div class="stat-title"><?php echo htmlspecialchars($aspirant['first_name'] . ' ' . $aspirant['last_name']); ?></div>
                                        <div class="stat-icon" style="background: var(--role-mds)20; color: var(--role-mds);">
                                            <?php
                                            echo $aspirant['step_status'] == 'pending' ? '📋' :
                                                ($aspirant['step_status'] == 'in_progress' ? '🎤' :
                                                ($aspirant['step_status'] == 'completed' ? '✅' : '❌'));
                                            ?>
                                        </div>
                                    </div>

                                    <div style="margin-bottom: var(--space-4);">
                                        <span class="badge <?php
                                            echo $aspirant['step_status'] == 'pending' ? 'badge-warning' :
                                                ($aspirant['step_status'] == 'in_progress' ? 'badge-primary' :
                                                ($aspirant['step_status'] == 'completed' ? 'badge-success' : 'badge-error'));
                                        ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $aspirant['step_status'])); ?>
                                        </span>
                                    </div>

                                    <div style="font-size: var(--text-sm); color: var(--gray-600); margin-bottom: var(--space-4);">
                                        <div style="margin-bottom: var(--space-1);">
                                            <strong>Email:</strong> <?php echo htmlspecialchars($aspirant['email']); ?>
                                        </div>
                                        <div style="margin-bottom: var(--space-1);">
                                            <strong>Applied:</strong> <?php echo date('M j, Y', strtotime($aspirant['application_date'])); ?>
                                        </div>
                                        <?php if ($aspirant['started_at']): ?>
                                            <div style="margin-bottom: var(--space-1);">
                                                <strong>Interview Started:</strong> <?php echo date('M j, Y', strtotime($aspirant['started_at'])); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <div style="display: flex; gap: var(--space-2);">
                                        <?php if ($aspirant['step_status'] == 'pending'): ?>
                                            <a href="mds/interview.php?id=<?php echo $aspirant['id']; ?>" class="btn btn-sm btn-primary" style="flex: 1;">Schedule</a>
                                        <?php elseif ($aspirant['step_status'] == 'in_progress'): ?>
                                            <a href="mds/interview.php?id=<?php echo $aspirant['id']; ?>" class="btn btn-sm btn-success" style="flex: 1;">Complete</a>
                                        <?php endif; ?>
                                        <a href="aspirants.php?id=<?php echo $aspirant['id']; ?>" class="btn btn-sm btn-outline" style="flex: 1;">View</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-grid" style="margin-top: var(--space-6);">
                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='mds/interviews.php'">
                        <div class="stat-header">
                            <div class="stat-title">Manage Interviews</div>
                            <div class="stat-icon" style="background: var(--role-mds)20; color: var(--role-mds);">
                                🎤
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Schedule, conduct, and complete aspirant interviews
                        </div>
                        <div class="btn btn-sm btn-primary" style="width: 100%;">Manage Interviews</div>
                    </div>

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='mds/reports.php'">
                        <div class="stat-header">
                            <div class="stat-title">Interview Reports</div>
                            <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                📊
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            View and manage interview reports and recommendations
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">View Reports</div>
                    </div>

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='aspirants.php'">
                        <div class="stat-header">
                            <div class="stat-title">All Aspirants</div>
                            <div class="stat-icon" style="background: var(--role-aspirant)20; color: var(--role-aspirant);">
                                🌟
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            View complete list of aspirants and their progress
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">View Aspirants</div>
                    </div>

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='mds/guidelines.php'">
                        <div class="stat-header">
                            <div class="stat-title">Interview Guidelines</div>
                            <div class="stat-icon" style="background: var(--role-mds)20; color: var(--role-mds);">
                                📋
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Access interview procedures and evaluation criteria
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">View Guidelines</div>
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
