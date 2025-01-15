<?php
declare(strict_types=1);

// Start session
session_start();

// Initialize error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Define root path
define('ROOT_PATH', dirname(__DIR__));

// Autoload dependencies
require_once ROOT_PATH . '/vendor/autoload.php';

// Load configuration
require_once ROOT_PATH . '/config/config.php';

// Get the path
$path = $_SERVER['REQUEST_URI'];
$path = parse_url($path, PHP_URL_PATH);
$path = trim($path, '/');

// Define routes
$routes = [
    // Core routes
    '' => ['DashboardController', 'index'],
    'dashboard' => ['DashboardController', 'index'],

    // Auth routes
    'login' => ['AuthController', 'index'],
    'auth/login' => ['AuthController', 'login'],
    'logout' => ['AuthController', 'logout'],

    // Settings routes
    'settings' => ['SettingsController', 'index'],
    'settings/update' => ['SettingsController', 'update'],
    'settings/test-connection' => ['SettingsController', 'testConnection'],

    // Programs routes
    'programs' => ['ProgramsController', 'index'],
    'programs/create' => ['ProgramsController', 'create'],
    'programs/store' => ['ProgramsController', 'store'],
    'programs/(\d+)/edit' => ['ProgramsController', 'edit'],
    'programs/(\d+)/integration' => ['ProgramsController', 'integration'],
    'programs/(\d+)/update' => ['ProgramsController', 'update'],
    'programs/(\d+)/delete' => ['ProgramsController', 'delete'],

    // Partners routes
    'partners' => ['PartnersController', 'index'],
    'partners/create' => ['PartnersController', 'create'],
    'partners/store' => ['PartnersController', 'store'],
    'partners/(\d+)/edit' => ['PartnersController', 'edit'],
    'partners/(\d+)/update' => ['PartnersController', 'update'],
    'partners/(\d+)/delete' => ['PartnersController', 'delete'],
    'partners/(\d+)/assign-program' => ['PartnersController', 'assignProgram'],

    // Conversions routes
    'conversions' => ['ConversionsController', 'index'],
    'conversions/update-status' => ['ConversionsController', 'updateStatus'],
    'conversions/export' => ['ConversionsController', 'export'],

    // API routes
    'api/tracking/config/(\d+)' => ['TrackingController', 'config'],
    'api/tracking/click' => ['TrackingController', 'click'],

    // Webhook routes
    'webhook/stripe' => ['WebhookController', 'stripeWebhook'],
];

// Check if route exists or matches a pattern
$matched = false;

foreach ($routes as $pattern => $route) {
    $pattern = str_replace('/', '\/', $pattern);
    if (preg_match('/^' . $pattern . '$/', $path, $matches)) {
        // Remove the full match from the matches array
        array_shift($matches);
        
        // Get controller and method
        $controllerName = "Numok\\Controllers\\" . $route[0];
        $methodName = $route[1];

        try {
            // Create controller instance
            $controller = new $controllerName();
            
            // Type cast numeric parameters to integer
            $params = array_map(function($value) {
                return is_numeric($value) ? (int)$value : $value;
            }, $matches);
            
            // Call the method with any captured parameters
            $controller->$methodName(...$params);
            
            $matched = true;
            break;
        } catch (\Exception $e) {
            // Log error
            error_log($e->getMessage());
            
            // Show error in development
            if (isset($config['app']['debug']) && $config['app']['debug']) {
                throw $e;
            }
            
            // Show 500 error in production
            http_response_code(500);
            echo "500 - Internal Server Error";
            exit;
        }
    }
}

if (!$matched) {
    http_response_code(404);
    echo "404 - Page Not Found";
    exit;
}