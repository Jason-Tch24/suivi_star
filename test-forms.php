<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Testing - STAR System</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/dashboard.css">
</head>
<body>
    <div class="container" style="padding: 2rem 0;">
        <h1 class="text-center mb-5">STAR System Form Testing</h1>
        
        <!-- Registration Form Test -->
        <section class="section-card mb-5">
            <h2>1. Registration Form Test</h2>
            <div id="registration-test">
                <button onclick="testRegistration()" class="btn btn-primary">Test Registration Form</button>
                <div id="registration-result" class="mt-3"></div>
            </div>
        </section>
        
        <!-- Login Form Test -->
        <section class="section-card mb-5">
            <h2>2. Login Form Test</h2>
            <div id="login-test">
                <button onclick="testLogin()" class="btn btn-success">Test Login Form</button>
                <div id="login-result" class="mt-3"></div>
            </div>
        </section>
        
        <!-- Dashboard Access Test -->
        <section class="section-card mb-5">
            <h2>3. Dashboard Access Test</h2>
            <div id="dashboard-test">
                <button onclick="testDashboard()" class="btn btn-info">Test Dashboard Access</button>
                <div id="dashboard-result" class="mt-3"></div>
            </div>
        </section>
        
        <!-- Form Validation Test -->
        <section class="section-card mb-5">
            <h2>4. Form Validation Test</h2>
            <form id="validation-test-form" class="auth-form">
                <div class="form-group">
                    <label for="test-email">Email (required)</label>
                    <input type="email" id="test-email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="test-password">Password (min 8 chars)</label>
                    <input type="password" id="test-password" name="password" minlength="8" required>
                </div>
                
                <div class="form-group">
                    <label for="test-confirm">Confirm Password</label>
                    <input type="password" id="test-confirm" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Test Validation</button>
            </form>
            <div id="validation-result" class="mt-3"></div>
        </section>
        
        <!-- JavaScript Test -->
        <section class="section-card mb-5">
            <h2>5. JavaScript Functionality Test</h2>
            <div id="js-test">
                <button onclick="testJavaScript()" class="btn btn-warning">Test JavaScript</button>
                <div id="js-result" class="mt-3"></div>
            </div>
        </section>
        
        <!-- Database Connection Test -->
        <section class="section-card mb-5">
            <h2>6. Database Connection Test</h2>
            <div id="db-test">
                <button onclick="testDatabase()" class="btn btn-secondary">Test Database</button>
                <div id="db-result" class="mt-3"></div>
            </div>
        </section>
        
        <div class="text-center">
            <a href="index.php" class="btn btn-outline">← Back to Home</a>
            <a href="login.php" class="btn btn-primary">Go to Login</a>
            <a href="register.php" class="btn btn-success">Go to Register</a>
        </div>
    </div>
    
    <script>
        // Test Registration Form
        async function testRegistration() {
            const resultDiv = document.getElementById('registration-result');
            resultDiv.innerHTML = '<div class="alert alert-info">Testing registration...</div>';
            
            try {
                const response = await fetch('register.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        first_name: 'Test',
                        last_name: 'User',
                        email: `test_${Date.now()}@example.com`,
                        password: 'testpass123',
                        confirm_password: 'testpass123',
                        ministry_preference_1: '1',
                        motivation: 'Testing form functionality'
                    })
                });
                
                const text = await response.text();
                
                if (text.includes('success') || text.includes('submitted successfully')) {
                    resultDiv.innerHTML = '<div class="alert alert-success">✅ Registration form working correctly!</div>';
                } else if (text.includes('error') || text.includes('failed')) {
                    resultDiv.innerHTML = '<div class="alert alert-error">❌ Registration form has errors</div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-warning">⚠️ Registration form response unclear</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="alert alert-error">❌ Registration test failed: ${error.message}</div>`;
            }
        }
        
        // Test Login Form
        async function testLogin() {
            const resultDiv = document.getElementById('login-result');
            resultDiv.innerHTML = '<div class="alert alert-info">Testing login...</div>';
            
            try {
                const response = await fetch('login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: new URLSearchParams({
                        email: 'admin@star-church.org',
                        password: 'password123'
                    })
                });
                
                if (response.redirected || response.status === 302) {
                    resultDiv.innerHTML = '<div class="alert alert-success">✅ Login form working correctly (redirected to dashboard)!</div>';
                } else {
                    const text = await response.text();
                    if (text.includes('Invalid email or password')) {
                        resultDiv.innerHTML = '<div class="alert alert-warning">⚠️ Login validation working (invalid credentials)</div>';
                    } else {
                        resultDiv.innerHTML = '<div class="alert alert-error">❌ Login form may have issues</div>';
                    }
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="alert alert-error">❌ Login test failed: ${error.message}</div>`;
            }
        }
        
        // Test Dashboard Access
        async function testDashboard() {
            const resultDiv = document.getElementById('dashboard-result');
            resultDiv.innerHTML = '<div class="alert alert-info">Testing dashboard access...</div>';
            
            try {
                const response = await fetch('dashboard.php');
                const text = await response.text();
                
                if (text.includes('Dashboard') && text.includes('STAR')) {
                    resultDiv.innerHTML = '<div class="alert alert-success">✅ Dashboard accessible and loading correctly!</div>';
                } else if (text.includes('login') || response.status === 401) {
                    resultDiv.innerHTML = '<div class="alert alert-info">ℹ️ Dashboard requires authentication (working correctly)</div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-warning">⚠️ Dashboard response unclear</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="alert alert-error">❌ Dashboard test failed: ${error.message}</div>`;
            }
        }
        
        // Test JavaScript Functionality
        function testJavaScript() {
            const resultDiv = document.getElementById('js-result');
            
            try {
                // Test basic JavaScript functionality
                const testArray = [1, 2, 3, 4, 5];
                const sum = testArray.reduce((a, b) => a + b, 0);
                
                // Test DOM manipulation
                const testElement = document.createElement('div');
                testElement.textContent = 'Test element';
                
                // Test fetch API availability
                const hasFetch = typeof fetch !== 'undefined';
                
                // Test local storage
                const hasLocalStorage = typeof localStorage !== 'undefined';
                
                resultDiv.innerHTML = `
                    <div class="alert alert-success">
                        ✅ JavaScript working correctly!<br>
                        - Array operations: ${sum === 15 ? 'OK' : 'FAIL'}<br>
                        - DOM manipulation: ${testElement ? 'OK' : 'FAIL'}<br>
                        - Fetch API: ${hasFetch ? 'Available' : 'Not available'}<br>
                        - Local Storage: ${hasLocalStorage ? 'Available' : 'Not available'}
                    </div>
                `;
            } catch (error) {
                resultDiv.innerHTML = `<div class="alert alert-error">❌ JavaScript test failed: ${error.message}</div>`;
            }
        }
        
        // Test Database Connection
        async function testDatabase() {
            const resultDiv = document.getElementById('db-result');
            resultDiv.innerHTML = '<div class="alert alert-info">Testing database connection...</div>';
            
            try {
                const response = await fetch('test-database.php');
                const text = await response.text();
                
                if (text.includes('Database connection successful')) {
                    resultDiv.innerHTML = '<div class="alert alert-success">✅ Database connection working correctly!</div>';
                } else {
                    resultDiv.innerHTML = '<div class="alert alert-error">❌ Database connection issues detected</div>';
                }
            } catch (error) {
                resultDiv.innerHTML = `<div class="alert alert-error">❌ Database test failed: ${error.message}</div>`;
            }
        }
        
        // Form Validation Test
        document.getElementById('validation-test-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('test-email').value;
            const password = document.getElementById('test-password').value;
            const confirm = document.getElementById('test-confirm').value;
            const resultDiv = document.getElementById('validation-result');
            
            let errors = [];
            
            // Email validation
            if (!email || !email.includes('@')) {
                errors.push('Invalid email address');
            }
            
            // Password validation
            if (!password || password.length < 8) {
                errors.push('Password must be at least 8 characters');
            }
            
            // Confirm password validation
            if (password !== confirm) {
                errors.push('Passwords do not match');
            }
            
            if (errors.length === 0) {
                resultDiv.innerHTML = '<div class="alert alert-success">✅ Form validation working correctly!</div>';
            } else {
                resultDiv.innerHTML = `<div class="alert alert-error">❌ Validation errors: ${errors.join(', ')}</div>`;
            }
        });
        
        // Auto-run JavaScript test on page load
        document.addEventListener('DOMContentLoaded', function() {
            testJavaScript();
        });
    </script>
</body>
</html>
