<?php
/**
 * Aspirants Management Page - STAR Volunteer Management System
 */

require_once __DIR__ . '/../middleware/Auth.php';
require_once __DIR__ . '/../models/Aspirant.php';
require_once __DIR__ . '/../models/Ministry.php';
require_once __DIR__ . '/../helpers/AssetHelper.php';

Auth::requireAnyRole(['administrator', 'pastor', 'mds', 'mentor']);

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
    <link rel="stylesheet" href="<?php echo AssetHelper::asset('css/modern-design-system.css'); ?>">
    <link rel="stylesheet" href="<?php echo AssetHelper::asset('css/dashboard-override.css'); ?>">
    <link rel="stylesheet" href="<?php echo AssetHelper::asset('css/layout-fixes.css'); ?>">
    <link rel="stylesheet" href="<?php echo AssetHelper::asset('css/force-layout.css'); ?>">

    <style>
        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 10000 !important;
        }

        /* Force modal visibility when opened */
        #viewAspirantModal[style*="display: flex"],
        #editAspirantModal[style*="display: flex"],
        #deleteConfirmModal[style*="display: flex"] {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            position: fixed !important;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            max-width: 800px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-small {
            max-width: 400px;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 24px 24px 0 24px;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 24px;
        }

        .modal-title {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
            color: #111827;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #6b7280;
            padding: 4px;
            border-radius: 4px;
            transition: all 0.2s;
        }

        .modal-close:hover {
            background: #f3f4f6;
            color: #374151;
        }

        .modal-body {
            padding: 0 24px 24px 24px;
        }

        .modal-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 1px solid #e5e7eb;
        }

        .modal-actions-right {
            display: flex;
            gap: 12px;
        }

        /* Form Styles */
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 500;
            color: #374151;
            margin-bottom: 6px;
            font-size: 14px;
        }

        .form-input, .form-select, .form-textarea {
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }

        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-textarea {
            resize: vertical;
            min-height: 80px;
        }

        /* Detail View Styles */
        .aspirant-details {
            font-size: 14px;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-item.full-width {
            grid-column: 1 / -1;
        }

        .detail-item label {
            font-weight: 500;
            color: #6b7280;
            margin-bottom: 4px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .detail-value {
            color: #111827;
            font-weight: 500;
        }

        /* Button Styles */
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
        }

        .btn-secondary {
            background: #6b7280;
            color: white;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
        }

        .btn-outline {
            background: transparent;
            color: #374151;
            border-color: #d1d5db;
        }

        .btn-outline:hover {
            background: #f9fafb;
            border-color: #9ca3af;
        }

        /* Badge Styles */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .badge-primary {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-success {
            background: #dcfce7;
            color: #166534;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-secondary {
            background: #f3f4f6;
            color: #374151;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .form-grid, .detail-grid {
                grid-template-columns: 1fr;
            }

            .modal-content {
                width: 95%;
                margin: 20px;
            }

            .modal-actions {
                flex-direction: column;
                gap: 12px;
            }

            .modal-actions-right {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
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
                    <a href="<?php echo AssetHelper::url('/dashboard'); ?>" class="nav-item">
                        <span class="nav-icon">📊</span>
                        Dashboard
                    </a>
                </div>

                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="<?php echo AssetHelper::url('/aspirants'); ?>" class="nav-item active">
                        <span class="nav-icon">🌟</span>
                        Aspirants
                    </a>
                    <a href="<?php echo AssetHelper::url('/ministries'); ?>" class="nav-item">
                        <span class="nav-icon">⛪</span>
                        Ministries
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
                                                <button onclick="viewAspirant(<?php echo $aspirant['id']; ?>)" class="btn btn-sm btn-outline">View</button>
                                                <?php if (in_array($user['role'], ['administrator', 'pastor'])): ?>
                                                    <button onclick="editAspirant(<?php echo $aspirant['id']; ?>)" class="btn btn-sm btn-primary">Edit</button>
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

    <!-- View Aspirant Modal -->
    <div id="viewAspirantModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">👤 Aspirant Details</h2>
                <button class="modal-close" onclick="closeViewModal()">&times;</button>
            </div>
            <div class="modal-body" id="viewAspirantContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Edit Aspirant Modal -->
    <div id="editAspirantModal" class="modal-overlay" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">✏️ Edit Aspirant</h2>
                <button class="modal-close" onclick="closeEditModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="editAspirantForm">
                    <input type="hidden" id="editAspirantId" name="id">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="editFirstName">First Name</label>
                            <input type="text" id="editFirstName" name="first_name" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="editLastName">Last Name</label>
                            <input type="text" id="editLastName" name="last_name" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="editEmail">Email</label>
                            <input type="email" id="editEmail" name="email" class="form-input" required>
                        </div>

                        <div class="form-group">
                            <label for="editPhone">Phone</label>
                            <input type="tel" id="editPhone" name="phone" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="editStatus">Status</label>
                            <select id="editStatus" name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="completed">Completed</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="editCurrentStep">Current Step</label>
                            <select id="editCurrentStep" name="current_step" class="form-select">
                                <option value="1">Step 1: Application</option>
                                <option value="2">Step 2: PCNC Training</option>
                                <option value="3">Step 3: MDS Interview</option>
                                <option value="4">Step 4: Ministry Training</option>
                                <option value="5">Step 5: Mentor Report</option>
                                <option value="6">Step 6: Confirmation & Assignment</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="editMinistry">Assigned Ministry</label>
                            <select id="editMinistry" name="assigned_ministry_id" class="form-select">
                                <option value="">Not assigned</option>
                                <?php foreach ($ministries as $ministry): ?>
                                    <option value="<?php echo $ministry['id']; ?>">
                                        <?php echo htmlspecialchars($ministry['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="editNotes">Notes</label>
                            <textarea id="editNotes" name="notes" class="form-textarea" rows="3"></textarea>
                        </div>
                    </div>

                    <div class="modal-actions">
                        <button type="button" onclick="deleteAspirant()" class="btn btn-danger">🗑️ Delete</button>
                        <div class="modal-actions-right">
                            <button type="button" onclick="closeEditModal()" class="btn btn-secondary">Cancel</button>
                            <button type="submit" class="btn btn-primary">💾 Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="modal-overlay" style="display: none;">
        <div class="modal-content modal-small">
            <div class="modal-header">
                <h2 class="modal-title">⚠️ Confirm Deletion</h2>
                <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this aspirant? This action cannot be undone.</p>
                <p><strong id="deleteAspirantName"></strong></p>
            </div>
            <div class="modal-actions">
                <button type="button" onclick="closeDeleteModal()" class="btn btn-secondary">Cancel</button>
                <button type="button" onclick="confirmDelete()" class="btn btn-danger">🗑️ Delete Permanently</button>
            </div>
        </div>
    </div>

    <script>
        let currentAspirantId = null;

        // View Aspirant Modal Functions
        function viewAspirant(id) {
            currentAspirantId = id;
            document.getElementById('viewAspirantModal').style.display = 'flex';
            loadAspirantDetails(id);
        }

        function closeViewModal() {
            document.getElementById('viewAspirantModal').style.display = 'none';
        }

        // Edit Aspirant Modal Functions
        function editAspirant(id) {
            currentAspirantId = id;
            document.getElementById('editAspirantModal').style.display = 'flex';
            loadAspirantForEdit(id);
        }

        function closeEditModal() {
            document.getElementById('editAspirantModal').style.display = 'none';
        }

        // Delete Functions
        function deleteAspirant() {
            const aspirantName = document.getElementById('editFirstName').value + ' ' + document.getElementById('editLastName').value;
            document.getElementById('deleteAspirantName').textContent = aspirantName;
            document.getElementById('deleteConfirmModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteConfirmModal').style.display = 'none';
        }

        function confirmDelete() {
            if (currentAspirantId) {
                fetch('<?php echo AssetHelper::directUrl('api/aspirants.php'); ?>', {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: currentAspirantId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeDeleteModal();
                        closeEditModal();
                        location.reload(); // Refresh the page to show updated list
                    } else {
                        alert('Error deleting aspirant: ' + (data.message || 'Unknown error'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting aspirant');
                });
            }
        }

        // Load aspirant details for view modal
        function loadAspirantDetails(id) {
            fetch(`<?php echo AssetHelper::directUrl('api/aspirants.php'); ?>?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const aspirant = data.aspirant;
                        const content = `
                            <div class="aspirant-details">
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <label>Full Name</label>
                                        <div class="detail-value">${aspirant.first_name} ${aspirant.last_name}</div>
                                    </div>
                                    <div class="detail-item">
                                        <label>Email</label>
                                        <div class="detail-value">${aspirant.email}</div>
                                    </div>
                                    <div class="detail-item">
                                        <label>Phone</label>
                                        <div class="detail-value">${aspirant.phone || 'Not provided'}</div>
                                    </div>
                                    <div class="detail-item">
                                        <label>Current Step</label>
                                        <div class="detail-value">
                                            <span class="badge badge-primary">Step ${aspirant.current_step}</span>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <label>Status</label>
                                        <div class="detail-value">
                                            <span class="badge ${getStatusBadgeClass(aspirant.status)}">${aspirant.status.charAt(0).toUpperCase() + aspirant.status.slice(1)}</span>
                                        </div>
                                    </div>
                                    <div class="detail-item">
                                        <label>Assigned Ministry</label>
                                        <div class="detail-value">${aspirant.assigned_ministry_name || 'Not assigned'}</div>
                                    </div>
                                    <div class="detail-item">
                                        <label>Application Date</label>
                                        <div class="detail-value">${new Date(aspirant.application_date).toLocaleDateString()}</div>
                                    </div>
                                    <div class="detail-item">
                                        <label>Last Updated</label>
                                        <div class="detail-value">${new Date(aspirant.updated_at).toLocaleDateString()}</div>
                                    </div>
                                    ${aspirant.notes ? `
                                    <div class="detail-item full-width">
                                        <label>Notes</label>
                                        <div class="detail-value">${aspirant.notes}</div>
                                    </div>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                        document.getElementById('viewAspirantContent').innerHTML = content;
                    } else {
                        document.getElementById('viewAspirantContent').innerHTML = '<p>Error loading aspirant details.</p>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('viewAspirantContent').innerHTML = '<p>Error loading aspirant details.</p>';
                });
        }

        // Load aspirant data for edit modal
        function loadAspirantForEdit(id) {
            fetch(`<?php echo AssetHelper::directUrl('api/aspirants.php'); ?>?id=${id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const aspirant = data.aspirant;
                        document.getElementById('editAspirantId').value = aspirant.id;
                        document.getElementById('editFirstName').value = aspirant.first_name;
                        document.getElementById('editLastName').value = aspirant.last_name;
                        document.getElementById('editEmail').value = aspirant.email;
                        document.getElementById('editPhone').value = aspirant.phone || '';
                        document.getElementById('editStatus').value = aspirant.status;
                        document.getElementById('editCurrentStep').value = aspirant.current_step;
                        document.getElementById('editMinistry').value = aspirant.assigned_ministry_id || '';
                        document.getElementById('editNotes').value = aspirant.notes || '';
                    } else {
                        alert('Error loading aspirant data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading aspirant data');
                });
        }

        // Handle edit form submission
        document.getElementById('editAspirantForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const data = Object.fromEntries(formData);

            fetch('<?php echo AssetHelper::directUrl('api/aspirants.php'); ?>', {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closeEditModal();
                    location.reload(); // Refresh the page to show updated data
                } else {
                    alert('Error updating aspirant: ' + (data.message || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating aspirant');
            });
        });

        // Helper function for status badge classes
        function getStatusBadgeClass(status) {
            switch(status) {
                case 'active': return 'badge-success';
                case 'completed': return 'badge-primary';
                case 'suspended': return 'badge-warning';
                default: return 'badge-secondary';
            }
        }

        // Close modals when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('modal-overlay')) {
                if (e.target.id === 'viewAspirantModal') closeViewModal();
                if (e.target.id === 'editAspirantModal') closeEditModal();
                if (e.target.id === 'deleteConfirmModal') closeDeleteModal();
            }
        });
    </script>

    <?php include __DIR__ . '/partials/footer.php'; ?>
</body>
</html>
