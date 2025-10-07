<?php
/**
 * Multi-step User Creation Wizard - STAR System
 * Adaptive wizard that changes based on selected role
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

// Check if user has permission to create users
if (!Auth::hasEnhancedPermission('create_users')) {
    header('Location: ../dashboard.php');
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    try {
        $step = $_POST['step'] ?? 1;
        
        if ($step == 'create') {
            // Final step - create the user
            $userData = [
                'email' => $_POST['email'],
                'password' => $_POST['password'],
                'first_name' => $_POST['first_name'],
                'last_name' => $_POST['last_name'],
                'phone' => $_POST['phone'] ?? null,
                'role' => $_POST['role'],
                'status' => $_POST['status'] ?? 'active'
            ];
            
            // Extract role-specific data
            $roleData = [];
            $roleFields = $userController->getRoleSpecificFields($_POST['role']);
            
            foreach ($roleFields as $fieldName => $fieldConfig) {
                if (isset($_POST[$fieldName])) {
                    if ($fieldConfig['type'] === 'checkbox') {
                        $roleData[$fieldName] = json_encode($_POST[$fieldName]);
                    } else {
                        $roleData[$fieldName] = $_POST[$fieldName];
                    }
                }
            }
            
            $userId = $userController->createUserWithRoleData($userData, $roleData);
            
            echo json_encode([
                'success' => true,
                'message' => 'User created successfully',
                'user_id' => $userId,
                'redirect' => 'users.php'
            ]);
        } else {
            // Validation step
            echo json_encode(['success' => true, 'message' => 'Validation passed']);
        }
        
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
    exit;
}

$availableRoles = $userController->getAvailableRoles();
$appConfig = require __DIR__ . '/../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New User - <?php echo $appConfig['name']; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../public/css/modern-design-system.css">
    <style>
        .wizard-container {
            max-width: 800px;
            margin: 0 auto;
            padding: var(--space-8);
        }
        
        .wizard-header {
            text-align: center;
            margin-bottom: var(--space-8);
        }
        
        .wizard-progress {
            display: flex;
            justify-content: center;
            margin-bottom: var(--space-8);
        }
        
        .progress-step {
            width: 40px;
            height: 40px;
            border-radius: var(--radius-full);
            background: var(--gray-200);
            color: var(--gray-500);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            position: relative;
        }
        
        .progress-step.active {
            background: var(--primary-600);
            color: white;
        }
        
        .progress-step.completed {
            background: var(--status-active);
            color: white;
        }
        
        .progress-step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 60px;
            height: 2px;
            background: var(--gray-300);
            transform: translateY(-50%);
        }
        
        .progress-step.completed:not(:last-child)::after {
            background: var(--status-active);
        }
        
        .wizard-step {
            display: none;
            background: white;
            border-radius: var(--radius-2xl);
            padding: var(--space-8);
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--gray-200);
        }
        
        .wizard-step.active {
            display: block;
        }
        
        .step-title {
            font-size: var(--text-2xl);
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: var(--space-2);
        }
        
        .step-description {
            color: var(--gray-600);
            margin-bottom: var(--space-6);
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--space-6);
            margin-bottom: var(--space-6);
        }
        
        .form-grid .full-width {
            grid-column: 1 / -1;
        }
        
        .role-selection {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--space-4);
            margin: var(--space-6) 0;
        }
        
        .role-card {
            border: 2px solid var(--gray-200);
            border-radius: var(--radius-xl);
            padding: var(--space-6);
            text-align: center;
            cursor: pointer;
            transition: all var(--transition-fast);
            background: white;
        }
        
        .role-card:hover {
            border-color: var(--primary-300);
            box-shadow: var(--shadow-md);
        }
        
        .role-card.selected {
            border-color: var(--primary-600);
            background: var(--primary-50);
        }
        
        .role-icon {
            font-size: 3rem;
            margin-bottom: var(--space-3);
        }
        
        .role-title {
            font-weight: 600;
            color: var(--gray-900);
            margin-bottom: var(--space-2);
        }
        
        .role-description {
            font-size: var(--text-sm);
            color: var(--gray-600);
        }
        
        .wizard-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: var(--space-8);
            padding-top: var(--space-6);
            border-top: 1px solid var(--gray-200);
        }
        
        .role-specific-fields {
            display: none;
            margin-top: var(--space-6);
            padding-top: var(--space-6);
            border-top: 1px solid var(--gray-200);
        }
        
        .role-specific-fields.show {
            display: block;
        }
        
        .checkbox-group {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--space-3);
            margin-top: var(--space-3);
        }
        
        .checkbox-item {
            display: flex;
            align-items: center;
            gap: var(--space-2);
        }
        
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }
        
        .loading-spinner {
            background: white;
            padding: var(--space-8);
            border-radius: var(--radius-xl);
            text-align: center;
        }
        
        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--gray-200);
            border-top: 4px solid var(--primary-600);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto var(--space-4) auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
                    <div class="user-name"><?php echo htmlspecialchars($currentUser['first_name'] . ' ' . $currentUser['last_name']); ?></div>
                    <div class="user-role"><?php echo ucfirst($currentUser['role']); ?></div>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Administration</div>
                    <a href="users.php" class="nav-item">
                        <span class="nav-icon">👥</span>
                        User Management
                    </a>
                    <a href="user-wizard.php" class="nav-item active">
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
        <main class="main-content">
            <div class="wizard-container">
                <div class="wizard-header">
                    <h1>Create New User</h1>
                    <p class="text-muted">Follow the steps to create a new user account with role-specific information</p>
                </div>
                
                <!-- Progress Indicator -->
                <div class="wizard-progress">
                    <div class="progress-step active" data-step="1">1</div>
                    <div class="progress-step" data-step="2">2</div>
                    <div class="progress-step" data-step="3">3</div>
                    <div class="progress-step" data-step="4">4</div>
                </div>
                
                <form id="userWizardForm">
                    <!-- Step 1: Role Selection -->
                    <div class="wizard-step active" data-step="1">
                        <h2 class="step-title">Select User Role</h2>
                        <p class="step-description">Choose the role that best describes the user's responsibilities in the STAR system.</p>
                        
                        <div class="role-selection">
                            <?php foreach ($availableRoles as $roleKey => $roleName): ?>
                            <div class="role-card" data-role="<?php echo $roleKey; ?>">
                                <div class="role-icon">
                                    <?php
                                    $icons = [
                                        'administrator' => '👑',
                                        'pastor' => '⛪',
                                        'mds' => '👥',
                                        'mentor' => '🤝',
                                        'aspirant' => '🌟'
                                    ];
                                    echo $icons[$roleKey] ?? '👤';
                                    ?>
                                </div>
                                <div class="role-title"><?php echo $roleName; ?></div>
                                <div class="role-description">
                                    <?php
                                    $descriptions = [
                                        'administrator' => 'Full system access and user management',
                                        'pastor' => 'Oversight of all STAR processes',
                                        'mds' => 'Manage STAR process and assign mentors',
                                        'mentor' => 'Guide and support aspirants',
                                        'aspirant' => 'Complete STAR volunteer journey'
                                    ];
                                    echo $descriptions[$roleKey] ?? 'System user';
                                    ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <input type="hidden" id="selectedRole" name="role" required>
                    </div>
                    
                    <!-- Step 2: Basic Information -->
                    <div class="wizard-step" data-step="2">
                        <h2 class="step-title">Basic Information</h2>
                        <p class="step-description">Enter the user's basic contact and account information.</p>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">First Name *</label>
                                <input type="text" name="first_name" class="form-input" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Last Name *</label>
                                <input type="text" name="last_name" class="form-input" required>
                            </div>
                            
                            <div class="form-group full-width">
                                <label class="form-label">Email Address *</label>
                                <input type="email" name="email" class="form-input" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Phone Number</label>
                                <input type="tel" name="phone" class="form-input">
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Step 3: Role-Specific Information -->
                    <div class="wizard-step" data-step="3">
                        <h2 class="step-title">Role-Specific Information</h2>
                        <p class="step-description">Provide additional information specific to the selected role.</p>
                        
                        <div id="roleSpecificFields">
                            <!-- Dynamic content will be loaded here -->
                        </div>
                    </div>
                    
                    <!-- Step 4: Security & Review -->
                    <div class="wizard-step" data-step="4">
                        <h2 class="step-title">Security & Review</h2>
                        <p class="step-description">Set the user's password and review all information before creating the account.</p>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="form-label">Password *</label>
                                <input type="password" name="password" class="form-input" required minlength="8">
                                <div class="form-help">Minimum 8 characters</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">Confirm Password *</label>
                                <input type="password" name="confirm_password" class="form-input" required minlength="8">
                            </div>
                        </div>
                        
                        <div id="reviewSection" class="mt-6">
                            <!-- Review content will be populated here -->
                        </div>
                    </div>
                    
                    <!-- Wizard Actions -->
                    <div class="wizard-actions">
                        <button type="button" id="prevBtn" class="btn btn-secondary" style="display: none;">
                            ← Previous
                        </button>
                        
                        <div class="flex gap-4">
                            <a href="users.php" class="btn btn-outline">Cancel</a>
                            <button type="button" id="nextBtn" class="btn btn-primary">
                                Next →
                            </button>
                            <button type="submit" id="createBtn" class="btn btn-primary" style="display: none;">
                                Create User
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p>Creating user account...</p>
        </div>
    </div>
    
    <script>
        // User Creation Wizard JavaScript
        let currentStep = 1;
        const totalSteps = 4;
        let selectedRole = '';

        // Role-specific field configurations
        const roleFields = {
            pastor: {
                church_position: { type: 'text', label: 'Church Position', required: true },
                years_of_service: { type: 'number', label: 'Years of Service', required: false },
                oversight_areas: { type: 'textarea', label: 'Oversight Areas', required: false }
            },
            mds: {
                department: {
                    type: 'select',
                    label: 'Department',
                    required: true,
                    options: ['Administration', 'Training', 'Coordination', 'Assessment']
                },
                certification_level: {
                    type: 'select',
                    label: 'Certification Level',
                    required: true,
                    options: ['Basic', 'Intermediate', 'Advanced', 'Expert']
                },
                specialization_areas: {
                    type: 'checkbox',
                    label: 'Specialization Areas',
                    required: false,
                    options: ['Youth Ministry', 'Adult Ministry', 'Music Ministry', 'Outreach', 'Administration']
                }
            },
            mentor: {
                experience_level: {
                    type: 'select',
                    label: 'Experience Level',
                    required: true,
                    options: ['Beginner', 'Intermediate', 'Advanced', 'Expert']
                },
                mentoring_capacity: {
                    type: 'number',
                    label: 'Mentoring Capacity',
                    required: true,
                    min: 1,
                    max: 10
                },
                available_time_slots: {
                    type: 'checkbox',
                    label: 'Available Time Slots',
                    required: false,
                    options: [
                        'Monday Morning', 'Monday Evening', 'Tuesday Morning', 'Tuesday Evening',
                        'Wednesday Morning', 'Wednesday Evening', 'Thursday Morning', 'Thursday Evening',
                        'Friday Morning', 'Friday Evening', 'Saturday Morning', 'Saturday Evening',
                        'Sunday Morning', 'Sunday Evening'
                    ]
                }
            },
            aspirant: {
                ministry_preference_1: {
                    type: 'select',
                    label: 'First Ministry Choice',
                    required: true,
                    options: ['Youth Ministry', 'Adult Ministry', 'Music Ministry', 'Children Ministry', 'Outreach Ministry', 'Administration', 'Technical Ministry', 'Hospitality Ministry', 'Prayer Ministry', 'Counseling Ministry']
                },
                ministry_preference_2: {
                    type: 'select',
                    label: 'Second Ministry Choice',
                    required: false,
                    options: ['Youth Ministry', 'Adult Ministry', 'Music Ministry', 'Children Ministry', 'Outreach Ministry', 'Administration', 'Technical Ministry', 'Hospitality Ministry', 'Prayer Ministry', 'Counseling Ministry']
                },
                ministry_preference_3: {
                    type: 'select',
                    label: 'Third Ministry Choice',
                    required: false,
                    options: ['Youth Ministry', 'Adult Ministry', 'Music Ministry', 'Children Ministry', 'Outreach Ministry', 'Administration', 'Technical Ministry', 'Hospitality Ministry', 'Prayer Ministry', 'Counseling Ministry']
                },
                background_check_status: {
                    type: 'select',
                    label: 'Background Check Status',
                    required: false,
                    options: ['Not Started', 'In Progress', 'Completed', 'Approved', 'Rejected']
                },
                training_progress: {
                    type: 'number',
                    label: 'Training Progress (%)',
                    required: false,
                    min: 0,
                    max: 100
                }
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            initializeWizard();
        });

        function initializeWizard() {
            // Role selection handlers
            document.querySelectorAll('.role-card').forEach(card => {
                card.addEventListener('click', function() {
                    selectRole(this.dataset.role);
                });
            });

            // Navigation handlers
            document.getElementById('nextBtn').addEventListener('click', nextStep);
            document.getElementById('prevBtn').addEventListener('click', prevStep);
            document.getElementById('userWizardForm').addEventListener('submit', createUser);

            // Password confirmation validation
            const passwordField = document.querySelector('input[name="password"]');
            const confirmPasswordField = document.querySelector('input[name="confirm_password"]');

            if (confirmPasswordField) {
                confirmPasswordField.addEventListener('input', function() {
                    if (this.value !== passwordField.value) {
                        this.setCustomValidity('Passwords do not match');
                    } else {
                        this.setCustomValidity('');
                    }
                });
            }
        }

        function selectRole(role) {
            selectedRole = role;

            // Update UI
            document.querySelectorAll('.role-card').forEach(card => {
                card.classList.remove('selected');
            });
            document.querySelector(`[data-role="${role}"]`).classList.add('selected');

            // Set hidden field
            document.getElementById('selectedRole').value = role;

            // Generate role-specific fields for step 3
            generateRoleSpecificFields(role);
        }

        function generateRoleSpecificFields(role) {
            const container = document.getElementById('roleSpecificFields');
            const fields = roleFields[role] || {};

            let html = '';

            if (Object.keys(fields).length === 0) {
                html = '<p class="text-muted">No additional information required for this role.</p>';
            } else {
                html = '<div class="form-grid">';

                for (const [fieldName, config] of Object.entries(fields)) {
                    const required = config.required ? ' required' : '';
                    const requiredLabel = config.required ? ' *' : '';

                    if (config.type === 'select') {
                        html += `
                            <div class="form-group ${config.options && config.options.length > 6 ? 'full-width' : ''}">
                                <label class="form-label">${config.label}${requiredLabel}</label>
                                <select name="${fieldName}" class="form-select"${required}>
                                    <option value="">Select ${config.label}</option>
                                    ${config.options.map(option => `<option value="${option}">${option}</option>`).join('')}
                                </select>
                            </div>
                        `;
                    } else if (config.type === 'textarea') {
                        html += `
                            <div class="form-group full-width">
                                <label class="form-label">${config.label}${requiredLabel}</label>
                                <textarea name="${fieldName}" class="form-input" rows="3"${required}></textarea>
                            </div>
                        `;
                    } else if (config.type === 'checkbox') {
                        html += `
                            <div class="form-group full-width">
                                <label class="form-label">${config.label}${requiredLabel}</label>
                                <div class="checkbox-group">
                                    ${config.options.map(option => `
                                        <div class="checkbox-item">
                                            <input type="checkbox" name="${fieldName}[]" value="${option}" id="${fieldName}_${option.replace(/\s+/g, '_')}">
                                            <label for="${fieldName}_${option.replace(/\s+/g, '_')}">${option}</label>
                                        </div>
                                    `).join('')}
                                </div>
                            </div>
                        `;
                    } else {
                        const minMax = config.min !== undefined ? ` min="${config.min}"` : '';
                        const maxAttr = config.max !== undefined ? ` max="${config.max}"` : '';

                        html += `
                            <div class="form-group">
                                <label class="form-label">${config.label}${requiredLabel}</label>
                                <input type="${config.type}" name="${fieldName}" class="form-input"${required}${minMax}${maxAttr}>
                            </div>
                        `;
                    }
                }

                html += '</div>';
            }

            container.innerHTML = html;
        }

        function nextStep() {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateWizardDisplay();

                    if (currentStep === 4) {
                        generateReviewSection();
                    }
                }
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                updateWizardDisplay();
            }
        }

        function validateCurrentStep() {
            const currentStepElement = document.querySelector(`.wizard-step[data-step="${currentStep}"]`);
            const requiredFields = currentStepElement.querySelectorAll('[required]');

            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    isValid = false;
                } else {
                    field.classList.remove('error');
                }
            });

            // Special validation for step 1 (role selection)
            if (currentStep === 1 && !selectedRole) {
                alert('Please select a role for the user.');
                return false;
            }

            if (!isValid) {
                alert('Please fill in all required fields.');
            }

            return isValid;
        }

        function updateWizardDisplay() {
            // Update progress indicators
            document.querySelectorAll('.progress-step').forEach((step, index) => {
                const stepNumber = index + 1;
                step.classList.remove('active', 'completed');

                if (stepNumber < currentStep) {
                    step.classList.add('completed');
                    step.innerHTML = '✓';
                } else if (stepNumber === currentStep) {
                    step.classList.add('active');
                    step.innerHTML = stepNumber;
                } else {
                    step.innerHTML = stepNumber;
                }
            });

            // Show/hide wizard steps
            document.querySelectorAll('.wizard-step').forEach(step => {
                step.classList.remove('active');
            });
            document.querySelector(`.wizard-step[data-step="${currentStep}"]`).classList.add('active');

            // Update navigation buttons
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            const createBtn = document.getElementById('createBtn');

            prevBtn.style.display = currentStep > 1 ? 'block' : 'none';

            if (currentStep === totalSteps) {
                nextBtn.style.display = 'none';
                createBtn.style.display = 'block';
            } else {
                nextBtn.style.display = 'block';
                createBtn.style.display = 'none';
            }
        }

        function generateReviewSection() {
            const form = document.getElementById('userWizardForm');
            const formData = new FormData(form);
            const reviewContainer = document.getElementById('reviewSection');

            let html = '<div class="card"><div class="card-header"><h3 class="card-title">Review User Information</h3></div><div class="card-body">';

            // Basic information
            html += '<h4>Basic Information</h4>';
            html += '<div class="form-grid">';
            html += `<div><strong>Name:</strong> ${formData.get('first_name')} ${formData.get('last_name')}</div>`;
            html += `<div><strong>Email:</strong> ${formData.get('email')}</div>`;
            html += `<div><strong>Phone:</strong> ${formData.get('phone') || 'Not provided'}</div>`;
            html += `<div><strong>Role:</strong> ${selectedRole}</div>`;
            html += `<div><strong>Status:</strong> ${formData.get('status')}</div>`;
            html += '</div>';

            // Role-specific information
            const fields = roleFields[selectedRole] || {};
            if (Object.keys(fields).length > 0) {
                html += '<h4 style="margin-top: var(--space-6);">Role-Specific Information</h4>';
                html += '<div class="form-grid">';

                for (const [fieldName, config] of Object.entries(fields)) {
                    const value = formData.get(fieldName) || formData.getAll(fieldName + '[]').join(', ') || 'Not provided';
                    html += `<div><strong>${config.label}:</strong> ${value}</div>`;
                }

                html += '</div>';
            }

            html += '</div></div>';
            reviewContainer.innerHTML = html;
        }

        function createUser(e) {
            e.preventDefault();

            if (!validateCurrentStep()) {
                return;
            }

            const formData = new FormData(e.target);
            formData.append('step', 'create');

            // Show loading overlay
            document.getElementById('loadingOverlay').style.display = 'flex';

            fetch('', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                document.getElementById('loadingOverlay').style.display = 'none';

                if (data.success) {
                    alert(data.message);
                    if (data.redirect) {
                        window.location.href = data.redirect;
                    }
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                document.getElementById('loadingOverlay').style.display = 'none';
                alert('Error: ' + error.message);
            });
        }
    </script>
</body>
</html>
