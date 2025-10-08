<?php
/**
 * Ministries Management Page - STAR Volunteer Management System
 */

require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../models/Ministry.php';
require_once __DIR__ . '/../helpers/AssetHelper.php';
require_once __DIR__ . '/../models/Aspirant.php';

Auth::requireAnyRole(['administrator', 'pastor', 'mds', 'mentor']);

$user = Auth::user();
$ministryModel = new Ministry();
$aspirantModel = new Aspirant();

// Get ministries with statistics
$ministries = $ministryModel->getAllWithStats();

$appConfig = require __DIR__ . '/../../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ministries Management - <?php echo $appConfig['name']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo AssetHelper::asset('css/modern-design-system.css'); ?>">
    <link rel="stylesheet" href="<?php echo AssetHelper::asset('css/dashboard-override.css'); ?>">
    <link rel="stylesheet" href="<?php echo AssetHelper::asset('css/layout-fixes.css'); ?>">
    <link rel="stylesheet" href="<?php echo AssetHelper::asset('css/force-layout.css'); ?>">
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
                    <div class="user-role"><?php echo ucfirst($user['role']); ?></div>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Overview</div>
                    <a href="<?php echo AssetHelper::url('/dashboard'); ?>" class="nav-item">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="<?php echo AssetHelper::url('/aspirants'); ?>" class="nav-item">
                        <span class="nav-icon">🌟</span>
                        Aspirants
                    </a>
                    <a href="<?php echo AssetHelper::url('/ministries'); ?>" class="nav-item active">
                        <span class="nav-icon">⛪</span>
                        Ministries
                    </a>
                    <?php if (in_array($user['role'], ['administrator', 'pastor'])): ?>
                        <a href="users.php" class="nav-item">
                            <span class="nav-icon">👥</span>
                            Users
                        </a>
                    <?php endif; ?>
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
        <main class="main-content">
            <!-- Header -->
            <header class="content-header">
                <div>
                    <h1 class="mb-0">⛪ Ministries Management</h1>
                    <p class="text-muted mb-0">Manage church ministries and volunteer assignments</p>
                </div>
                <div class="flex gap-4">
                    <?php if (in_array($user['role'], ['administrator', 'pastor'])): ?>
                        <a href="ministries/add.php" class="btn btn-primary">
                            <span>➕</span>
                            Add Ministry
                        </a>
                    <?php endif; ?>
                </div>
            </header>
            
            <!-- Content Body -->
            <div class="content-body">
                <!-- Statistics Overview -->
                <div class="dashboard-grid" style="margin-bottom: var(--space-6);">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Total Ministries</div>
                            <div class="stat-icon" style="background: var(--role-pastor)20; color: var(--role-pastor);">
                                ⛪
                            </div>
                        </div>
                        <div class="stat-value"><?php echo count($ministries); ?></div>
                        <div class="stat-change positive">
                            <span>Active departments</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Total Volunteers</div>
                            <div class="stat-icon" style="background: var(--role-aspirant)20; color: var(--role-aspirant);">
                                🌟
                            </div>
                        </div>
                        <div class="stat-value">
                            <?php 
                            $totalVolunteers = array_sum(array_column($ministries, 'assigned_aspirants'));
                            echo $totalVolunteers;
                            ?>
                        </div>
                        <div class="stat-change positive">
                            <span>Assigned aspirants</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Interested Aspirants</div>
                            <div class="stat-icon" style="background: var(--status-pending)20; color: var(--status-pending);">
                                👋
                            </div>
                        </div>
                        <div class="stat-value">
                            <?php 
                            $totalInterested = array_sum(array_column($ministries, 'interested_aspirants'));
                            echo $totalInterested;
                            ?>
                        </div>
                        <div class="stat-change positive">
                            <span>Showing interest</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Conversion Rate</div>
                            <div class="stat-icon" style="background: var(--status-success)20; color: var(--status-success);">
                                📈
                            </div>
                        </div>
                        <div class="stat-value">
                            <?php 
                            $conversionRate = $totalInterested > 0 ? round(($totalVolunteers / $totalInterested) * 100) : 0;
                            echo $conversionRate . '%';
                            ?>
                        </div>
                        <div class="stat-change <?php echo $conversionRate >= 70 ? 'positive' : 'negative'; ?>">
                            <span><?php echo $conversionRate >= 70 ? 'Excellent' : 'Needs improvement'; ?></span>
                        </div>
                    </div>
                </div>
                
                <!-- Ministries Grid -->
                <div class="data-table">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            All Ministries (<?php echo count($ministries); ?>)
                        </h2>
                        <p style="margin: var(--space-2) 0 0 0; color: var(--gray-600); font-size: var(--text-sm);">
                            Overview of all church ministries and their volunteer statistics
                        </p>
                    </div>
                    
                    <?php if (empty($ministries)): ?>
                        <div style="padding: var(--space-8); text-align: center;">
                            <div style="font-size: 3rem; margin-bottom: var(--space-4);">⛪</div>
                            <h3 style="margin: 0 0 var(--space-2) 0; color: var(--gray-900);">No Ministries Found</h3>
                            <p style="margin: 0; color: var(--gray-600);">Start by adding your first ministry.</p>
                        </div>
                    <?php else: ?>
                        <div style="padding: var(--space-6);">
                            <div class="dashboard-grid">
                                <?php foreach ($ministries as $ministry): ?>
                                    <div class="stat-card" style="border-left: 4px solid var(--role-pastor);">
                                        <div class="stat-header">
                                            <div class="stat-title"><?php echo htmlspecialchars($ministry['name']); ?></div>
                                            <div class="stat-icon" style="background: var(--role-pastor)20; color: var(--role-pastor);">
                                                ⛪
                                            </div>
                                        </div>
                                        
                                        <div style="margin-bottom: var(--space-4);">
                                            <p style="margin: 0; color: var(--gray-600); font-size: var(--text-sm);">
                                                <?php echo htmlspecialchars($ministry['description'] ?: 'No description available'); ?>
                                            </p>
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
                                        
                                        <div style="display: flex; gap: var(--space-2);">
                                            <a href="ministries/view.php?id=<?php echo $ministry['id']; ?>" class="btn btn-sm btn-outline" style="flex: 1;">View Details</a>
                                            <?php if (in_array($user['role'], ['administrator', 'pastor'])): ?>
                                                <a href="ministries/edit.php?id=<?php echo $ministry['id']; ?>" class="btn btn-sm btn-primary" style="flex: 1;">Edit</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
