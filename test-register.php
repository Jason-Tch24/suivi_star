<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration Form Test - STAR System</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Registration Form Test</h1>
                <p>Testing all form elements and styling</p>
            </div>
            
            <!-- Test Success Alert -->
            <div class="alert alert-success">
                <strong>Success!</strong> This is how success messages will appear.
            </div>
            
            <!-- Test Error Alert -->
            <div class="alert alert-error">
                <strong>Error!</strong> This is how error messages will appear.
            </div>
            
            <form class="auth-form">
                <!-- Test Form Row -->
                <div class="form-row">
                    <div class="form-group">
                        <label for="test_first">First Name *</label>
                        <input type="text" id="test_first" placeholder="Enter first name" required>
                    </div>
                    <div class="form-group">
                        <label for="test_last">Last Name *</label>
                        <input type="text" id="test_last" placeholder="Enter last name" required>
                    </div>
                </div>
                
                <!-- Test Single Input -->
                <div class="form-group">
                    <label for="test_email">Email Address *</label>
                    <input type="email" id="test_email" placeholder="Enter email address" required>
                </div>
                
                <!-- Test Ministry Preferences Section -->
                <div class="ministry-preferences">
                    <h3>Ministry Preferences</h3>
                    <p>Please select up to 3 ministries you're interested in serving (in order of preference):</p>
                    
                    <div class="form-group">
                        <label for="test_ministry1">First Choice *</label>
                        <select id="test_ministry1" required>
                            <option value="">Select your first choice</option>
                            <option value="1">Worship Ministry</option>
                            <option value="2">Children's Ministry</option>
                            <option value="3">Youth Ministry</option>
                            <option value="4">Hospitality Ministry</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="test_ministry2">Second Choice</label>
                        <select id="test_ministry2">
                            <option value="">Select your second choice</option>
                            <option value="1">Worship Ministry</option>
                            <option value="2">Children's Ministry</option>
                            <option value="3">Youth Ministry</option>
                            <option value="4">Hospitality Ministry</option>
                        </select>
                    </div>
                </div>
                
                <!-- Test Textarea -->
                <div class="form-group">
                    <label for="test_motivation">Motivation</label>
                    <textarea id="test_motivation" rows="4" placeholder="Tell us about your motivation..."></textarea>
                </div>
                
                <!-- Test Button -->
                <button type="submit" class="btn btn-primary btn-full">Submit Test Form</button>
            </form>
            
            <!-- Test Auth Links -->
            <div class="auth-links">
                <p><a href="index.php">← Back to Home</a></p>
                <p>Already have an account? <a href="login.php">Sign in here</a></p>
            </div>
            
            <!-- Test Demo Credentials -->
            <div class="demo-credentials">
                <h4>Demo System Information</h4>
                <p class="demo-info">This is a demonstration of the registration form styling and functionality.</p>
                <div class="demo-grid">
                    <div class="demo-account">
                        <strong>Form Features</strong><br>
                        Enhanced input styling<br>
                        Responsive design
                    </div>
                    <div class="demo-account">
                        <strong>Validation</strong><br>
                        Real-time feedback<br>
                        Error handling
                    </div>
                    <div class="demo-account">
                        <strong>User Experience</strong><br>
                        Modern interface<br>
                        Mobile friendly
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
        }
    </style>
    
    <script>
        // Test form interactions
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.auth-form');
            const inputs = form.querySelectorAll('input, select, textarea');
            
            // Add focus/blur effects
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });
            });
            
            // Test form submission
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                alert('Form test completed! All styling is working correctly.');
            });
        });
    </script>
</body>
</html>
