<?php
/**
 * Pastor Reports Page - STAR Volunteer Management System
 */

require_once __DIR__ . '/../../middleware/Auth.php';
require_once __DIR__ . '/../../models/Aspirant.php';
require_once __DIR__ . '/../../models/Ministry.php';
require_once __DIR__ . '/../../models/JourneyStep.php';

Auth::requireRole('pastor');

$user = Auth::user();
$aspirantModel = new Aspirant();
$ministryModel = new Ministry();
$journeyStepModel = new JourneyStep();

// Get comprehensive statistics for reports
$aspirantStats = $aspirantModel->getStatistics();
$stepStats = $journeyStepModel->getStepStatistics();
$ministryStats = $ministryModel->getStatistics();

// Calculate completion rates
$completionRates = [];
for ($i = 1; $i <= 6; $i++) {
    $completionRates[$i] = $journeyStepModel->getCompletionRate($i);
}

$appConfig = require __DIR__ . '/../../../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pastor Reports - <?php echo $appConfig['name']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/modern-design-system.css">
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
                    <a href="../dashboard.php" class="nav-item">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">STAR Process</div>
                    <a href="../aspirants.php" class="nav-item">
                        <span class="nav-icon">🌟</span>
                        Aspirants
                    </a>
                    <a href="../ministries.php" class="nav-item">
                        <span class="nav-icon">⛪</span>
                        Ministries
                    </a>
                    <a href="assignments.php" class="nav-item">
                        <span class="nav-icon">📋</span>
                        Final Assignments
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Reports</div>
                    <a href="reports.php" class="nav-item active">
                        <span class="nav-icon">📈</span>
                        Analytics
                    </a>
                    <a href="performance.php" class="nav-item">
                        <span class="nav-icon">🎯</span>
                        Performance
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
                    <h1 class="mb-0">📈 Pastor Reports & Analytics</h1>
                    <p class="text-muted mb-0">Comprehensive STAR program performance reports</p>
                </div>
                <div class="flex gap-4">
                    <button onclick="exportReport()" class="btn btn-outline">
                        <span>📄</span>
                        Export PDF
                    </button>
                    <button onclick="printReport()" class="btn btn-primary">
                        <span>🖨️</span>
                        Print Report
                    </button>
                </div>
            </header>
            
            <!-- Content Body -->
            <div class="content-body">
                <!-- Executive Summary -->
                <div class="data-table" style="margin-bottom: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            📊 Executive Summary
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Key performance indicators for the STAR program
                        </p>
                    </div>
                    
                    <div style="padding: var(--space-6);">
                        <div class="dashboard-grid">
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-title">Program Completion Rate</div>
                                    <div class="stat-icon" style="background: var(--status-success)20; color: var(--status-success);">
                                        🎯
                                    </div>
                                </div>
                                <div class="stat-value">
                                    <?php 
                                    $completionRate = $aspirantStats['total'] > 0 
                                        ? round(($aspirantStats['by_status']['completed'] / $aspirantStats['total']) * 100) 
                                        : 0;
                                    echo $completionRate . '%';
                                    ?>
                                </div>
                                <div class="stat-change <?php echo $completionRate >= 70 ? 'positive' : 'negative'; ?>">
                                    <span><?php echo $completionRate >= 70 ? 'Above target' : 'Below target'; ?></span>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-title">Active Aspirants</div>
                                    <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                        🚀
                                    </div>
                                </div>
                                <div class="stat-value"><?php echo $aspirantStats['by_status']['active'] ?? 0; ?></div>
                                <div class="stat-change positive">
                                    <span>Currently in program</span>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-title">Ministry Placements</div>
                                    <div class="stat-icon" style="background: var(--role-pastor)20; color: var(--role-pastor);">
                                        ⛪
                                    </div>
                                </div>
                                <div class="stat-value">
                                    <?php 
                                    $totalPlacements = array_sum(array_column($ministryStats, 'assigned_aspirants'));
                                    echo $totalPlacements;
                                    ?>
                                </div>
                                <div class="stat-change positive">
                                    <span>Successful assignments</span>
                                </div>
                            </div>
                            
                            <div class="stat-card">
                                <div class="stat-header">
                                    <div class="stat-title">Average Time to Complete</div>
                                    <div class="stat-icon" style="background: var(--status-warning)20; color: var(--status-warning);">
                                        ⏱️
                                    </div>
                                </div>
                                <div class="stat-value">12 weeks</div>
                                <div class="stat-change positive">
                                    <span>Within target range</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Step Performance Analysis -->
                <div class="data-table" style="margin-bottom: var(--space-6);">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            📈 Step Performance Analysis
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Detailed breakdown of completion rates by step
                        </p>
                    </div>
                    
                    <div style="padding: var(--space-6);">
                        <div style="background: white; border-radius: var(--radius-lg); padding: var(--space-4); border: 1px solid var(--gray-200); margin-bottom: var(--space-6);">
                            <canvas id="stepPerformanceChart" width="400" height="200"></canvas>
                        </div>
                        
                        <div class="dashboard-grid">
                            <?php for ($i = 1; $i <= 6; $i++): ?>
                                <div class="stat-card">
                                    <div class="stat-header">
                                        <div class="stat-title">Step <?php echo $i; ?></div>
                                        <div class="stat-icon" style="background: var(--role-pastor)20; color: var(--role-pastor);">
                                            <?php echo $i; ?>
                                        </div>
                                    </div>
                                    <div class="stat-value"><?php echo $completionRates[$i]; ?>%</div>
                                    <div class="stat-change <?php echo $completionRates[$i] >= 80 ? 'positive' : 'negative'; ?>">
                                        <span><?php echo $completionRates[$i] >= 80 ? 'Good' : 'Needs attention'; ?></span>
                                    </div>
                                </div>
                            <?php endfor; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Ministry Performance -->
                <div class="data-table">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            ⛪ Ministry Performance Report
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Volunteer interest and assignment conversion rates by ministry
                        </p>
                    </div>
                    
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Ministry</th>
                                <th>Interested</th>
                                <th>Assigned</th>
                                <th>Conversion Rate</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($ministryStats as $ministry): ?>
                                <tr>
                                    <td>
                                        <div style="font-weight: 600; color: var(--gray-900);">
                                            <?php echo htmlspecialchars($ministry['name']); ?>
                                        </div>
                                    </td>
                                    <td style="color: var(--gray-600);">
                                        <?php echo $ministry['interested_aspirants']; ?>
                                    </td>
                                    <td style="color: var(--gray-600);">
                                        <?php echo $ministry['assigned_aspirants']; ?>
                                    </td>
                                    <td>
                                        <?php 
                                        $conversion = $ministry['interested_aspirants'] > 0 
                                            ? round(($ministry['assigned_aspirants'] / $ministry['interested_aspirants']) * 100) 
                                            : 0;
                                        echo $conversion . '%';
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge <?php echo $conversion >= 70 ? 'badge-success' : ($conversion >= 50 ? 'badge-warning' : 'badge-error'); ?>">
                                            <?php echo $conversion >= 70 ? 'Excellent' : ($conversion >= 50 ? 'Good' : 'Needs Improvement'); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
    
    <script>
        // Step Performance Chart
        const ctx = document.getElementById('stepPerformanceChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Step 1', 'Step 2', 'Step 3', 'Step 4', 'Step 5', 'Step 6'],
                datasets: [{
                    label: 'Completion Rate (%)',
                    data: [<?php echo implode(',', array_values($completionRates)); ?>],
                    backgroundColor: 'var(--role-pastor)',
                    borderColor: 'var(--role-pastor)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Step Completion Rates'
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
        
        function exportReport() {
            alert('Export functionality would be implemented here');
        }
        
        function printReport() {
            window.print();
        }
    </script>
    
    <?php include __DIR__ . '/../partials/footer.php'; ?>
</body>
</html>
