<?php

namespace Numok\Controllers;

class Controller {
    protected function view(string $template, array $data = []): void {
        extract($data);
        
        require ROOT_PATH . "/src/Views/layouts/header.php";
        require ROOT_PATH . "/src/Views/{$template}.php";
        require ROOT_PATH . "/src/Views/layouts/footer.php";
    }

    protected function json(array $data): void {
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}