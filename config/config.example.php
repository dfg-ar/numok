<?php

// Database configuration
$config['database'] = [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'database' => getenv('DB_NAME') ?: 'numok_app',
    'username' => getenv('DB_USER') ?: 'numok_user',
    'password' => getenv('DB_PASS') ?: 'change_me_app_2025',
];

// Email configuration
$config['email'] = [
    'resend_api_key' => getenv('RESEND_API_KEY') ?: 'RESEND_API_KEY',
    'from_address' => getenv('MAIL_FROM_ADDRESS') ?: 'onboarding@resend.dev',
];

// Initialize database connection
\Numok\Database\Database::setConfig($config['database']);

// Application configuration
$config['app'] = [
    'name' => 'Numok',
    'url' => getenv('APP_URL') ?: 'http://localhost',
    'debug' => getenv('APP_DEBUG') ?: true
];

// Time zone
date_default_timezone_set('UTC');

return $config;