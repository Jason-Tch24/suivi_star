-- STAR System Initial Data Seeds
-- Created: 2025-09-02

USE star_volunteer_system;

-- Insert Journey Steps
INSERT INTO journey_steps (step_number, name, description, duration_days) VALUES
(1, 'Application', 'Complete online registration form and become Aspirant STAR', 7),
(2, 'PCNC Training', '6-month PCNC (Pastoral Care and Nurture Course) training program', 180),
(3, 'MDS Interview', 'Interview with Ministry of STAR (MDS) for validation/rejection', 14),
(4, 'Ministry Training', 'One-month training within chosen ministry with assigned mentor', 30),
(5, 'Mentor Report', 'Mentor submits favorable/unfavorable report determining progression', 7),
(6, 'Confirmation & Assignment', 'Validated aspirants become active STAR members assigned to specific ministry roles', 7);

-- Insert default ministries
INSERT INTO ministries (name, description, status) VALUES
('Worship & Music', 'Leading worship services, choir, and musical ministries', 'active'),
('Children Ministry', 'Teaching and caring for children during services and programs', 'active'),
('Youth Ministry', 'Mentoring and leading youth programs and activities', 'active'),
('Hospitality', 'Welcoming guests and managing church hospitality services', 'active'),
('Technical Ministry', 'Audio/visual support, live streaming, and technical services', 'active'),
('Pastoral Care', 'Visiting members, counseling, and providing spiritual support', 'active'),
('Evangelism', 'Outreach programs and community evangelism efforts', 'active'),
('Administration', 'Church administration, finance, and organizational support', 'active'),
('Security', 'Church security and safety coordination', 'active'),
('Cleaning & Maintenance', 'Church facility maintenance and cleaning coordination', 'active');

-- Insert default administrator user
INSERT INTO users (email, password_hash, first_name, last_name, phone, role, status) VALUES
('admin@star-church.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System', 'Administrator', '+1234567890', 'administrator', 'active');

-- Insert sample pastor user
INSERT INTO users (email, password_hash, first_name, last_name, phone, role, status) VALUES
('pastor@star-church.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John', 'Pastor', '+1234567891', 'pastor', 'active');

-- Insert sample MDS user
INSERT INTO users (email, password_hash, first_name, last_name, phone, role, status) VALUES
('mds@star-church.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Mary', 'Johnson', '+1234567892', 'mds', 'active');

-- Insert sample mentor users
INSERT INTO users (email, password_hash, first_name, last_name, phone, role, status) VALUES
('mentor1@star-church.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'David', 'Wilson', '+1234567893', 'mentor', 'active'),
('mentor2@star-church.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Sarah', 'Brown', '+1234567894', 'mentor', 'active'),
('mentor3@star-church.org', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Michael', 'Davis', '+1234567895', 'mentor', 'active');

-- Insert sample aspirant users
INSERT INTO users (email, password_hash, first_name, last_name, phone, role, status) VALUES
('aspirant1@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Alice', 'Smith', '+1234567896', 'aspirant', 'active'),
('aspirant2@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Bob', 'Jones', '+1234567897', 'aspirant', 'active'),
('aspirant3@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Carol', 'Miller', '+1234567898', 'aspirant', 'active');

-- Update ministry coordinators
UPDATE ministries SET coordinator_id = 4 WHERE name = 'Worship & Music';
UPDATE ministries SET coordinator_id = 5 WHERE name = 'Children Ministry';
UPDATE ministries SET coordinator_id = 6 WHERE name = 'Youth Ministry';

-- Insert sample aspirants
INSERT INTO aspirants (user_id, application_date, current_step, ministry_preference_1, ministry_preference_2, ministry_preference_3, status) VALUES
(7, '2024-08-01', 2, 1, 2, 3, 'active'),
(8, '2024-08-15', 1, 4, 5, 6, 'active'),
(9, '2024-09-01', 3, 2, 3, 4, 'active');

-- Insert step progress for sample aspirants
-- Aspirant 1 (Alice) - Currently in PCNC Training (Step 2)
INSERT INTO step_progress (aspirant_id, step_id, status, started_at, completed_at, validator_id) VALUES
(1, 1, 'completed', '2024-08-01 09:00:00', '2024-08-05 17:00:00', 1),
(1, 2, 'in_progress', '2024-08-06 09:00:00', NULL, NULL);

-- Aspirant 2 (Bob) - Just applied (Step 1)
INSERT INTO step_progress (aspirant_id, step_id, status, started_at) VALUES
(2, 1, 'in_progress', '2024-08-15 10:00:00');

-- Aspirant 3 (Carol) - Completed PCNC, awaiting MDS Interview (Step 3)
INSERT INTO step_progress (aspirant_id, step_id, status, started_at, completed_at, validator_id) VALUES
(3, 1, 'completed', '2024-09-01 09:00:00', '2024-09-03 17:00:00', 1),
(3, 2, 'completed', '2024-09-04 09:00:00', '2024-12-04 17:00:00', 1),
(3, 3, 'pending', NULL, NULL, NULL);

-- Insert PCNC training records
INSERT INTO pcnc_training (aspirant_id, start_date, expected_completion_date, attendance_percentage, status) VALUES
(1, '2024-08-06', '2025-02-06', 85.50, 'in_progress'),
(3, '2024-09-04', '2025-03-04', 92.30, 'completed');

-- Insert sample MDS interview
INSERT INTO mds_interviews (aspirant_id, interviewer_id, scheduled_date, status) VALUES
(3, 3, '2024-12-15 14:00:00', 'scheduled');

-- Insert system settings
INSERT INTO system_settings (setting_key, setting_value, description) VALUES
('site_name', 'STAR Volunteer Management System', 'Name of the application'),
('church_name', 'Grace Community Church', 'Name of the church organization'),
('admin_email', 'admin@star-church.org', 'Primary administrator email'),
('pcnc_duration_months', '6', 'Duration of PCNC training in months'),
('ministry_training_duration_days', '30', 'Duration of ministry training in days'),
('email_notifications_enabled', '1', 'Enable/disable email notifications'),
('max_file_upload_size', '10485760', 'Maximum file upload size in bytes (10MB)'),
('session_timeout_minutes', '120', 'Session timeout in minutes');

-- Insert sample documents
INSERT INTO documents (title, description, file_path, file_type, file_size, category, access_level, uploaded_by) VALUES
('STAR Application Form', 'Official application form for STAR volunteers', '/uploads/documents/star_application_form.pdf', 'pdf', 245760, 'form', 'public', 1),
('PCNC Training Manual', 'Complete training manual for PCNC course', '/uploads/documents/pcnc_training_manual.pdf', 'pdf', 1048576, 'training_material', 'aspirants', 1),
('Ministry Guidelines', 'General guidelines for all ministry volunteers', '/uploads/documents/ministry_guidelines.pdf', 'pdf', 512000, 'training_material', 'all_users', 1),
('Mentor Report Template', 'Template for mentor evaluation reports', '/uploads/documents/mentor_report_template.docx', 'docx', 102400, 'form', 'mentors', 1);

-- Note: Default password for all sample users is 'password123'
-- In production, these should be changed immediately
