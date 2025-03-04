<?php

// Database configuration
$config['database'] = [
    'host' => getenv('DB_HOST') ?: 'localhost',
    'database' => getenv('DB_NAME') ?: 'numok',
    'username' => getenv('DB_USER') ?: 'DBUSER',
    'password' => getenv('DB_PASS') ?: 'DBPASS',
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