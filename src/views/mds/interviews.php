<?php
/**
 * MDS Interviews Management Page - STAR Volunteer Management System
 */

require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../models/Aspirant.php';
require_once __DIR__ . '/../../models/JourneyStep.php';

Auth::requireRole('mds');

$user = Auth::user();
$aspirantModel = new Aspirant();
$journeyStepModel = new JourneyStep();

// Get aspirants at interview step (Step 3)
$interviewAspirants = $journeyStepModel->getAspirantsAtStep(3);

// Get aspirants ready for interview (completed Step 2)
$readyForInterview = $journeyStepModel->getAspirantsAtStep(3, 'pending');

$appConfig = require __DIR__ . '/../../../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MDS Interviews - <?php echo $appConfig['name']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/modern-design-system.css">
    <link rel="stylesheet" href="public/css/dashboard-override.css">
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
                    <a href="../dashboard.php" class="nav-item">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">MDS Process</div>
                    <a href="interviews.php" class="nav-item active">
                        <span class="nav-icon">🎤</span>
                        Interviews
                    </a>
                    <a href="training-review.php" class="nav-item">
                        <span class="nav-icon">📝</span>
                        Training Review
                    </a>
                    <a href="../aspirants.php" class="nav-item">
                        <span class="nav-icon">🌟</span>
                        All Aspirants
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Reports</div>
                    <a href="analytics.php" class="nav-item">
                        <span class="nav-icon">📈</span>
                        Analytics
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Account</div>
                    <a href="../logout.php" class="nav-item">
                        <span class="nav-icon">🚪</span>
                        Sign Out
                    </a>
                </div>
            </nav>
        </aside>
        
        <!-- Main Content -->
        <main class="main-content">
            <!-- Header -->
            <header class="content-header">
                <div>
                    <h1 class="mb-0">🎤 MDS Interviews</h1>
                    <p class="text-muted mb-0">Manage aspirant interviews and evaluations</p>
                </div>
                <div class="flex gap-4">
                    <a href="interview-guidelines.php" class="btn btn-outline">
                        <span>📋</span>
                        Guidelines
                    </a>
                    <button onclick="scheduleInterview()" class="btn btn-primary">
                        <span>📅</span>
                        Schedule Interview
                    </button>
                </div>
            </header>
            
            <!-- Content Body -->
            <div class="content-body">
                <!-- Statistics Overview -->
                <div class="dashboard-grid" style="margin-bottom: var(--space-6);">
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
                            <div class="stat-icon" style="background: var(--status-success)20; color: var(--status-success);">
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
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Success Rate</div>
                            <div class="stat-icon" style="background: var(--role-mds)20; color: var(--role-mds);">
                                📈
                            </div>
                        </div>
                        <div class="stat-value">
                            <?php 
                            $totalInterviews = count($interviewAspirants);
                            $successRate = $totalInterviews > 0 ? round((count($completed) / $totalInterviews) * 100) : 0;
                            echo $successRate . '%';
                            ?>
                        </div>
                        <div class="stat-change <?php echo $successRate >= 80 ? 'positive' : 'negative'; ?>">
                            <span><?php echo $successRate >= 80 ? 'Excellent' : 'Good'; ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Pending Interviews -->
                <div class="data-table" style="margin-bottom: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            📋 Pending Interviews
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Aspirants ready for MDS interviews
                        </p>
                    </div>
                    
                    <?php if (empty($readyForInterview)): ?>
                        <div style="padding: var(--space-8); text-align: center;">
                            <div style="font-size: 3rem; margin-bottom: var(--space-4);">🎉</div>
                            <h3 style="margin: 0 0 var(--space-2) 0; color: var(--gray-900);">All Caught Up!</h3>
                            <p style="margin: 0; color: var(--gray-600);">No pending interviews at this time.</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>PCNC Completed</th>
                                    <th>Ministry Interest</th>
                                    <th>Priority</th>
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
                                            <?php echo htmlspecialchars($aspirant['ministry_preference_1_name'] ?: 'Not specified'); ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-warning">Normal</span>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: var(--space-2);">
                                                <button onclick="scheduleInterviewFor(<?php echo $aspirant['id']; ?>)" class="btn btn-sm btn-primary">Schedule</button>
                                                <a href="../aspirants/view.php?id=<?php echo $aspirant['id']; ?>" class="btn btn-sm btn-outline">View Profile</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
                
                <!-- All Interview Candidates -->
                <div class="data-table">
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
                                            <button onclick="scheduleInterviewFor(<?php echo $aspirant['id']; ?>)" class="btn btn-sm btn-primary" style="flex: 1;">Schedule</button>
                                        <?php elseif ($aspirant['step_status'] == 'in_progress'): ?>
                                            <button onclick="completeInterview(<?php echo $aspirant['id']; ?>)" class="btn btn-sm btn-success" style="flex: 1;">Complete</button>
                                        <?php endif; ?>
                                        <a href="../aspirants/view.php?id=<?php echo $aspirant['id']; ?>" class="btn btn-sm btn-outline" style="flex: 1;">View</a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        function scheduleInterview() {
            alert('Schedule interview functionality would be implemented here');
        }
        
        function scheduleInterviewFor(aspirantId) {
            alert('Schedule interview for aspirant ID: ' + aspirantId);
        }
        
        function completeInterview(aspirantId) {
            alert('Complete interview for aspirant ID: ' + aspirantId);
        }
    </script>
    
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
