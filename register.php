<?php
/**
 * Registration Page - STAR Volunteer Management System
 */

require_once __DIR__ . '/src/middleware/Auth.php';
require_once __DIR__ . '/src/models/User.php';
require_once __DIR__ . '/src/models/Aspirant.php';
require_once __DIR__ . '/src/models/Ministry.php';

// Redirect if already logged in
if (Auth::check()) {
    header('Location: ' . Auth::getDashboardUrl());
    exit;
}

$userModel = new User();
$aspirantModel = new Aspirant();
$ministryModel = new Ministry();

$appConfig = require __DIR__ . '/config/app.php';
$error = '';
$success = '';

// Get available ministries
$ministries = $ministryModel->getAll('active');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $ministryPref1 = $_POST['ministry_preference_1'] ?? '';
    $ministryPref2 = $_POST['ministry_preference_2'] ?? '';
    $ministryPref3 = $_POST['ministry_preference_3'] ?? '';
    $motivation = trim($_POST['motivation'] ?? '');

    // Validation
    if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } elseif (!$userModel->isEmailUnique($email)) {
        $error = 'An account with this email address already exists.';
    } elseif (empty($ministryPref1)) {
        $error = 'Please select at least your first ministry preference.';
    } else {
        try {
            // Create user account
            $userId = $userModel->create([
                'email' => $email,
                'password' => $password,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'phone' => $phone,
                'role' => 'aspirant',
                'status' => 'active'
            ]);

            // Create aspirant record
            $aspirantData = [
                'user_id' => $userId,
                'application_date' => date('Y-m-d'),
                'current_step' => 1,
                'ministry_preference_1' => $ministryPref1 ?: null,
                'ministry_preference_2' => $ministryPref2 ?: null,
                'ministry_preference_3' => $ministryPref3 ?: null,
                'status' => 'active',
                'notes' => $motivation
            ];

            $aspirantId = $aspirantModel->create($aspirantData);

            // Initialize first step progress
            $aspirantModel->updateStepProgress($aspirantId, 1, [
                'status' => 'in_progress',
                'started_at' => date('Y-m-d H:i:s')
            ]);

            $success = 'Your application has been submitted successfully! You can now log in to track your STAR journey.';

        } catch (Exception $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply to Become a STAR - <?php echo $appConfig['name']; ?></title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Become a STAR Volunteer</h1>
                <p>Join our church community and make a difference</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                    <p style="margin-top: 1rem;">
                        <a href="login.php" class="btn btn-primary">Login Now</a>
                    </p>
                </div>
            <?php else: ?>
                <form method="POST" class="auth-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name *</label>
                            <input
                                type="text"
                                id="first_name"
                                name="first_name"
                                value="<?php echo htmlspecialchars($_POST['first_name'] ?? ''); ?>"
                                required
                                placeholder="Enter your first name"
                            >
                        </div>

                        <div class="form-group">
                            <label for="last_name">Last Name *</label>
                            <input
                                type="text"
                                id="last_name"
                                name="last_name"
                                value="<?php echo htmlspecialchars($_POST['last_name'] ?? ''); ?>"
                                required
                                placeholder="Enter your last name"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                            required
                            placeholder="Enter your email address"
                        >
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input
                            type="tel"
                            id="phone"
                            name="phone"
                            value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                            placeholder="Enter your phone number"
                        >
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password *</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                minlength="8"
                                placeholder="Create a password (min 8 characters)"
                            >
                        </div>

                        <div class="form-group">
                            <label for="confirm_password">Confirm Password *</label>
                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                required
                                placeholder="Confirm your password"
                            >
                        </div>
                    </div>

                    <div class="ministry-preferences">
                        <h3>Ministry Preferences</h3>
                        <p>Please select up to 3 ministries you're interested in serving (in order of preference):</p>

                        <div class="form-group">
                            <label for="ministry_preference_1">First Choice *</label>
                            <select id="ministry_preference_1" name="ministry_preference_1" required>
                                <option value="">Select your first choice</option>
                                <?php foreach ($ministries as $ministry): ?>
                                    <option value="<?php echo $ministry['id']; ?>"
                                            <?php echo ($_POST['ministry_preference_1'] ?? '') == $ministry['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ministry['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="ministry_preference_2">Second Choice</label>
                            <select id="ministry_preference_2" name="ministry_preference_2">
                                <option value="">Select your second choice</option>
                                <?php foreach ($ministries as $ministry): ?>
                                    <option value="<?php echo $ministry['id']; ?>"
                                            <?php echo ($_POST['ministry_preference_2'] ?? '') == $ministry['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ministry['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="ministry_preference_3">Third Choice</label>
                            <select id="ministry_preference_3" name="ministry_preference_3">
                                <option value="">Select your third choice</option>
                                <?php foreach ($ministries as $ministry): ?>
                                    <option value="<?php echo $ministry['id']; ?>"
                                            <?php echo ($_POST['ministry_preference_3'] ?? '') == $ministry['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($ministry['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="motivation">Why do you want to become a STAR volunteer?</label>
                        <textarea
                            id="motivation"
                            name="motivation"
                            rows="4"
                            placeholder="Tell us about your motivation to serve..."
                        ><?php echo htmlspecialchars($_POST['motivation'] ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">Submit Application</button>
                </form>
            <?php endif; ?>

            <div class="auth-links">
                <p><a href="index.php">← Back to Home</a></p>
                <p>Already have an account? <a href="login.php">Sign in here</a></p>
            </div>

            <div class="demo-credentials">
                <h4>Demo System Information</h4>
                <p class="demo-info">This is a demonstration system. After registration, you can explore the STAR volunteer journey process.</p>
                <div class="demo-grid">
                    <div class="demo-account">
                        <strong>Sample Journey</strong><br>
                        Complete the 6-step STAR process<br>
                        Track your progress
                    </div>
                    <div class="demo-account">
                        <strong>Ministry Options</strong><br>
                        Choose from available ministries<br>
                        Get matched with mentors
                    </div>
                    <div class="demo-account">
                        <strong>Professional Growth</strong><br>
                        Develop leadership skills<br>
                        Serve your community
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Enhanced Registration Form Styles */
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-lg, 1.5rem);
            margin-bottom: var(--spacing-lg, 1.5rem);
        }

        .ministry-preferences {
            background: linear-gradient(135deg, var(--primary-light, #dbeafe) 0%, white 100%);
            padding: var(--spacing-xl, 2rem);
            border-radius: var(--radius-lg, 0.75rem);
            margin: var(--spacing-xl, 2rem) 0;
            border: 1px solid var(--gray-200, #e2e8f0);
            box-shadow: var(--shadow-sm, 0 1px 2px 0 rgba(0, 0, 0, 0.05));
        }

        .ministry-preferences h3 {
            color: var(--gray-900, #0f172a);
            margin-bottom: var(--spacing-sm, 0.5rem);
            font-weight: 700;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: var(--spacing-sm, 0.5rem);
        }

        .ministry-preferences h3::before {
            content: '🏛️';
            font-size: 1.5rem;
        }

        .ministry-preferences p {
            color: var(--gray-600, #475569);
            margin-bottom: var(--spacing-lg, 1.5rem);
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .demo-credentials {
            background: linear-gradient(135deg, var(--gray-100, #f1f5f9) 0%, white 100%);
            padding: var(--spacing-xl, 2rem);
            border-radius: var(--radius-lg, 0.75rem);
            margin-top: var(--spacing-xl, 2rem);
            border: 1px solid var(--gray-200, #e2e8f0);
        }

        .demo-credentials h4 {
            color: var(--gray-900, #0f172a);
            margin-bottom: var(--spacing-md, 1rem);
            font-weight: 600;
            font-size: 1.125rem;
            text-align: center;
        }

        .demo-info {
            color: var(--gray-600, #475569);
            text-align: center;
            margin-bottom: var(--spacing-lg, 1.5rem);
            font-size: 0.9rem;
            line-height: 1.6;
        }

        .demo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: var(--spacing-lg, 1.5rem);
        }

        .demo-account {
            background: white;
            padding: var(--spacing-lg, 1.5rem);
            border-radius: var(--radius-md, 0.5rem);
            text-align: center;
            font-size: 0.85rem;
            line-height: 1.5;
            border: 1px solid var(--gray-200, #e2e8f0);
            box-shadow: var(--shadow-sm, 0 1px 2px 0 rgba(0, 0, 0, 0.05));
            transition: all 0.2s ease;
        }

        .demo-account:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md, 0 4px 6px -1px rgba(0, 0, 0, 0.1));
        }

        .demo-account strong {
            color: var(--primary-color, #2563eb);
            display: block;
            margin-bottom: var(--spacing-xs, 0.25rem);
            font-weight: 600;
        }

        /* Enhanced form styling */
        .auth-form .form-group {
            margin-bottom: var(--spacing-lg, 1.5rem);
        }

        .auth-form input,
        .auth-form select,
        .auth-form textarea {
            transition: all 0.2s ease;
            border: 2px solid var(--gray-300, #cbd5e1);
        }

        .auth-form input:focus,
        .auth-form select:focus,
        .auth-form textarea:focus {
            border-color: var(--primary-color, #2563eb);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
            transform: translateY(-1px);
        }

        .auth-form label {
            font-weight: 500;
            color: var(--gray-900, #0f172a);
            margin-bottom: var(--spacing-sm, 0.5rem);
        }

        /* Fix scrolling issues */
        .auth-page {
            align-items: flex-start !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding-top: 2rem !important;
            padding-bottom: 2rem !important;
        }

        .auth-container {
            margin: 1rem auto !important;
            max-width: 32rem !important;
        }

        .auth-card {
            overflow: visible !important;
            max-height: none !important;
        }

        .auth-form {
            max-height: none !important;
            overflow: visible !important;
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: var(--spacing-md, 1rem);
            }

            .ministry-preferences {
                padding: var(--spacing-lg, 1.5rem);
                margin: var(--spacing-lg, 1.5rem) 0;
            }

            .demo-grid {
                grid-template-columns: 1fr;
                gap: var(--spacing-md, 1rem);
            }

            .demo-credentials {
                padding: var(--spacing-lg, 1.5rem);
            }

            .auth-container {
                max-width: 90% !important;
                margin: 1rem auto !important;
            }
        }

        @media (max-width: 480px) {
            .auth-card {
                margin: var(--spacing-sm, 0.5rem);
            }

            .ministry-preferences {
                padding: var(--spacing-md, 1rem);
            }

            .demo-credentials {
                padding: var(--spacing-md, 1rem);
            }

            .auth-page {
                padding: 1rem !important;
            }
        }

        @media (max-height: 800px) {
            .auth-page {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }
        }

        @media (max-height: 600px) {
            .ministry-preferences {
                padding: var(--spacing-md, 1rem);
                margin: var(--spacing-md, 1rem) 0;
            }

            .demo-credentials {
                padding: var(--spacing-md, 1rem);
                margin-top: var(--spacing-md, 1rem);
            }
        }
    </style>
</body>
</html>
