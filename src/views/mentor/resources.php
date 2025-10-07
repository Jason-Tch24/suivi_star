<?php
/**
 * Mentor Resources Page - STAR Volunteer Management System
 */

require_once __DIR__ . '/../../middleware/Auth.php';

Auth::requireRole('mentor');

$user = Auth::user();

$appConfig = require __DIR__ . '/../../../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mentor Resources - <?php echo $appConfig['name']; ?></title>
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
                    <a href="../dashboard.php" class="nav-item">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Mentoring</div>
                    <a href="aspirants.php" class="nav-item">
                        <span class="nav-icon">🌟</span>
                        My Aspirants
                    </a>
                    <a href="schedule.php" class="nav-item">
                        <span class="nav-icon">📅</span>
                        Schedule
                    </a>
                    <a href="reports.php" class="nav-item">
                        <span class="nav-icon">📝</span>
                        Reports
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Resources</div>
                    <a href="resources.php" class="nav-item active">
                        <span class="nav-icon">📚</span>
                        Resources
                    </a>
                    <a href="progress.php" class="nav-item">
                        <span class="nav-icon">📈</span>
                        Progress
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
                    <h1 class="mb-0">📚 Mentor Resources</h1>
                    <p class="text-muted mb-0">Training materials and guides for effective mentoring</p>
                </div>
                <div class="flex gap-4">
                    <button onclick="requestResource()" class="btn btn-outline">
                        <span>📝</span>
                        Request Resource
                    </button>
                    <button onclick="downloadAll()" class="btn btn-primary">
                        <span>📥</span>
                        Download All
                    </button>
                </div>
            </header>
            
            <!-- Content Body -->
            <div class="content-body">
                <!-- Quick Access -->
                <div class="dashboard-grid" style="margin-bottom: var(--space-6);">
                    <div class="stat-card" style="cursor: pointer;" onclick="openResource('mentoring-guide')">
                        <div class="stat-header">
                            <div class="stat-title">Mentoring Guide</div>
                            <div class="stat-icon" style="background: var(--role-mentor)20; color: var(--role-mentor);">
                                📖
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Complete guide to effective mentoring in the STAR program
                        </div>
                        <div class="btn btn-sm btn-primary" style="width: 100%;">Open Guide</div>
                    </div>
                    
                    <div class="stat-card" style="cursor: pointer;" onclick="openResource('step-by-step')">
                        <div class="stat-header">
                            <div class="stat-title">Step-by-Step Manual</div>
                            <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                📋
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Detailed instructions for each step of the STAR journey
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">View Manual</div>
                    </div>
                    
                    <div class="stat-card" style="cursor: pointer;" onclick="openResource('evaluation-forms')">
                        <div class="stat-header">
                            <div class="stat-title">Evaluation Forms</div>
                            <div class="stat-icon" style="background: var(--status-warning)20; color: var(--status-warning);">
                                📝
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Templates for evaluating aspirant progress
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">Download Forms</div>
                    </div>
                    
                    <div class="stat-card" style="cursor: pointer;" onclick="openResource('best-practices')">
                        <div class="stat-header">
                            <div class="stat-title">Best Practices</div>
                            <div class="stat-icon" style="background: var(--role-mentor)20; color: var(--role-mentor);">
                                ⭐
                            </div>
                        </div>
                        <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                            Tips and strategies from experienced mentors
                        </div>
                        <div class="btn btn-sm btn-outline" style="width: 100%;">Read Tips</div>
                    </div>
                </div>
                
                <!-- Training Materials -->
                <div class="data-table" style="margin-bottom: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            📚 Training Materials
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Essential resources for mentoring aspirants through each step
                        </p>
                    </div>
                    
                    <div style="padding: var(--space-6);">
                        <div class="dashboard-grid">
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-title">Step 1: Application Review</div>
                                    <div class="stat-icon" style="background: var(--role-mentor)20; color: var(--role-mentor);">
                                        1
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Guide for reviewing and validating initial applications
                                </div>
                                <div style="display: flex; gap: var(--space-2);">
                                    <button onclick="downloadResource('step1-guide')" class="btn btn-sm btn-outline" style="flex: 1;">Download</button>
                                    <button onclick="viewResource('step1-guide')" class="btn btn-sm btn-primary" style="flex: 1;">View</button>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-title">Step 2: PCNC Training</div>
                                    <div class="stat-icon" style="background: var(--role-mentor)20; color: var(--role-mentor);">
                                        2
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Supporting aspirants through PCNC training requirements
                                </div>
                                <div style="display: flex; gap: var(--space-2);">
                                    <button onclick="downloadResource('step2-guide')" class="btn btn-sm btn-outline" style="flex: 1;">Download</button>
                                    <button onclick="viewResource('step2-guide')" class="btn btn-sm btn-primary" style="flex: 1;">View</button>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-title">Step 4: Ministry Training</div>
                                    <div class="stat-icon" style="background: var(--role-mentor)20; color: var(--role-mentor);">
                                        4
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Guiding aspirants through ministry-specific training
                                </div>
                                <div style="display: flex; gap: var(--space-2);">
                                    <button onclick="downloadResource('step4-guide')" class="btn btn-sm btn-outline" style="flex: 1;">Download</button>
                                    <button onclick="viewResource('step4-guide')" class="btn btn-sm btn-primary" style="flex: 1;">View</button>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-title">Step 5: Evaluation</div>
                                    <div class="stat-icon" style="background: var(--role-mentor)20; color: var(--role-mentor);">
                                        5
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Conducting thorough evaluations and providing feedback
                                </div>
                                <div style="display: flex; gap: var(--space-2);">
                                    <button onclick="downloadResource('step5-guide')" class="btn btn-sm btn-outline" style="flex: 1;">Download</button>
                                    <button onclick="viewResource('step5-guide')" class="btn btn-sm btn-primary" style="flex: 1;">View</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Video Resources -->
                <div class="data-table" style="margin-bottom: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            🎥 Video Resources
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Video tutorials and training sessions for mentors
                        </p>
                    </div>
                    
                    <div style="padding: var(--space-6);">
                        <div class="dashboard-grid">
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-title">Effective Mentoring Techniques</div>
                                    <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                        🎥
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-2);">
                                    Duration: 45 minutes
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Learn proven techniques for effective mentoring relationships
                                </div>
                                <button onclick="playVideo('mentoring-techniques')" class="btn btn-sm btn-primary" style="width: 100%;">▶️ Watch Video</button>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-title">Handling Difficult Conversations</div>
                                    <div class="stat-icon" style="background: var(--status-warning)20; color: var(--status-warning);">
                                        🎥
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-2);">
                                    Duration: 30 minutes
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Navigate challenging conversations with aspirants
                                </div>
                                <button onclick="playVideo('difficult-conversations')" class="btn btn-sm btn-primary" style="width: 100%;">▶️ Watch Video</button>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-title">STAR Program Overview</div>
                                    <div class="stat-icon" style="background: var(--role-mentor)20; color: var(--role-mentor);">
                                        🎥
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-2);">
                                    Duration: 25 minutes
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Complete overview of the STAR volunteer program
                                </div>
                                <button onclick="playVideo('star-overview')" class="btn btn-sm btn-primary" style="width: 100%;">▶️ Watch Video</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Section -->
                <div class="data-table">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            ❓ Frequently Asked Questions
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Common questions and answers for mentors
                        </p>
                    </div>
                    
                    <div style="padding: var(--space-6);">
                        <div style="display: flex; flex-direction: column; gap: var(--space-4);">
                            <div style="padding: var(--space-4); background: var(--gray-50); border-radius: var(--radius-md);">
                                <h4 style="margin: 0 0 var(--space-2) 0; color: var(--gray-900);">How often should I meet with my aspirants?</h4>
                                <p style="margin: 0; color: var(--gray-600); font-size: var(--text-sm);">
                                    We recommend meeting with aspirants at least once every two weeks, with more frequent contact during critical steps like ministry training.
                                </p>
                            </div>
                            
                            <div style="padding: var(--space-4); background: var(--gray-50); border-radius: var(--radius-md);">
                                <h4 style="margin: 0 0 var(--space-2) 0; color: var(--gray-900);">What should I do if an aspirant is struggling?</h4>
                                <p style="margin: 0; color: var(--gray-600); font-size: var(--text-sm);">
                                    Provide additional support and resources. If challenges persist, contact the MDS team or program administrators for guidance.
                                </p>
                            </div>
                            
                            <div style="padding: var(--space-4); background: var(--gray-50); border-radius: var(--radius-md);">
                                <h4 style="margin: 0 0 var(--space-2) 0; color: var(--gray-900);">How do I submit evaluation reports?</h4>
                                <p style="margin: 0; color: var(--gray-600); font-size: var(--text-sm);">
                                    Use the Reports section in your dashboard to submit detailed evaluation reports for each aspirant at the completion of Step 5.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        function requestResource() {
            alert('Request resource functionality would be implemented here');
        }
        
        function downloadAll() {
            alert('Download all resources functionality would be implemented here');
        }
        
        function openResource(resourceType) {
            alert('Opening resource: ' + resourceType);
        }
        
        function downloadResource(resource) {
            alert('Downloading resource: ' + resource);
        }
        
        function viewResource(resource) {
            alert('Viewing resource: ' + resource);
        }
        
        function playVideo(videoId) {
            alert('Playing video: ' + videoId);
        }
    </script>
    
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
