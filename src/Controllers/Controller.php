<?php

namespace Numok\Controllers;

class Controller {
    
    protected function handlePreflightRequest(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('Access-Control-Allow-Origin: *');
            header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
            header('Access-Control-Allow-Headers: Content-Type');
            header('Access-Control-Max-Age: 86400');
            http_response_code(200);
            exit;
        }
    }

    protected function view(string $template, array $data = []): void {
        extract($data);
        
        require ROOT_PATH . "/src/Views/layouts/header.php";
        require ROOT_PATH . "/src/Views/{$template}.php";
        require ROOT_PATH . "/src/Views/layouts/footer.php";
    }

    protected function json(array $data): void {
        // Add CORS headers for all API responses
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type');
        header('Access-Control-Max-Age: 86400');
        
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}