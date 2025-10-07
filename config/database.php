<?php
/**
 * Database Configuration for STAR Volunteer Management System
 * 
 * This file contains database connection settings and configuration
 * for the STAR volunteer management intranet system.
 */

return [
    // Default database connection
    'default' => 'mysql',
    
    // Database connections
    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => $_ENV['DB_HOST'] ?? '127.0.0.1',
            'port' => $_ENV['DB_PORT'] ?? '8889',
            'database' => $_ENV['DB_DATABASE'] ?? 'star_volunteer_system',
            'username' => $_ENV['DB_USERNAME'] ?? 'root',
            'password' => $_ENV['DB_PASSWORD'] ?? 'root',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ],
    ],
    
    // Migration settings
    'migrations' => [
        'table' => 'migrations',
        'path' => __DIR__ . '/../database/migrations',
    ],
    
    // Seed settings
    'seeds' => [
        'path' => __DIR__ . '/../database/seeds',
    ],
];
