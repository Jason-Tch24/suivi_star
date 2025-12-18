<?php
/**
 * Application Configuration for STAR Volunteer Management System
 */

return [
    // Application settings
    'name' => $_ENV['APP_NAME'] ?? 'STAR Volunteer Management System',
    'env' => $_ENV['APP_ENV'] ?? 'development',
    'debug' => $_ENV['APP_DEBUG'] ?? true,
    'url' => $_ENV['APP_URL'] ?? 'http://localhost/suivie_star',
    'timezone' => $_ENV['APP_TIMEZONE'] ?? 'UTC',
    
    // Security settings
    'key' => $_ENV['APP_KEY'] ?? 'star-volunteer-system-secret-key-2025',
    'cipher' => 'AES-256-CBC',
    
    // Session configuration
    'session' => [
        'lifetime' => 120, // minutes
        'expire_on_close' => false,
        'encrypt' => false,
        'files' => __DIR__ . '/../storage/sessions',
        'connection' => null,
        'table' => 'sessions',
        'store' => null,
        'lottery' => [2, 100],
        'cookie' => 'star_session',
        'path' => '/',
        'domain' => null,
        'secure' => false,
        'http_only' => true,
        'same_site' => 'lax',
    ],
    
    // Email configuration
    'mail' => [
        'driver' => $_ENV['MAIL_DRIVER'] ?? 'smtp',
        'host' => $_ENV['MAIL_HOST'] ?? 'localhost',
        'port' => $_ENV['MAIL_PORT'] ?? 587,
        'username' => $_ENV['MAIL_USERNAME'] ?? '',
        'password' => $_ENV['MAIL_PASSWORD'] ?? '',
        'encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
        'from' => [
            'address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@star-church.org',
            'name' => $_ENV['MAIL_FROM_NAME'] ?? 'STAR System',
        ],
    ],
    
    // File upload settings
    'upload' => [
        'max_size' => 10 * 1024 * 1024, // 10MB
        'allowed_types' => ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png', 'gif'],
        'path' => __DIR__ . '/../public/uploads',
        'url' => '/uploads',
    ],
    
    // Logging configuration
    'log' => [
        'channel' => 'single',
        'path' => __DIR__ . '/../storage/logs/star.log',
        'level' => $_ENV['LOG_LEVEL'] ?? 'debug',
        'days' => 14,
    ],
    
    // Church-specific settings
    'church' => [
        'name' => $_ENV['CHURCH_NAME'] ?? 'Grace Community Church',
        'address' => $_ENV['CHURCH_ADDRESS'] ?? '',
        'phone' => $_ENV['CHURCH_PHONE'] ?? '',
        'email' => $_ENV['CHURCH_EMAIL'] ?? '',
        'website' => $_ENV['CHURCH_WEBSITE'] ?? '',
    ],
    
    // STAR system specific settings
    'star' => [
        'pcnc_duration_months' => 6,
        'ministry_training_duration_days' => 30,
        'application_review_days' => 7,
        'interview_scheduling_days' => 14,
        'mentor_report_days' => 7,
        'final_assignment_days' => 7,
    ],

    // Google OAuth configuration
    'google' => [
        'client_id' => $_ENV['GOOGLE_CLIENT_ID'] ?? '',
        'client_secret' => $_ENV['GOOGLE_CLIENT_SECRET'] ?? '',
        'redirect_uri' => ($_ENV['APP_URL'] ?? 'http://localhost/suivie_star') . '/auth/google/callback.php',
        'scopes' => ['email', 'profile'],
    ],
];
