-- Role-specific profile tables for STAR System
-- Enhanced user management with role-specific data

-- Pastor profiles table
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
);

-- MDS profiles table
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
);

-- Mentor profiles table
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
);

-- Aspirant profiles table (enhanced)
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
);

-- Administrator profiles table
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
);

-- User activity log table
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
);

-- AI insights table for the AI agent sidebar
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
);

-- User preferences table
CREATE TABLE IF NOT EXISTS user_preferences (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    preference_key VARCHAR(100) NOT NULL,
    preference_value JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_preference (user_id, preference_key)
);

-- Bulk operations log table
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
);

-- Insert default preferences for AI sidebar
INSERT IGNORE INTO user_preferences (user_id, preference_key, preference_value)
SELECT id, 'ai_sidebar_enabled', 'true'
FROM users
WHERE role IN ('administrator', 'pastor', 'mds');

INSERT IGNORE INTO user_preferences (user_id, preference_key, preference_value)
SELECT id, 'ai_sidebar_collapsed', 'false'
FROM users;

-- Create indexes for better performance
CREATE INDEX idx_users_role_status ON users(role, status);
CREATE INDEX idx_users_created_at ON users(created_at);
CREATE INDEX idx_users_email ON users(email);

-- Update existing aspirants table if it exists
-- This ensures compatibility with existing data
INSERT IGNORE INTO aspirant_profiles (user_id, application_date, current_step)
SELECT user_id, application_date, current_step
FROM aspirants
WHERE user_id IS NOT NULL;
