-- STAR Volunteer Management System Database Schema
-- Created: 2025-09-02

-- Create database
CREATE DATABASE IF NOT EXISTS star_volunteer_system CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE star_volunteer_system;

-- Users table (all system users)
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role ENUM('aspirant', 'administrator', 'mentor', 'mds', 'pastor') NOT NULL,
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    profile_image VARCHAR(255) NULL
);

-- Ministries table
CREATE TABLE ministries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    coordinator_id INT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (coordinator_id) REFERENCES users(id) ON DELETE SET NULL
);

-- STAR Journey Steps definition
CREATE TABLE journey_steps (
    id INT PRIMARY KEY AUTO_INCREMENT,
    step_number INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    duration_days INT DEFAULT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Aspirants table (extends users for aspirant-specific data)
CREATE TABLE aspirants (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT UNIQUE NOT NULL,
    application_date DATE NOT NULL,
    current_step INT DEFAULT 1,
    ministry_preference_1 INT,
    ministry_preference_2 INT,
    ministry_preference_3 INT,
    assigned_ministry_id INT NULL,
    mentor_id INT NULL,
    status ENUM('active', 'completed', 'rejected', 'withdrawn') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ministry_preference_1) REFERENCES ministries(id) ON DELETE SET NULL,
    FOREIGN KEY (ministry_preference_2) REFERENCES ministries(id) ON DELETE SET NULL,
    FOREIGN KEY (ministry_preference_3) REFERENCES ministries(id) ON DELETE SET NULL,
    FOREIGN KEY (assigned_ministry_id) REFERENCES ministries(id) ON DELETE SET NULL,
    FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Step Progress tracking
CREATE TABLE step_progress (
    id INT PRIMARY KEY AUTO_INCREMENT,
    aspirant_id INT NOT NULL,
    step_id INT NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'rejected', 'extended') DEFAULT 'pending',
    started_at TIMESTAMP NULL,
    completed_at TIMESTAMP NULL,
    deadline DATE NULL,
    validator_id INT NULL,
    validation_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (aspirant_id) REFERENCES aspirants(id) ON DELETE CASCADE,
    FOREIGN KEY (step_id) REFERENCES journey_steps(id) ON DELETE CASCADE,
    FOREIGN KEY (validator_id) REFERENCES users(id) ON DELETE SET NULL,
    UNIQUE KEY unique_aspirant_step (aspirant_id, step_id)
);

-- PCNC Training records (Step 2)
CREATE TABLE pcnc_training (
    id INT PRIMARY KEY AUTO_INCREMENT,
    aspirant_id INT NOT NULL,
    start_date DATE NOT NULL,
    expected_completion_date DATE NOT NULL,
    actual_completion_date DATE NULL,
    attendance_percentage DECIMAL(5,2) DEFAULT 0.00,
    final_grade DECIMAL(5,2) NULL,
    status ENUM('enrolled', 'in_progress', 'completed', 'failed', 'withdrawn') DEFAULT 'enrolled',
    instructor_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (aspirant_id) REFERENCES aspirants(id) ON DELETE CASCADE
);

-- MDS Interviews (Step 3)
CREATE TABLE mds_interviews (
    id INT PRIMARY KEY AUTO_INCREMENT,
    aspirant_id INT NOT NULL,
    interviewer_id INT NOT NULL,
    scheduled_date DATETIME NOT NULL,
    actual_date DATETIME NULL,
    status ENUM('scheduled', 'completed', 'rescheduled', 'cancelled') DEFAULT 'scheduled',
    result ENUM('approved', 'rejected', 'pending') DEFAULT 'pending',
    interview_notes TEXT,
    recommendation TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (aspirant_id) REFERENCES aspirants(id) ON DELETE CASCADE,
    FOREIGN KEY (interviewer_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Ministry Training records (Step 4)
CREATE TABLE ministry_training (
    id INT PRIMARY KEY AUTO_INCREMENT,
    aspirant_id INT NOT NULL,
    ministry_id INT NOT NULL,
    mentor_id INT NOT NULL,
    start_date DATE NOT NULL,
    expected_completion_date DATE NOT NULL,
    actual_completion_date DATE NULL,
    status ENUM('assigned', 'in_progress', 'completed', 'extended', 'failed') DEFAULT 'assigned',
    mentor_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (aspirant_id) REFERENCES aspirants(id) ON DELETE CASCADE,
    FOREIGN KEY (ministry_id) REFERENCES ministries(id) ON DELETE CASCADE,
    FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Mentor Reports (Step 5)
CREATE TABLE mentor_reports (
    id INT PRIMARY KEY AUTO_INCREMENT,
    aspirant_id INT NOT NULL,
    mentor_id INT NOT NULL,
    ministry_training_id INT NOT NULL,
    report_type ENUM('progress', 'final') DEFAULT 'final',
    recommendation ENUM('favorable', 'unfavorable', 'needs_extension') NOT NULL,
    attendance_rating INT CHECK (attendance_rating BETWEEN 1 AND 5),
    participation_rating INT CHECK (participation_rating BETWEEN 1 AND 5),
    attitude_rating INT CHECK (attitude_rating BETWEEN 1 AND 5),
    skills_rating INT CHECK (skills_rating BETWEEN 1 AND 5),
    overall_rating INT CHECK (overall_rating BETWEEN 1 AND 5),
    strengths TEXT,
    areas_for_improvement TEXT,
    additional_comments TEXT,
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_by INT NULL,
    reviewed_at TIMESTAMP NULL,
    status ENUM('draft', 'submitted', 'reviewed', 'approved') DEFAULT 'draft',
    FOREIGN KEY (aspirant_id) REFERENCES aspirants(id) ON DELETE CASCADE,
    FOREIGN KEY (mentor_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (ministry_training_id) REFERENCES ministry_training(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewed_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Final Assignments (Step 6)
CREATE TABLE final_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    aspirant_id INT NOT NULL,
    ministry_id INT NOT NULL,
    assigned_role VARCHAR(100) NOT NULL,
    assignment_date DATE NOT NULL,
    assigned_by INT NOT NULL,
    status ENUM('active', 'inactive', 'transferred') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (aspirant_id) REFERENCES aspirants(id) ON DELETE CASCADE,
    FOREIGN KEY (ministry_id) REFERENCES ministries(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Notifications system
CREATE TABLE notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    action_url VARCHAR(255) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Email logs
CREATE TABLE email_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    recipient_email VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    sent_at TIMESTAMP NULL,
    error_message TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- System settings
CREATE TABLE system_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT NOT NULL,
    description TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    updated_by INT,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
);

-- Document storage
CREATE TABLE documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(50) NOT NULL,
    file_size INT NOT NULL,
    category ENUM('training_material', 'form', 'report', 'other') DEFAULT 'other',
    access_level ENUM('public', 'aspirants', 'mentors', 'administrators', 'all_users') DEFAULT 'all_users',
    uploaded_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE CASCADE
);
