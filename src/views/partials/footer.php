<?php
$appConfig = require __DIR__ . '/../../../config/app.php';
?>
<footer class="main-footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <h3>STAR System</h3>
                <p>Volunteer Management for <?php echo htmlspecialchars($appConfig['church']['name']); ?></p>
            </div>
            
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <?php if (Auth::check()): ?>
                        <li><a href="<?php echo Auth::getDashboardUrl(); ?>">Dashboard</a></li>
                        <li><a href="profile.php">Profile</a></li>
                        <li><a href="documents.php">Resources</a></li>
                    <?php else: ?>
                        <li><a href="login.php">Login</a></li>
                        <li><a href="register.php">Apply to Volunteer</a></li>
                        <li><a href="index.php">Home</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Support</h4>
                <ul>
                    <li><a href="help.php">Help Center</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                    <li><a href="faq.php">FAQ</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h4>Contact</h4>
                <?php if (!empty($appConfig['church']['email'])): ?>
                    <p>Email: <a href="mailto:<?php echo $appConfig['church']['email']; ?>"><?php echo $appConfig['church']['email']; ?></a></p>
                <?php endif; ?>
                <?php if (!empty($appConfig['church']['phone'])): ?>
                    <p>Phone: <?php echo htmlspecialchars($appConfig['church']['phone']); ?></p>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars($appConfig['church']['name']); ?>. All rights reserved.</p>
            <p>STAR Volunteer Management System v1.0</p>
        </div>
    </div>
</footer>
