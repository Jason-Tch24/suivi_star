<?php
/**
 * Aspirant Dashboard - STAR Volunteer Management System
 */

require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../models/Aspirant.php';
require_once __DIR__ . '/../../models/JourneyStep.php';
require_once __DIR__ . '/../../components/AISidebar.php';
require_once __DIR__ . '/../../helpers/AssetHelper.php';

Auth::requireRole('aspirant');

$user = Auth::user();
$aspirantModel = new Aspirant();
$journeyStepModel = new JourneyStep();

// Get aspirant data
$aspirant = $aspirantModel->findByUserId($user['id']);
if (!$aspirant) {
    // Redirect to application form if not found
    header('Location: /register');
    exit;
}

// Get journey progress
$journeyProgress = $journeyStepModel->getProgressForAspirant($aspirant['id']);
$currentStep = $journeyStepModel->getCurrentStepForAspirant($aspirant['id']);

// Initialize AI Sidebar with Aspirant-specific insights
$aiSidebar = new AISidebar($user, 'aspirant');

$appConfig = require __DIR__ . '/../../../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My STAR Journey - <?php echo $appConfig['name']; ?></title>
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
                    <span style="font-size: 1.5rem;">🌟</span>
                    <span>STAR System</span>
                </div>
                <div class="sidebar-user">
                    <div class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></div>
                    <div class="user-role">Aspirant</div>
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
                    <div class="nav-section-title">My Journey</div>
                    <a href="aspirant/progress.php" class="nav-item">
                        <span class="nav-icon">🚀</span>
                        Progress
                    </a>
                    <a href="aspirant/schedule.php" class="nav-item">
                        <span class="nav-icon">📅</span>
                        Schedule
                    </a>
                    <a href="aspirant/ministry-matches.php" class="nav-item">
                        <span class="nav-icon">⛪</span>
                        Ministry Matches
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Resources</div>
                    <a href="aspirant/resources.php" class="nav-item">
                        <span class="nav-icon">📚</span>
                        Resources
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
                    <h1 class="mb-0">🌟 Welcome, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
                    <p class="text-muted mb-0">Track your STAR volunteer journey</p>
                </div>
                <div class="flex gap-4">
                    <a href="aspirant/advance.php" class="btn btn-primary">
                        <span>🚀</span>
                        Next Step
                    </a>
                </div>
            </header>

            <!-- Dashboard Content -->
            <div class="content-body">
                <!-- Current Status Card -->
                <div class="stat-card" style="margin-bottom: var(--space-6); border-left: 4px solid var(--role-aspirant);">
                    <div style="display: flex; align-items: center; gap: var(--space-4); margin-bottom: var(--space-4);">
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: var(--role-aspirant); color: white; display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700;">
                            <?php echo $aspirant['current_step']; ?>
                        </div>
                        <div style="flex: 1;">
                            <h2 style="margin: 0 0 var(--space-2) 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                                Current Status
                            </h2>
                            <h3 style="margin: 0 0 var(--space-1) 0; font-size: var(--text-lg); font-weight: 600; color: var(--role-aspirant);">
                                <?php echo htmlspecialchars($currentStep['name'] ?? 'Unknown Step'); ?>
                            </h3>
                            <p style="margin: 0; color: var(--gray-600); font-size: var(--text-sm);">
                                <?php echo htmlspecialchars($currentStep['description'] ?? ''); ?>
                            </p>
                        </div>
                        <?php if ($currentStep['progress_status']): ?>
                            <div>
                                <span class="badge <?php
                                    echo $currentStep['progress_status'] === 'pending' ? 'badge-warning' :
                                        ($currentStep['progress_status'] === 'in_progress' ? 'badge-primary' :
                                        ($currentStep['progress_status'] === 'completed' ? 'badge-success' : 'badge-error'));
                                ?>">
                                    <?php echo ucfirst(str_replace('_', ' ', $currentStep['progress_status'])); ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="padding: var(--space-4); background: var(--gray-50); border-radius: var(--radius-md);">
                        <?php if ($currentStep['progress_status'] === 'pending'): ?>
                            <p style="margin: 0; color: var(--status-warning); font-weight: 500;">
                                ⏳ Waiting for validation from administrators.
                            </p>
                        <?php elseif ($currentStep['progress_status'] === 'in_progress'): ?>
                            <p style="margin: 0; color: var(--status-active); font-weight: 500;">
                                🚀 Keep up the great work! You're making progress.
                            </p>
                        <?php elseif ($currentStep['progress_status'] === 'completed'): ?>
                            <p style="margin: 0; color: var(--status-success); font-weight: 500;">
                                ✅ Step completed! Moving to next phase.
                            </p>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Journey Timeline -->
                <div class="data-table" style="margin-top: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            🚀 Your STAR Journey
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Track your progress through each step of the STAR program
                        </p>
                    </div>

                    <div style="padding: var(--space-6);">
                        <div style="position: relative;">
                            <?php foreach ($journeyProgress as $index => $step): ?>
                                <div style="display: flex; margin-bottom: var(--space-6); position: relative;">
                                    <!-- Timeline connector -->
                                    <?php if ($index < count($journeyProgress) - 1): ?>
                                        <div style="position: absolute; left: 24px; top: 60px; width: 2px; height: calc(100% + var(--space-6)); background: <?php echo ($step['progress_status'] === 'completed') ? 'var(--status-success)' : 'var(--gray-300)'; ?>;"></div>
                                    <?php endif; ?>

                                    <!-- Step marker -->
                                    <div style="width: 48px; height: 48px; border-radius: 50%; background: <?php
                                        echo ($step['progress_status'] === 'completed') ? 'var(--status-success)' :
                                            (($step['progress_status'] === 'in_progress') ? 'var(--status-active)' :
                                            (($step['progress_status'] === 'pending') ? 'var(--status-warning)' : 'var(--gray-300)'));
                                    ?>; color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; margin-right: var(--space-4); flex-shrink: 0; z-index: 1; position: relative;">
                                        <?php echo $step['step_number']; ?>
                                    </div>

                                    <!-- Step content -->
                                    <div style="flex: 1; background: white; border-radius: var(--radius-lg); padding: var(--space-4); border: 1px solid var(--gray-200);">
                                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: var(--space-3);">
                                            <div>
                                                <h3 style="margin: 0 0 var(--space-1) 0; font-size: var(--text-lg); font-weight: 600; color: var(--gray-900);">
                                                    <?php echo htmlspecialchars($step['name']); ?>
                                                </h3>
                                                <p style="margin: 0; color: var(--gray-600); font-size: var(--text-sm);">
                                                    <?php echo htmlspecialchars($step['description']); ?>
                                                </p>
                                            </div>
                                            <span class="badge <?php
                                                echo ($step['progress_status'] === 'completed') ? 'badge-success' :
                                                    (($step['progress_status'] === 'in_progress') ? 'badge-primary' :
                                                    (($step['progress_status'] === 'pending') ? 'badge-warning' : 'badge-secondary'));
                                            ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $step['progress_status'] ?? 'not started')); ?>
                                            </span>
                                        </div>

                                        <div style="display: flex; flex-wrap: wrap; gap: var(--space-4); font-size: var(--text-sm); color: var(--gray-600);">
                                            <?php if ($step['started_at']): ?>
                                                <div><strong>Started:</strong> <?php echo date('M j, Y', strtotime($step['started_at'])); ?></div>
                                            <?php endif; ?>

                                            <?php if ($step['completed_at']): ?>
                                                <div><strong>Completed:</strong> <?php echo date('M j, Y', strtotime($step['completed_at'])); ?></div>
                                            <?php endif; ?>

                                            <?php if ($step['deadline']): ?>
                                                <div><strong>Deadline:</strong> <?php echo date('M j, Y', strtotime($step['deadline'])); ?></div>
                                            <?php endif; ?>

                                            <?php if ($step['validator_first_name']): ?>
                                                <div><strong>Validated by:</strong> <?php echo htmlspecialchars($step['validator_first_name'] . ' ' . $step['validator_last_name']); ?></div>
                                            <?php endif; ?>
                                        </div>

                                        <?php if ($step['validation_notes']): ?>
                                            <div style="margin-top: var(--space-3); padding: var(--space-3); background: var(--gray-50); border-radius: var(--radius-md);">
                                                <strong style="color: var(--gray-900);">Notes:</strong>
                                                <span style="color: var(--gray-700);"><?php echo htmlspecialchars($step['validation_notes']); ?></span>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Ministry Preferences -->
                <div class="data-table" style="margin-top: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            ⛪ Ministry Preferences
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Your ministry preferences and current assignment
                        </p>
                    </div>

                    <div style="padding: var(--space-6);">
                        <div class="dashboard-grid">
                            <?php if ($aspirant['ministry_preference_1_name']): ?>
                                <div class="stat-card" style="border-left: 4px solid var(--role-aspirant);">
                                    <div class="stat-header">
                                        <div class="stat-title">1st Choice</div>
                                        <div class="stat-icon" style="background: var(--role-aspirant)20; color: var(--role-aspirant);">
                                            🥇
                                        </div>
                                    </div>
                                    <div style="font-size: var(--text-lg); font-weight: 600; color: var(--gray-900);">
                                        <?php echo htmlspecialchars($aspirant['ministry_preference_1_name']); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($aspirant['ministry_preference_2_name']): ?>
                                <div class="stat-card" style="border-left: 4px solid var(--status-active);">
                                    <div class="stat-header">
                                        <div class="stat-title">2nd Choice</div>
                                        <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                            🥈
                                        </div>
                                    </div>
                                    <div style="font-size: var(--text-lg); font-weight: 600; color: var(--gray-900);">
                                        <?php echo htmlspecialchars($aspirant['ministry_preference_2_name']); ?>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <?php if ($aspirant['ministry_preference_3_name']): ?>
                                <div class="stat-card" style="border-left: 4px solid var(--status-warning);">
                                    <div class="stat-header">
                                        <div class="stat-title">3rd Choice</div>
                                        <div class="stat-icon" style="background: var(--status-warning)20; color: var(--status-warning);">
                                            🥉
                                        </div>
                                    </div>
                                    <div style="font-size: var(--text-lg); font-weight: 600; color: var(--gray-900);">
                                        <?php echo htmlspecialchars($aspirant['ministry_preference_3_name']); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <?php if ($aspirant['assigned_ministry_name']): ?>
                            <div style="margin-top: var(--space-6); padding: var(--space-6); background: var(--status-success)10; border-radius: var(--radius-lg); border: 1px solid var(--status-success);">
                                <h3 style="margin: 0 0 var(--space-4) 0; font-size: var(--text-lg); font-weight: 600; color: var(--status-success);">
                                    🎉 Assigned Ministry
                                </h3>
                                <div style="display: flex; align-items: center; gap: var(--space-4);">
                                    <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--status-success); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                        ⛪
                                    </div>
                                    <div style="flex: 1;">
                                        <h4 style="margin: 0 0 var(--space-1) 0; font-size: var(--text-lg); font-weight: 600; color: var(--gray-900);">
                                            <?php echo htmlspecialchars($aspirant['assigned_ministry_name']); ?>
                                        </h4>
                                        <?php if ($aspirant['mentor_first_name']): ?>
                                            <p style="margin: 0; color: var(--gray-600); font-size: var(--text-sm);">
                                                <strong>Mentor:</strong> <?php echo htmlspecialchars($aspirant['mentor_first_name'] . ' ' . $aspirant['mentor_last_name']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="dashboard-grid" style="margin-top: var(--space-6);">
                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='profile.php'">
                        <div class="stat-header">
                            <div class="stat-title">Update Profile</div>
                            <div class="stat-icon" style="background: var(--role-aspirant)20; color: var(--role-aspirant);">
                                👤
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Update your personal information and contact details
                        </div>
                        <div class="btn btn-sm btn-primary" style="width: 100%;">Update Profile</div>
                    </div>

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='aspirant/resources.php'">
                        <div class="stat-header">
                            <div class="stat-title">Training Materials</div>
                            <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                📚
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Access and download training materials and resources
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">View Materials</div>
                    </div>

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='notifications.php'">
                        <div class="stat-header">
                            <div class="stat-title">Notifications</div>
                            <div class="stat-icon" style="background: var(--status-warning)20; color: var(--status-warning);">
                                🔔
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            View important updates and messages
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">View Notifications</div>
                    </div>

                    <div class="stat-card" style="cursor: pointer;" onclick="location.href='contact.php'">
                        <div class="stat-header">
                            <div class="stat-title">Contact Support</div>
                            <div class="stat-icon" style="background: var(--role-aspirant)20; color: var(--role-aspirant);">
                                🆘
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Get help or ask questions about your journey
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
