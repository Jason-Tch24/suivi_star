<?php
/**
 * Aspirant Resources Page - STAR Volunteer Management System
 */

require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../models/Aspirant.php';

Auth::requireRole('aspirant');

$user = Auth::user();
$aspirantModel = new Aspirant();

// Get aspirant data
$aspirant = $aspirantModel->findByUserId($user['id']);

$appConfig = require __DIR__ . '/../../../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Resources - <?php echo $appConfig['name']; ?></title>
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
                    <a href="../dashboard.php" class="nav-item">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">My Journey</div>
                    <a href="progress.php" class="nav-item">
                        <span class="nav-icon">🚀</span>
                        Progress
                    </a>
                    <a href="schedule.php" class="nav-item">
                        <span class="nav-icon">📅</span>
                        Schedule
                    </a>
                    <a href="ministry-matches.php" class="nav-item">
                        <span class="nav-icon">⛪</span>
                        Ministry Matches
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Resources</div>
                    <a href="resources.php" class="nav-item active">
                        <span class="nav-icon">📚</span>
                        Resources
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
                    <h1 class="mb-0">📚 My Resources</h1>
                    <p class="text-muted mb-0">Training materials and guides for your STAR journey</p>
                </div>
                <div class="flex gap-4">
                    <button onclick="downloadAll()" class="btn btn-primary">
                        <span>📥</span>
                        Download All
                    </button>
                </div>
            </header>
            
            <!-- Content Body -->
            <div class="content-body">
                <!-- Current Step Resources -->
                <div class="data-table" style="margin-bottom: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            🎯 Current Step Resources
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Resources specific to your current step in the STAR program
                        </p>
                    </div>
                    
                    <div style="padding: var(--space-6);">
                        <div class="stat-card" style="border-left: 4px solid var(--role-aspirant);">
                            <div style="display: flex; align-items: center; gap: var(--space-4); margin-bottom: var(--space-4);">
                                <div style="width: 60px; height: 60px; border-radius: 50%; background: var(--role-aspirant); color: white; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700;">
                                    <?php echo $aspirant['current_step'] ?? 1; ?>
                                </div>
                                <div style="flex: 1;">
                                    <h3 style="margin: 0 0 var(--space-1) 0; font-size: var(--text-lg); font-weight: 600; color: var(--gray-900);">
                                        Step <?php echo $aspirant['current_step'] ?? 1; ?> Resources
                                    </h3>
                                    <p style="margin: 0; color: var(--gray-600); font-size: var(--text-sm);">
                                        Materials and guides for your current step
                                    </p>
                                </div>
                            </div>
                            
                            <div class="dashboard-grid">
                                <button onclick="openResource('current-step-guide')" class="btn btn-primary">📖 Step Guide</button>
                                <button onclick="openResource('current-step-checklist')" class="btn btn-outline">✅ Checklist</button>
                                <button onclick="openResource('current-step-video')" class="btn btn-outline">🎥 Video Tutorial</button>
                                <button onclick="openResource('current-step-faq')" class="btn btn-outline">❓ FAQ</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- General Resources -->
                <div class="data-table" style="margin-bottom: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            📚 General Resources
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Essential resources for all STAR program participants
                        </p>
                    </div>
                    
                    <div style="padding: var(--space-6);">
                        <div class="dashboard-grid">
                            <div class="stat-card" style="cursor: pointer;" onclick="openResource('star-handbook')">
                                <div class="stat-header">
                                    <div class="stat-title">STAR Handbook</div>
                                    <div class="stat-icon" style="background: var(--role-aspirant)20; color: var(--role-aspirant);">
                                        📖
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Complete guide to the STAR volunteer program
                                </div>
                                <div class="btn btn-sm btn-primary" style="width: 100%;">Read Handbook</div>
                            </div>
                            
                            <div class="stat-card" style="cursor: pointer;" onclick="openResource('ministry-overview')">
                                <div class="stat-header">
                                    <div class="stat-title">Ministry Overview</div>
                                    <div class="stat-icon" style="background: var(--role-pastor)20; color: var(--role-pastor);">
                                        ⛪
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Learn about different ministry opportunities
                                </div>
                                <div class="btn btn-sm btn-outline" style="width: 100%;">Explore Ministries</div>
                            </div>
                            
                            <div class="stat-card" style="cursor: pointer;" onclick="openResource('volunteer-expectations')">
                                <div class="stat-header">
                                    <div class="stat-title">Volunteer Expectations</div>
                                    <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                        📋
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Understand what's expected of STAR volunteers
                                </div>
                                <div class="btn btn-sm btn-outline" style="width: 100%;">View Expectations</div>
                            </div>
                            
                            <div class="stat-card" style="cursor: pointer;" onclick="openResource('contact-directory')">
                                <div class="stat-header">
                                    <div class="stat-title">Contact Directory</div>
                                    <div class="stat-icon" style="background: var(--status-warning)20; color: var(--status-warning);">
                                        📞
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Important contacts for your STAR journey
                                </div>
                                <div class="btn btn-sm btn-outline" style="width: 100%;">View Contacts</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Training Videos -->
                <div class="data-table" style="margin-bottom: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            🎥 Training Videos
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Video resources to support your learning journey
                        </p>
                    </div>
                    
                    <div style="padding: var(--space-6);">
                        <div class="dashboard-grid">
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-title">Welcome to STAR</div>
                                    <div class="stat-icon" style="background: var(--role-aspirant)20; color: var(--role-aspirant);">
                                        🎥
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-2);">
                                    Duration: 15 minutes
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Introduction to the STAR volunteer program
                                </div>
                                <button onclick="playVideo('welcome-star')" class="btn btn-sm btn-primary" style="width: 100%;">▶️ Watch Video</button>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-title">PCNC Training Overview</div>
                                    <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                        🎥
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-2);">
                                    Duration: 20 minutes
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    What to expect from PCNC training
                                </div>
                                <button onclick="playVideo('pcnc-overview')" class="btn btn-sm btn-primary" style="width: 100%;">▶️ Watch Video</button>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-title">Ministry Exploration</div>
                                    <div class="stat-icon" style="background: var(--role-pastor)20; color: var(--role-pastor);">
                                        🎥
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-2);">
                                    Duration: 25 minutes
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Discover different ministry opportunities
                                </div>
                                <button onclick="playVideo('ministry-exploration')" class="btn btn-sm btn-primary" style="width: 100%;">▶️ Watch Video</button>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Help -->
                <div class="data-table">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            🆘 Need Help?
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Get support when you need it
                        </p>
                    </div>
                    
                    <div style="padding: var(--space-6);">
                        <div class="dashboard-grid">
                            <div class="stat-card" style="cursor: pointer;" onclick="contactMentor()">
                                <div class="stat-header">
                                    <div class="stat-title">Contact My Mentor</div>
                                    <div class="stat-icon" style="background: var(--role-mentor)20; color: var(--role-mentor);">
                                        🤝
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Get personalized guidance from your assigned mentor
                                </div>
                                <div class="btn btn-sm btn-primary" style="width: 100%;">Contact Mentor</div>
                            </div>
                            
                            <div class="stat-card" style="cursor: pointer;" onclick="viewFAQ()">
                                <div class="stat-header">
                                    <div class="stat-title">FAQ</div>
                                    <div class="stat-icon" style="background: var(--status-warning)20; color: var(--status-warning);">
                                        ❓
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Find answers to frequently asked questions
                                </div>
                                <div class="btn btn-sm btn-outline" style="width: 100%;">View FAQ</div>
                            </div>
                            
                            <div class="stat-card" style="cursor: pointer;" onclick="contactSupport()">
                                <div class="stat-header">
                                    <div class="stat-title">Contact Support</div>
                                    <div class="stat-icon" style="background: var(--role-aspirant)20; color: var(--role-aspirant);">
                                        📧
                                    </div>
                                </div>
                                <div style="color: var(--gray-600); font-size: var(--text-sm); margin-bottom: var(--space-4);">
                                    Reach out to the STAR program administrators
                                </div>
                                <div class="btn btn-sm btn-outline" style="width: 100%;">Contact Support</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        function downloadAll() {
            alert('Download all resources functionality would be implemented here');
        }
        
        function openResource(resourceType) {
            alert('Opening resource: ' + resourceType);
        }
        
        function playVideo(videoId) {
            alert('Playing video: ' + videoId);
        }
        
        function contactMentor() {
            alert('Contact mentor functionality would be implemented here');
        }
        
        function viewFAQ() {
            alert('FAQ functionality would be implemented here');
        }
        
        function contactSupport() {
            alert('Contact support functionality would be implemented here');
        }
    </script>
    
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
