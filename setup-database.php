<?php
/**
 * Database Setup Script - STAR System
 * Creates necessary tables for enhanced user management and AI features
 */

require_once __DIR__ . '/src/models/Database.php';

try {
    $db = Database::getInstance();
    
    echo "<h1>🔧 Setting up Enhanced STAR Database</h1>";
    echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 8px;'>";
    
    // Create role-specific profile tables
    $tables = [
        'pastor_profiles' => "
            CREATE TABLE IF NOT EXISTS pastor_profiles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                church_position VARCHAR(100),
                years_of_service INT DEFAULT 0,
                oversight_areas TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY unique_pastor_user (user_id)
            )
        ",
        
        'mds_profiles' => "
            CREATE TABLE IF NOT EXISTS mds_profiles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                department ENUM('Administration', 'Training', 'Coordination', 'Assessment') NOT NULL,
                certification_level ENUM('Basic', 'Intermediate', 'Advanced', 'Expert') NOT NULL,
                specialization_areas JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY unique_mds_user (user_id)
            )
        ",
        
        'mentor_profiles' => "
            CREATE TABLE IF NOT EXISTS mentor_profiles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                experience_level ENUM('Beginner', 'Intermediate', 'Advanced', 'Expert') NOT NULL,
                mentoring_capacity INT NOT NULL DEFAULT 3,
                available_time_slots JSON,
                current_mentees INT DEFAULT 0,
                total_mentees_completed INT DEFAULT 0,
                average_rating DECIMAL(3,2) DEFAULT 0.00,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY unique_mentor_user (user_id)
            )
        ",
        
        'aspirant_profiles' => "
            CREATE TABLE IF NOT EXISTS aspirant_profiles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                ministry_preference_1 VARCHAR(100),
                ministry_preference_2 VARCHAR(100),
                ministry_preference_3 VARCHAR(100),
                background_check_status ENUM('Not Started', 'In Progress', 'Completed', 'Approved', 'Rejected') DEFAULT 'Not Started',
                training_progress INT DEFAULT 0,
                current_step INT DEFAULT 1,
                mentor_id INT NULL,
                application_date DATE,
                completion_date DATE NULL,
                notes TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE SET NULL,
                UNIQUE KEY unique_aspirant_user (user_id)
            )
        ",
        
        'administrator_profiles' => "
            CREATE TABLE IF NOT EXISTS administrator_profiles (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                access_level ENUM('Super Admin', 'System Admin', 'Limited Admin') DEFAULT 'System Admin',
                permissions JSON,
                last_login TIMESTAMP NULL,
                login_count INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY unique_admin_user (user_id)
            )
        ",
        
        'user_activity_log' => "
            CREATE TABLE IF NOT EXISTS user_activity_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                action VARCHAR(100) NOT NULL,
                description TEXT,
                performed_by INT NULL,
                ip_address VARCHAR(45),
                user_agent TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_user_activity (user_id, created_at),
                INDEX idx_action (action),
                INDEX idx_performed_by (performed_by)
            )
        ",
        
        'ai_insights' => "
            CREATE TABLE IF NOT EXISTS ai_insights (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                insight_type VARCHAR(100) NOT NULL,
                content JSON NOT NULL,
                priority ENUM('low', 'medium', 'high', 'critical') DEFAULT 'medium',
                status ENUM('active', 'dismissed', 'acted_upon') DEFAULT 'active',
                expires_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_insights (user_id, status, created_at),
                INDEX idx_insight_type (insight_type),
                INDEX idx_priority (priority)
            )
        ",
        
        'user_preferences' => "
            CREATE TABLE IF NOT EXISTS user_preferences (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                preference_key VARCHAR(100) NOT NULL,
                preference_value JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                UNIQUE KEY unique_user_preference (user_id, preference_key)
            )
        ",
        
        'bulk_operations_log' => "
            CREATE TABLE IF NOT EXISTS bulk_operations_log (
                id INT AUTO_INCREMENT PRIMARY KEY,
                operation_type VARCHAR(100) NOT NULL,
                performed_by INT NOT NULL,
                affected_users JSON NOT NULL,
                operation_data JSON,
                success_count INT DEFAULT 0,
                failed_count INT DEFAULT 0,
                errors JSON,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (performed_by) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_operation_type (operation_type),
                INDEX idx_performed_by (performed_by, created_at)
            )
        "
    ];
    
    foreach ($tables as $tableName => $sql) {
        echo "<strong>Creating table: $tableName</strong><br>";
        try {
            $db->exec($sql);
            echo "✅ Table '$tableName' created successfully<br><br>";
        } catch (PDOException $e) {
            echo "❌ Error creating table '$tableName': " . $e->getMessage() . "<br><br>";
        }
    }
    
    // Create indexes for better performance
    echo "<strong>Creating indexes...</strong><br>";
    $indexes = [
        "CREATE INDEX IF NOT EXISTS idx_users_role_status ON users(role, status)",
        "CREATE INDEX IF NOT EXISTS idx_users_created_at ON users(created_at)",
        "CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)"
    ];
    
    foreach ($indexes as $indexSql) {
        try {
            $db->exec($indexSql);
            echo "✅ Index created successfully<br>";
        } catch (PDOException $e) {
            echo "❌ Error creating index: " . $e->getMessage() . "<br>";
        }
    }
    
    // Insert default preferences for AI sidebar
    echo "<br><strong>Setting up default preferences...</strong><br>";
    try {
        $stmt = $db->prepare("
            INSERT IGNORE INTO user_preferences (user_id, preference_key, preference_value)
            SELECT id, 'ai_sidebar_enabled', 'true'
            FROM users
            WHERE role IN ('administrator', 'pastor', 'mds')
        ");
        $stmt->execute();
        
        $stmt = $db->prepare("
            INSERT IGNORE INTO user_preferences (user_id, preference_key, preference_value)
            SELECT id, 'ai_sidebar_collapsed', 'false'
            FROM users
        ");
        $stmt->execute();
        
        echo "✅ Default preferences set up successfully<br>";
    } catch (PDOException $e) {
        echo "❌ Error setting up preferences: " . $e->getMessage() . "<br>";
    }
    
    // Migrate existing aspirant data if it exists
    echo "<br><strong>Migrating existing data...</strong><br>";
    try {
        $stmt = $db->query("SHOW TABLES LIKE 'aspirants'");
        if ($stmt->rowCount() > 0) {
            $stmt = $db->prepare("
                INSERT IGNORE INTO aspirant_profiles (user_id, application_date, current_step)
                SELECT user_id, application_date, current_step
                FROM aspirants
                WHERE user_id IS NOT NULL
            ");
            $stmt->execute();
            echo "✅ Existing aspirant data migrated successfully<br>";
        } else {
            echo "ℹ️ No existing aspirant data to migrate<br>";
        }
    } catch (PDOException $e) {
        echo "❌ Error migrating data: " . $e->getMessage() . "<br>";
    }
    
    echo "</div>";
    echo "<br><div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; border: 1px solid #c3e6cb;'>";
    echo "<h2>🎉 Database Setup Complete!</h2>";
    echo "<p><strong>Enhanced features now available:</strong></p>";
    echo "<ul>";
    echo "<li>✅ Role-specific user profiles</li>";
    echo "<li>✅ Enhanced user management system</li>";
    echo "<li>✅ AI insights and recommendations</li>";
    echo "<li>✅ User activity logging</li>";
    echo "<li>✅ Bulk operations support</li>";
    echo "<li>✅ User preferences system</li>";
    echo "</ul>";
    echo "<p><strong>Next steps:</strong></p>";
    echo "<ul>";
    echo "<li>🔗 <a href='admin/user-wizard.php'>Test the User Creation Wizard</a></li>";
    echo "<li>🔗 <a href='admin/users.php'>Access Enhanced User Management</a></li>";
    echo "<li>🔗 <a href='dashboard.php'>View AI-Enhanced Dashboard</a></li>";
    echo "</ul>";
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; border: 1px solid #f5c6cb;'>";
    echo "<h2>❌ Database Setup Failed</h2>";
    echo "<p>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}
?>
