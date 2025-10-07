<?php
/**
 * Aspirants Management Page - STAR Volunteer Management System
 */

require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../models/Aspirant.php';
require_once __DIR__ . '/../models/Ministry.php';

Auth::requireRole(['administrator', 'pastor', 'mds', 'mentor']);

$user = Auth::user();
$aspirantModel = new Aspirant();
$ministryModel = new Ministry();

// Get filter parameters
$status = $_GET['status'] ?? 'all';
$ministry = $_GET['ministry'] ?? 'all';
$step = $_GET['step'] ?? 'all';
$search = $_GET['search'] ?? '';

// Get aspirants with filters
$aspirants = $aspirantModel->getFiltered($status, $ministry, $step, $search);
$ministries = $ministryModel->getAll();

$appConfig = require __DIR__ . '/../../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aspirants Management - <?php echo $appConfig['name']; ?></title>
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
                    <div class="user-role"><?php echo ucfirst($user['role']); ?></div>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Overview</div>
                    <a href="dashboard.php" class="nav-item">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="aspirants.php" class="nav-item active">
                        <span class="nav-icon">🌟</span>
                        Aspirants
                    </a>
                    <a href="ministries.php" class="nav-item">
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
                    <a href="logout.php" class="nav-item">
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
                    <h1 class="mb-0">🌟 Aspirants Management</h1>
                    <p class="text-muted mb-0">Manage and track all STAR program aspirants</p>
                </div>
                <div class="flex gap-4">
                    <a href="aspirants/add.php" class="btn btn-primary">
                        <span>➕</span>
                        Add Aspirant
                    </a>
                </div>
            </header>
            
            <!-- Content Body -->
            <div class="content-body">
                <!-- Filters -->
                <div class="data-table" style="margin-bottom: var(--space-6);">
                    <div style="padding: var(--space-4); border-bottom: 1px solid var(--gray-200);">
                        <h3 style="margin: 0; font-size: var(--text-lg); font-weight: 600; color: var(--gray-900);">
                            🔍 Filters
                        </h3>
                    </div>
                    
                    <div style="padding: var(--space-4);">
                        <form method="GET" class="dashboard-grid" style="align-items: end;">
                            <div>
                                <label style="display: block; margin-bottom: var(--space-2); font-weight: 500; color: var(--gray-700);">Search</label>
                                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Name or email..." class="form-input">
                            </div>
                            
                            <div>
                                <label style="display: block; margin-bottom: var(--space-2); font-weight: 500; color: var(--gray-700);">Status</label>
                                <select name="status" class="form-select">
                                    <option value="all" <?php echo $status === 'all' ? 'selected' : ''; ?>>All Status</option>
                                    <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="completed" <?php echo $status === 'completed' ? 'selected' : ''; ?>>Completed</option>
                                    <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            
                            <div>
                                <label style="display: block; margin-bottom: var(--space-2); font-weight: 500; color: var(--gray-700);">Ministry</label>
                                <select name="ministry" class="form-select">
                                    <option value="all" <?php echo $ministry === 'all' ? 'selected' : ''; ?>>All Ministries</option>
                                    <?php foreach ($ministries as $min): ?>
                                        <option value="<?php echo $min['id']; ?>" <?php echo $ministry == $min['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($min['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label style="display: block; margin-bottom: var(--space-2); font-weight: 500; color: var(--gray-700);">Step</label>
                                <select name="step" class="form-select">
                                    <option value="all" <?php echo $step === 'all' ? 'selected' : ''; ?>>All Steps</option>
                                    <option value="1" <?php echo $step === '1' ? 'selected' : ''; ?>>Step 1</option>
                                    <option value="2" <?php echo $step === '2' ? 'selected' : ''; ?>>Step 2</option>
                                    <option value="3" <?php echo $step === '3' ? 'selected' : ''; ?>>Step 3</option>
                                    <option value="4" <?php echo $step === '4' ? 'selected' : ''; ?>>Step 4</option>
                                    <option value="5" <?php echo $step === '5' ? 'selected' : ''; ?>>Step 5</option>
                                    <option value="6" <?php echo $step === '6' ? 'selected' : ''; ?>>Step 6</option>
                                </select>
                            </div>
                            
                            <div style="display: flex; gap: var(--space-2);">
                                <button type="submit" class="btn btn-primary">Filter</button>
                                <a href="aspirants.php" class="btn btn-outline">Clear</a>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Aspirants Table -->
                <div class="data-table">
                    <div style="padding: var(--space-6); border-bottom: 1px solid var(--gray-200);">
                        <h2 style="margin: 0; font-size: var(--text-xl); font-weight: 600; color: var(--gray-900);">
                            Aspirants (<?php echo count($aspirants); ?>)
                        </h2>
                    </div>
                    
                    <?php if (empty($aspirants)): ?>
                        <div style="padding: var(--space-8); text-align: center;">
                            <div style="font-size: 3rem; margin-bottom: var(--space-4);">🔍</div>
                            <h3 style="margin: 0 0 var(--space-2) 0; color: var(--gray-900);">No Aspirants Found</h3>
                            <p style="margin: 0; color: var(--gray-600);">Try adjusting your filters or add a new aspirant.</p>
                        </div>
                    <?php else: ?>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Current Step</th>
                                    <th>Status</th>
                                    <th>Ministry</th>
                                    <th>Applied</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($aspirants as $aspirant): ?>
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
                                            <span class="badge badge-primary">
                                                Step <?php echo $aspirant['current_step']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge <?php 
                                                echo $aspirant['status'] === 'active' ? 'badge-success' : 
                                                    ($aspirant['status'] === 'completed' ? 'badge-primary' : 'badge-secondary');
                                            ?>">
                                                <?php echo ucfirst($aspirant['status']); ?>
                                            </span>
                                        </td>
                                        <td style="color: var(--gray-600);">
                                            <?php echo htmlspecialchars($aspirant['assigned_ministry_name'] ?: 'Not assigned'); ?>
                                        </td>
                                        <td style="color: var(--gray-600);">
                                            <?php echo date('M j, Y', strtotime($aspirant['application_date'])); ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: var(--space-2);">
                                                <a href="aspirants/view.php?id=<?php echo $aspirant['id']; ?>" class="btn btn-sm btn-outline">View</a>
                                                <?php if (in_array($user['role'], ['administrator', 'pastor'])): ?>
                                                    <a href="aspirants/edit.php?id=<?php echo $aspirant['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
    
    <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
