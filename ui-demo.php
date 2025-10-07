<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STAR UI Components Demo</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/dashboard.css">
</head>
<body>
    <div class="container" style="padding: 2rem 0;">
        <h1 class="text-center mb-5">STAR UI Components Demo</h1>
        
        <!-- Button Showcase -->
        <section class="section-card mb-5">
            <h2>Enhanced Buttons</h2>
            <div class="flex flex-wrap" style="gap: 1rem; margin-bottom: 2rem;">
                <button class="btn btn-primary">Primary Button</button>
                <button class="btn btn-secondary">Secondary Button</button>
                <button class="btn btn-success">Success Button</button>
                <button class="btn btn-warning">Warning Button</button>
                <button class="btn btn-danger">Danger Button</button>
                <button class="btn btn-outline">Outline Button</button>
                <button class="btn btn-ghost">Ghost Button</button>
            </div>
            
            <div class="flex flex-wrap" style="gap: 1rem;">
                <button class="btn btn-primary btn-sm">Small</button>
                <button class="btn btn-primary">Regular</button>
                <button class="btn btn-primary btn-lg">Large</button>
            </div>
        </section>
        
        <!-- Statistics Cards -->
        <section class="section-card mb-5">
            <h2>Statistics Cards</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">127</div>
                    <div class="stat-label">Total Aspirants</div>
                    <div class="stat-trend">All time</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">45</div>
                    <div class="stat-label">Active Journey</div>
                    <div class="stat-trend">Currently progressing</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">82</div>
                    <div class="stat-label">Completed</div>
                    <div class="stat-trend">Active volunteers</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">12</div>
                    <div class="stat-label">Active Ministries</div>
                    <div class="stat-trend">Departments</div>
                </div>
            </div>
        </section>
        
        <!-- Timeline Demo -->
        <section class="section-card mb-5">
            <h2>Enhanced Timeline</h2>
            <div class="journey-timeline">
                <div class="timeline">
                    <div class="timeline-item completed">
                        <div class="timeline-marker">1</div>
                        <div class="timeline-content">
                            <h3>Application Completed</h3>
                            <p>Successfully submitted application and became an Aspirant STAR</p>
                            <div class="step-meta">
                                <span class="meta-item">Completed: Mar 15, 2025</span>
                                <span class="meta-item">Validator: Admin Team</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item in_progress">
                        <div class="timeline-marker">2</div>
                        <div class="timeline-content">
                            <h3>PCNC Training</h3>
                            <p>Currently enrolled in 6-month Pastoral Care and Nurture Course</p>
                            <div class="step-meta">
                                <span class="meta-item">Started: Mar 20, 2025</span>
                                <span class="meta-item">Progress: 60%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="timeline-item pending">
                        <div class="timeline-marker">3</div>
                        <div class="timeline-content">
                            <h3>MDS Interview</h3>
                            <p>Awaiting interview scheduling with Ministry of STAR team</p>
                            <div class="step-meta">
                                <span class="meta-item">Expected: Sep 2025</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Alert Examples -->
        <section class="section-card mb-5">
            <h2>Enhanced Alerts</h2>
            <div class="alert alert-success mb-3">
                <strong>Success!</strong> Your application has been submitted successfully.
            </div>
            <div class="alert alert-info mb-3">
                <strong>Information:</strong> Your next training session is scheduled for tomorrow.
            </div>
            <div class="alert alert-warning mb-3">
                <strong>Warning:</strong> Your PCNC training deadline is approaching.
            </div>
            <div class="alert alert-error mb-3">
                <strong>Error:</strong> There was an issue processing your request.
            </div>
        </section>
        
        <!-- Form Elements -->
        <section class="section-card mb-5">
            <h2>Enhanced Form Elements</h2>
            <form>
                <div class="form-group">
                    <label for="demo-input">Text Input</label>
                    <input type="text" id="demo-input" placeholder="Enter your text here">
                </div>
                
                <div class="form-group">
                    <label for="demo-select">Select Dropdown</label>
                    <select id="demo-select">
                        <option>Choose an option</option>
                        <option>Option 1</option>
                        <option>Option 2</option>
                        <option>Option 3</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="demo-textarea">Textarea</label>
                    <textarea id="demo-textarea" placeholder="Enter your message here"></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Submit Form</button>
            </form>
        </section>
        
        <!-- Action Cards -->
        <section class="section-card mb-5">
            <h2>Action Cards</h2>
            <div class="actions-grid">
                <a href="#" class="action-card">
                    <h3>View Dashboard</h3>
                    <p>Access your personalized dashboard with progress tracking and notifications</p>
                </a>
                
                <a href="#" class="action-card">
                    <h3>Training Materials</h3>
                    <p>Download and access all training resources and documentation</p>
                </a>
                
                <a href="#" class="action-card">
                    <h3>Contact Support</h3>
                    <p>Get help from our support team or ask questions about your journey</p>
                </a>
            </div>
        </section>
        
        <!-- Status Card Demo -->
        <div class="status-card mb-5">
            <div class="status-info">
                <div class="current-step">
                    <span class="step-number">2</span>
                    <div class="step-details">
                        <h3>PCNC Training</h3>
                        <p>Complete the 6-month Pastoral Care and Nurture Course</p>
                        <span class="status-badge">In Progress</span>
                    </div>
                </div>
            </div>
            <div class="status-actions">
                <p class="status-message">Keep up the great work! You're making excellent progress.</p>
            </div>
        </div>
        
        <div class="text-center">
            <a href="index.php" class="btn btn-primary">← Back to Home</a>
            <a href="login.php" class="btn btn-secondary">Login to System</a>
        </div>
    </div>
</body>
</html>
