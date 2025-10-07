<?php
$appConfig = require __DIR__ . '/../config/app.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $appConfig['name']; ?></title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
    <div class="hero-section">
        <div class="container">
            <div class="hero-content">
                <h1>Welcome to STAR</h1>
                <h2>Volunteer Management System</h2>
                <p class="hero-description">
                    Join our church community as a STAR volunteer and make a difference. 
                    Track your journey through our structured 6-step program from application to active service.
                </p>
                
                <div class="hero-actions">
                    <a href="login.php" class="btn btn-primary">Login</a>
                    <a href="register.php" class="btn btn-secondary">Apply to Become a STAR</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="features-section">
        <div class="container">
            <h2>Your STAR Journey</h2>
            <div class="journey-steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h3>Application</h3>
                    <p>Complete your online registration and become an Aspirant STAR</p>
                </div>
                
                <div class="step">
                    <div class="step-number">2</div>
                    <h3>PCNC Training</h3>
                    <p>Complete our 6-month Pastoral Care and Nurture Course</p>
                </div>
                
                <div class="step">
                    <div class="step-number">3</div>
                    <h3>MDS Interview</h3>
                    <p>Interview with our Ministry of STAR team for validation</p>
                </div>
                
                <div class="step">
                    <div class="step-number">4</div>
                    <h3>Ministry Training</h3>
                    <p>One-month specialized training in your chosen ministry</p>
                </div>
                
                <div class="step">
                    <div class="step-number">5</div>
                    <h3>Mentor Report</h3>
                    <p>Your mentor provides feedback on your progress and readiness</p>
                </div>
                
                <div class="step">
                    <div class="step-number">6</div>
                    <h3>Active Service</h3>
                    <p>Begin your active role as a STAR volunteer in your ministry</p>
                </div>
            </div>
        </div>
    </div>
    
    <div class="info-section">
        <div class="container">
            <div class="info-grid">
                <div class="info-card">
                    <h3>For Aspirants</h3>
                    <p>Track your progress through the STAR journey, access training materials, and stay connected with your mentors.</p>
                    <ul>
                        <li>Personal timeline view</li>
                        <li>Download training materials</li>
                        <li>Track your progress</li>
                        <li>Receive notifications</li>
                    </ul>
                </div>
                
                <div class="info-card">
                    <h3>For Mentors</h3>
                    <p>Guide and support aspirants through their ministry training and provide valuable feedback.</p>
                    <ul>
                        <li>Track assigned aspirants</li>
                        <li>Submit progress reports</li>
                        <li>Access training resources</li>
                        <li>Communicate with administrators</li>
                    </ul>
                </div>
                
                <div class="info-card">
                    <h3>For Leadership</h3>
                    <p>Comprehensive oversight and management tools for the entire STAR program.</p>
                    <ul>
                        <li>Dashboard analytics</li>
                        <li>Progress monitoring</li>
                        <li>Report generation</li>
                        <li>System administration</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <div class="container">
            <p>&copy; 2025 <?php echo $appConfig['church']['name']; ?>. All rights reserved.</p>
            <p>STAR Volunteer Management System</p>
        </div>
    </footer>
</body>
</html>
