<?php
/**
 * Enhanced User Management Interface - STAR System
 * Modern user management with inline editing and bulk operations
 */

session_start();

require_once __DIR__ . '/../src/middleware/Auth.php';
require_once __DIR__ . '/../src/controllers/EnhancedUserController.php';

// Check authentication and permissions
if (!Auth::check()) {
    header('Location: ../login.php');
    exit;
}

$currentUser = Auth::user();
$userController = new EnhancedUserController();

// Check if user has permission to manage users
if (!Auth::hasEnhancedPermission('manage_users')) {
    header('Location: ../dashboard.php');
    exit;
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'inline_update':
                $userId = $_POST['user_id'];
                $field = $_POST['field'];
                $value = $_POST['value'];
                
                $userController->updateUserWithRoleData($userId, [$field => $value]);
                echo json_encode(['success' => true, 'message' => 'User updated successfully']);
                break;
                
            case 'bulk_update':
                $userIds = $_POST['user_ids'];
                $updates = $_POST['updates'];
                
                $result = $userController->bulkUpdateUsers($userIds, $updates);
                echo json_encode(['success' => true, 'result' => $result]);
                break;
                
            default:
                throw new Exception('Invalid action');
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

// Get filter parameters
$role = $_GET['role'] ?? '';
$status = $_GET['status'] ?? '';
$page = max(1, intval($_GET['page'] ?? 1));
$limit = 20;

// Get users and statistics
$usersData = $userController->getAllUsers($page, $limit, $role, $status);
$users = $usersData['users'] ?? [];
$totalUsers = $usersData['total'] ?? 0;
$totalPages = ceil($totalUsers / $limit);

// Get user statistics
$stats = [
    'total_active' => 0,
    'recent_registrations' => 0,
    'role_stats' => []
];

try {
    $db = Database::getInstance();
    
    // Get total active users
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE status = 'active'");
    $stats['total_active'] = $stmt->fetch()['count'];
    
    // Get recent registrations (last 30 days)
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
    $stats['recent_registrations'] = $stmt->fetch()['count'];
    
    // Get role statistics
    $stmt = $db->query("SELECT role, COUNT(*) as count FROM users WHERE status = 'active' GROUP BY role");
    $stats['role_stats'] = $stmt->fetchAll();
} catch (Exception $e) {
    // Handle database errors gracefully
}

$availableRoles = $userController->getAvailableRoles();
$appConfig = require __DIR__ . '/../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - <?php echo $appConfig['name']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/modern-design-system.css">
    <link rel="stylesheet" href="../public/css/ai-sidebar.css">
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
                    <div class="user-name"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></div>
                    <div class="user-role"><?php echo ucfirst($currentUser['role']); ?></div>
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
                    <div class="nav-section-title">Administration</div>
                    <a href="users.php" class="nav-item active">
                        <span class="nav-icon">👥</span>
                        User Management
                    </a>
                    <a href="user-wizard.php" class="nav-item">
                        <span class="nav-icon">➕</span>
                        Create User
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
        <main class="main-content with-ai-sidebar">
            <!-- Header -->
            <header class="content-header">
                <div>
                    <h1 class="mb-0">👥 User Management</h1>
                    <p class="text-muted mb-0">Manage system users, roles, and permissions</p>
                </div>
                <div class="flex gap-4">
                    <a href="user-wizard.php" class="btn btn-primary">
                        <span>➕</span>
                        Add New User
                    </a>
                </div>
            </header>
            
            <!-- Dashboard Content -->
            <div class="content-body">
                <!-- Statistics -->
                <div class="dashboard-grid">
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">Active Users</div>
                            <div class="stat-icon" style="background: var(--primary-100); color: var(--primary-600);">
                                👥
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $stats['total_active']; ?></div>
                        <div class="stat-change positive">
                            <span>↗️</span>
                            <span>Total active</span>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title">New This Month</div>
                            <div class="stat-icon" style="background: var(--status-active)20; color: var(--status-active);">
                                📈
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $stats['recent_registrations']; ?></div>
                        <div class="stat-change positive">
                            <span>Recent registrations</span>
                        </div>
                    </div>
                    
                    <?php foreach ($stats['role_stats'] as $roleStat): ?>
                    <div class="stat-card">
                        <div class="stat-header">
                            <div class="stat-title"><?php echo ucfirst($roleStat['role']); ?>s</div>
                            <div class="stat-icon" style="background: var(--role-<?php echo $roleStat['role']; ?>)20; color: var(--role-<?php echo $roleStat['role']; ?>);">
                                <?php
                                $icons = [
                                    'administrator' => '👑',
                                    'pastor' => '⛪',
                                    'mds' => '👥',
                                    'mentor' => '🤝',
                                    'aspirant' => '🌟'
                                ];
                                echo $icons[$roleStat['role']] ?? '👤';
                                ?>
                            </div>
                        </div>
                        <div class="stat-value"><?php echo $roleStat['count']; ?></div>
                        <div class="stat-change neutral">
                            <span>Active users</span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Filters -->
                <div class="filters-bar">
                    <div class="filter-group">
                        <label class="filter-label">Role:</label>
                        <select id="roleFilter" class="form-select" onchange="filterUsers()" style="min-width: 150px;">
                            <option value="">All Roles</option>
                            <?php foreach ($availableRoles as $roleKey => $roleName): ?>
                            <option value="<?php echo $roleKey; ?>" <?php echo $role === $roleKey ? 'selected' : ''; ?>>
                                <?php echo $roleName; ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="filter-group">
                        <label class="filter-label">Status:</label>
                        <select id="statusFilter" class="form-select" onchange="filterUsers()" style="min-width: 120px;">
                            <option value="">All Statuses</option>
                            <option value="active" <?php echo $status === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                            <option value="suspended" <?php echo $status === 'suspended' ? 'selected' : ''; ?>>Suspended</option>
                        </select>
                    </div>
                    
                    <div style="margin-left: auto;">
                        <input type="search" placeholder="Search users..." class="form-input" style="min-width: 200px;">
                    </div>
                </div>
                
                <!-- Users Table -->
                <div class="data-table">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>User</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Joined</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr data-user-id="<?php echo $user['id']; ?>">
                                <td>
                                    <input type="checkbox" class="user-checkbox" value="<?php echo $user['id']; ?>">
                                </td>
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar" style="background: var(--role-<?php echo $user['role']; ?>);">
                                            <?php
                                            $icons = [
                                                'administrator' => '👑',
                                                'pastor' => '⛪',
                                                'mds' => '👥',
                                                'mentor' => '🤝',
                                                'aspirant' => '🌟'
                                            ];
                                            echo $icons[$user['role']] ?? '👤';
                                            ?>
                                        </div>
                                        <div class="user-details">
                                            <h4 data-editable="text" data-field="first_name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h4>
                                            <p data-editable="email" data-field="email"><?php echo htmlspecialchars($user['email']); ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td data-editable="role" data-field="role">
                                    <span class="badge badge-<?php echo $user['role']; ?>">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td data-editable="status" data-field="status">
                                    <span class="badge badge-<?php echo $user['status'] === 'active' ? 'success' : 'gray'; ?>">
                                        <?php echo ucfirst($user['status']); ?>
                                    </span>
                                </td>
                                <td class="text-muted">
                                    <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                </td>
                                <td>
                                    <div class="flex gap-2">
                                        <button class="btn btn-sm btn-outline" onclick="editUser(<?php echo $user['id']; ?>)">
                                            Edit
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="resetPassword(<?php echo $user['id']; ?>)">
                                            Reset
                                        </button>
                                        <div class="action-menu">
                                            <button class="action-menu-button">⋯</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                <div class="flex justify-center mt-6">
                    <div class="flex gap-2">
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>&role=<?php echo $role; ?>&status=<?php echo $status; ?>" 
                           class="btn btn-sm <?php echo $i === $page ? 'btn-primary' : 'btn-outline'; ?>">
                            <?php echo $i; ?>
                        </a>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Bulk Actions Bar -->
                <div id="bulkActionsBar" class="bulk-actions-bar" style="display: none;">
                    <div class="flex items-center gap-4">
                        <span id="selectedCount">0 users selected</span>
                        <button class="btn btn-sm btn-primary" onclick="bulkChangeRole()">Change Role</button>
                        <button class="btn btn-sm btn-secondary" onclick="bulkChangeStatus()">Change Status</button>
                        <button class="btn btn-sm btn-warning" onclick="bulkSendEmail()">Send Email</button>
                    </div>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Scripts -->
    <script src="../public/js/inline-editing.js"></script>
    <script>
        function filterUsers() {
            const role = document.getElementById('roleFilter').value;
            const status = document.getElementById('statusFilter').value;
            const url = new URL(window.location);
            
            if (role) url.searchParams.set('role', role);
            else url.searchParams.delete('role');
            
            if (status) url.searchParams.set('status', status);
            else url.searchParams.delete('status');
            
            url.searchParams.delete('page'); // Reset to first page
            window.location.href = url.toString();
        }
        
        function toggleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.user-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
            });
            
            updateBulkActionsBar();
        }
        
        function updateBulkActionsBar() {
            const selectedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
            const bulkActionsBar = document.getElementById('bulkActionsBar');
            const selectedCount = document.getElementById('selectedCount');
            
            if (selectedCheckboxes.length > 0) {
                bulkActionsBar.style.display = 'block';
                selectedCount.textContent = `${selectedCheckboxes.length} user${selectedCheckboxes.length > 1 ? 's' : ''} selected`;
            } else {
                bulkActionsBar.style.display = 'none';
            }
        }
        
        // Add event listeners to checkboxes
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.user-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', updateBulkActionsBar);
            });
        });
        
        function editUser(userId) {
            // Implement user editing modal
            alert('Edit user functionality - would open modal for user ID: ' + userId);
        }
        
        function resetPassword(userId) {
            if (confirm('Are you sure you want to reset this user\'s password?')) {
                // Implement password reset
                alert('Password reset functionality for user ID: ' + userId);
            }
        }
        
        function bulkChangeRole() {
            const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
            if (selected.length === 0) return;
            
            const newRole = prompt('Enter new role (administrator, pastor, mds, mentor, aspirant):');
            if (newRole) {
                // Implement bulk role change
                alert(`Bulk role change to ${newRole} for ${selected.length} users`);
            }
        }
        
        function bulkChangeStatus() {
            const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
            if (selected.length === 0) return;
            
            const newStatus = prompt('Enter new status (active, inactive, suspended):');
            if (newStatus) {
                // Implement bulk status change
                alert(`Bulk status change to ${newStatus} for ${selected.length} users`);
            }
        }
        
        function bulkSendEmail() {
            const selected = Array.from(document.querySelectorAll('.user-checkbox:checked')).map(cb => cb.value);
            if (selected.length === 0) return;
            
            // Implement bulk email functionality
            alert(`Send email to ${selected.length} selected users`);
        }
    </script>
    
    <style>
        .filters-bar {
            background: white;
            padding: var(--space-6);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm);
            border: 1px solid var(--gray-200);
            margin-bottom: var(--space-6);
            display: flex;
            align-items: center;
            gap: var(--space-4);
            flex-wrap: wrap;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }
        
        .filter-label {
            font-size: var(--text-sm);
            font-weight: 500;
            color: var(--gray-700);
            white-space: nowrap;
        }
        
        .user-avatar {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-full);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: var(--text-lg);
            font-weight: 600;
            color: white;
            flex-shrink: 0;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: var(--space-4);
        }
        
        .user-details h4 {
            margin: 0;
            font-size: var(--text-base);
            font-weight: 600;
            color: var(--gray-900);
        }
        
        .user-details p {
            margin: 0;
            font-size: var(--text-sm);
            color: var(--gray-500);
        }
        
        .action-menu {
            position: relative;
            display: inline-block;
        }
        
        .action-menu-button {
            background: none;
            border: none;
            padding: var(--space-2);
            border-radius: var(--radius-md);
            cursor: pointer;
            color: var(--gray-400);
            transition: all var(--transition-fast);
        }
        
        .action-menu-button:hover {
            background: var(--gray-100);
            color: var(--gray-600);
        }
        
        .bulk-actions-bar {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: var(--space-4) var(--space-6);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-xl);
            border: 1px solid var(--gray-200);
            z-index: 1000;
        }
    </style>
</body>
</html>
