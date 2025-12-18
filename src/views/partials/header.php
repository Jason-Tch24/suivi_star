<?php
$user = Auth::user();
$appConfig = require __DIR__ . '/../../../config/app.php';
?>
<header class="main-header">
    <div class="container">
        <div class="header-content">
            <div class="logo">
                <a href="index.php">
                    <h1>STAR</h1>
                    <span>Volunteer Management</span>
                </a>
            </div>
            
            <nav class="main-nav">
                <?php if ($user): ?>
                    <?php if ($user['role'] === 'aspirant'): ?>
                        <a href="dashboard.php">Dashboard</a>
                        <a href="profile.php">Profile</a>
                        <a href="documents.php">Materials</a>
                    <?php elseif ($user['role'] === 'mentor'): ?>
                        <a href="dashboard.php">Dashboard</a>
                        <a href="mentor_aspirants.php">My Aspirants</a>
                        <a href="mentor_reports.php">Reports</a>
                    <?php elseif ($user['role'] === 'mds'): ?>
                        <a href="dashboard.php">Dashboard</a>
                        <a href="mds_interviews.php">Interviews</a>
                        <a href="aspirants.php">Aspirants</a>
                    <?php elseif ($user['role'] === 'administrator'): ?>
                        <a href="dashboard.php">Dashboard</a>
                        <a href="admin_aspirants.php">Aspirants</a>
                        <a href="admin_ministries.php">Ministries</a>
                    <?php elseif ($user['role'] === 'pastor'): ?>
                        <a href="dashboard.php">Dashboard</a>
                        <a href="pastor_analytics.php">Analytics</a>
                        <a href="aspirants.php">Aspirants</a>
                    <?php endif; ?>
                <?php endif; ?>
            </nav>
            
            <div class="user-menu">
                <?php if ($user): ?>
                    <div class="user-info">
                        <span class="user-name"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></span>
                        <span class="user-role"><?php echo ucfirst($user['role']); ?></span>
                    </div>
                    <div class="user-actions">
                        <a href="profile.php" class="profile-link">Profile</a>
                        <a href="notifications.php" class="notifications-link">
                            Notifications
                            <span class="notification-badge" id="notification-count" style="display: none;"></span>
                        </a>
                        <a href="logout.php" class="logout-link">Logout</a>
                    </div>
                <?php else: ?>
                    <div class="auth-links">
                        <a href="login.php" class="btn btn-primary">Login</a>
                        <a href="register.php" class="btn btn-secondary">Apply</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>
