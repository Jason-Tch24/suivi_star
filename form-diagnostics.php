<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Diagnostics - STAR System</title>
    <link rel="stylesheet" href="public/css/style.css">
    <style>
        .diagnostic-section {
            background: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }
        .success { border-left-color: #28a745; background: #d4edda; }
        .warning { border-left-color: #ffc107; background: #fff3cd; }
        .error { border-left-color: #dc3545; background: #f8d7da; }
        .code-block {
            background: #2d3748;
            color: #e2e8f0;
            padding: 15px;
            border-radius: 5px;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            overflow-x: auto;
            margin: 10px 0;
        }
        .test-button {
            background: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin: 5px;
        }
        .test-button:hover { background: #0056b3; }
        .test-button.success { background: #28a745; }
        .test-button.warning { background: #ffc107; color: #212529; }
        .test-button.error { background: #dc3545; }
    </style>
</head>
<body>
    <div class="container" style="padding: 2rem 0;">
        <h1>STAR System Form Diagnostics</h1>
        
        <!-- PHP Environment Check -->
        <div class="diagnostic-section">
            <h2>1. PHP Environment</h2>
            <p><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></p>
            <p><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></p>
            <p><strong>Document Root:</strong> <?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></p>
            <p><strong>Script Name:</strong> <?php echo $_SERVER['SCRIPT_NAME'] ?? 'Unknown'; ?></p>
            <p><strong>Request Method:</strong> <?php echo $_SERVER['REQUEST_METHOD'] ?? 'Unknown'; ?></p>
        </div>
        
        <!-- Form Processing Test -->
        <div class="diagnostic-section">
            <h2>2. Form Processing Test</h2>
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div class="success">
                    <h3>✅ POST Request Received</h3>
                    <p><strong>POST Data:</strong></p>
                    <div class="code-block">
                        <?php print_r($_POST); ?>
                    </div>
                    
                    <?php if (!empty($_FILES)): ?>
                        <p><strong>FILES Data:</strong></p>
                        <div class="code-block">
                            <?php print_r($_FILES); ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <p>Submit the form below to test POST processing:</p>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div style="margin: 10px 0;">
                    <label>Text Input:</label>
                    <input type="text" name="test_text" value="Test Value" style="width: 200px; padding: 5px;">
                </div>
                <div style="margin: 10px 0;">
                    <label>Email Input:</label>
                    <input type="email" name="test_email" value="test@example.com" style="width: 200px; padding: 5px;">
                </div>
                <div style="margin: 10px 0;">
                    <label>Password Input:</label>
                    <input type="password" name="test_password" value="testpass123" style="width: 200px; padding: 5px;">
                </div>
                <div style="margin: 10px 0;">
                    <label>Select:</label>
                    <select name="test_select" style="width: 200px; padding: 5px;">
                        <option value="option1">Option 1</option>
                        <option value="option2" selected>Option 2</option>
                    </select>
                </div>
                <div style="margin: 10px 0;">
                    <label>Textarea:</label><br>
                    <textarea name="test_textarea" style="width: 300px; height: 60px; padding: 5px;">Test textarea content</textarea>
                </div>
                <button type="submit" class="test-button">Test Form Submission</button>
            </form>
        </div>
        
        <!-- JavaScript Test -->
        <div class="diagnostic-section">
            <h2>3. JavaScript Functionality</h2>
            <button onclick="testJavaScript()" class="test-button">Test JavaScript</button>
            <div id="js-results"></div>
        </div>
        
        <!-- AJAX Test -->
        <div class="diagnostic-section">
            <h2>4. AJAX/Fetch Test</h2>
            <button onclick="testAjax()" class="test-button">Test AJAX</button>
            <div id="ajax-results"></div>
        </div>
        
        <!-- Form Validation Test -->
        <div class="diagnostic-section">
            <h2>5. HTML5 Form Validation</h2>
            <form id="validation-form" novalidate>
                <div style="margin: 10px 0;">
                    <label>Required Field:</label>
                    <input type="text" name="required_field" required style="width: 200px; padding: 5px;">
                </div>
                <div style="margin: 10px 0;">
                    <label>Email Field:</label>
                    <input type="email" name="email_field" style="width: 200px; padding: 5px;">
                </div>
                <div style="margin: 10px 0;">
                    <label>Min Length (8 chars):</label>
                    <input type="text" name="minlength_field" minlength="8" style="width: 200px; padding: 5px;">
                </div>
                <button type="submit" class="test-button">Test Validation</button>
            </form>
            <div id="validation-results"></div>
        </div>
        
        <!-- CSS Loading Test -->
        <div class="diagnostic-section">
            <h2>6. CSS Loading Test</h2>
            <button onclick="testCSS()" class="test-button">Test CSS Loading</button>
            <div id="css-results"></div>
        </div>
        
        <!-- Database Connection Test -->
        <div class="diagnostic-section">
            <h2>7. Database Connection</h2>
            <button onclick="testDatabase()" class="test-button">Test Database</button>
            <div id="db-results"></div>
        </div>
        
        <!-- Session Test -->
        <div class="diagnostic-section">
            <h2>8. Session Information</h2>
            <div class="code-block">
Session Status: <?php echo session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive'; ?>
Session ID: <?php echo session_id() ?: 'None'; ?>
Session Data:
<?php 
session_start();
print_r($_SESSION); 
?>
            </div>
        </div>
        
        <!-- Error Log -->
        <div class="diagnostic-section">
            <h2>9. Error Console</h2>
            <div id="error-console" class="code-block" style="min-height: 100px;">
                No errors logged yet...
            </div>
            <button onclick="clearErrorLog()" class="test-button warning">Clear Error Log</button>
        </div>
        
        <div style="text-align: center; margin: 30px 0;">
            <a href="index.php" class="test-button">← Back to Home</a>
            <a href="test-forms.php" class="test-button">Form Tests</a>
            <a href="test-login-flow.php" class="test-button">Login Flow Test</a>
        </div>
    </div>
    
    <script>
        // Error logging
        let errorLog = [];
        
        function logError(message, type = 'error') {
            const timestamp = new Date().toLocaleTimeString();
            errorLog.push(`[${timestamp}] ${type.toUpperCase()}: ${message}`);
            updateErrorConsole();
        }
        
        function updateErrorConsole() {
            const console = document.getElementById('error-console');
            if (errorLog.length === 0) {
                console.textContent = 'No errors logged yet...';
            } else {
                console.textContent = errorLog.join('\n');
            }
        }
        
        function clearErrorLog() {
            errorLog = [];
            updateErrorConsole();
        }
        
        // Capture JavaScript errors
        window.addEventListener('error', function(e) {
            logError(`${e.message} at ${e.filename}:${e.lineno}`, 'javascript');
        });
        
        // Test JavaScript functionality
        function testJavaScript() {
            const results = document.getElementById('js-results');
            results.innerHTML = '<p>Testing JavaScript...</p>';
            
            try {
                // Test basic operations
                const testArray = [1, 2, 3, 4, 5];
                const sum = testArray.reduce((a, b) => a + b, 0);
                
                // Test DOM manipulation
                const testDiv = document.createElement('div');
                testDiv.textContent = 'Test element created';
                
                // Test modern JavaScript features
                const testArrow = () => 'Arrow function works';
                const testTemplate = `Template literal works: ${sum}`;
                
                results.innerHTML = `
                    <div class="success">
                        <h4>✅ JavaScript Tests Passed</h4>
                        <p>Array sum: ${sum}</p>
                        <p>DOM element: ${testDiv.textContent}</p>
                        <p>Arrow function: ${testArrow()}</p>
                        <p>Template literal: ${testTemplate}</p>
                    </div>
                `;
                
                logError('JavaScript tests completed successfully', 'info');
                
            } catch (error) {
                results.innerHTML = `<div class="error"><h4>❌ JavaScript Error</h4><p>${error.message}</p></div>`;
                logError(`JavaScript test failed: ${error.message}`);
            }
        }
        
        // Test AJAX functionality
        async function testAjax() {
            const results = document.getElementById('ajax-results');
            results.innerHTML = '<p>Testing AJAX...</p>';
            
            try {
                // Test fetch API
                const response = await fetch('test-database.php');
                const text = await response.text();
                
                if (response.ok) {
                    results.innerHTML = `
                        <div class="success">
                            <h4>✅ AJAX Test Passed</h4>
                            <p>Status: ${response.status}</p>
                            <p>Response received: ${text.length} characters</p>
                        </div>
                    `;
                    logError('AJAX test completed successfully', 'info');
                } else {
                    throw new Error(`HTTP ${response.status}`);
                }
                
            } catch (error) {
                results.innerHTML = `<div class="error"><h4>❌ AJAX Error</h4><p>${error.message}</p></div>`;
                logError(`AJAX test failed: ${error.message}`);
            }
        }
        
        // Test CSS loading
        function testCSS() {
            const results = document.getElementById('css-results');
            results.innerHTML = '<p>Testing CSS...</p>';
            
            try {
                // Check if CSS is loaded by testing computed styles
                const testElement = document.createElement('div');
                testElement.className = 'btn btn-primary';
                document.body.appendChild(testElement);
                
                const styles = window.getComputedStyle(testElement);
                const hasStyles = styles.backgroundColor !== 'rgba(0, 0, 0, 0)' && styles.backgroundColor !== 'transparent';
                
                document.body.removeChild(testElement);
                
                if (hasStyles) {
                    results.innerHTML = `
                        <div class="success">
                            <h4>✅ CSS Loading Test Passed</h4>
                            <p>Button background color: ${styles.backgroundColor}</p>
                        </div>
                    `;
                    logError('CSS loading test completed successfully', 'info');
                } else {
                    results.innerHTML = `
                        <div class="warning">
                            <h4>⚠️ CSS May Not Be Fully Loaded</h4>
                            <p>Button styles not detected</p>
                        </div>
                    `;
                    logError('CSS loading test: styles not detected', 'warning');
                }
                
            } catch (error) {
                results.innerHTML = `<div class="error"><h4>❌ CSS Test Error</h4><p>${error.message}</p></div>`;
                logError(`CSS test failed: ${error.message}`);
            }
        }
        
        // Test database connection
        async function testDatabase() {
            const results = document.getElementById('db-results');
            results.innerHTML = '<p>Testing database...</p>';
            
            try {
                const response = await fetch('test-database.php');
                const text = await response.text();
                
                if (text.includes('Database connection successful')) {
                    results.innerHTML = `
                        <div class="success">
                            <h4>✅ Database Test Passed</h4>
                            <p>Connection successful</p>
                        </div>
                    `;
                    logError('Database test completed successfully', 'info');
                } else {
                    results.innerHTML = `
                        <div class="error">
                            <h4>❌ Database Test Failed</h4>
                            <p>Connection issues detected</p>
                        </div>
                    `;
                    logError('Database test failed: connection issues');
                }
                
            } catch (error) {
                results.innerHTML = `<div class="error"><h4>❌ Database Test Error</h4><p>${error.message}</p></div>`;
                logError(`Database test failed: ${error.message}`);
            }
        }
        
        // Form validation test
        document.getElementById('validation-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const results = document.getElementById('validation-results');
            const formData = new FormData(this);
            let errors = [];
            
            // Check required field
            if (!formData.get('required_field')) {
                errors.push('Required field is empty');
            }
            
            // Check email field
            const email = formData.get('email_field');
            if (email && !email.includes('@')) {
                errors.push('Invalid email format');
            }
            
            // Check min length
            const minLengthField = formData.get('minlength_field');
            if (minLengthField && minLengthField.length < 8) {
                errors.push('Min length field too short');
            }
            
            if (errors.length === 0) {
                results.innerHTML = `
                    <div class="success">
                        <h4>✅ Form Validation Passed</h4>
                        <p>All validation rules satisfied</p>
                    </div>
                `;
                logError('Form validation test passed', 'info');
            } else {
                results.innerHTML = `
                    <div class="error">
                        <h4>❌ Form Validation Errors</h4>
                        <ul>${errors.map(error => `<li>${error}</li>`).join('')}</ul>
                    </div>
                `;
                logError(`Form validation errors: ${errors.join(', ')}`);
            }
        });
        
        // Auto-run tests on page load
        document.addEventListener('DOMContentLoaded', function() {
            logError('Page loaded successfully', 'info');
            setTimeout(testJavaScript, 500);
        });
    </script>
</body>
</html>
