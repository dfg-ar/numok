<?php

namespace Numok\Controllers;

class PartnerBaseController extends Controller {
    protected function view(string $template, array $data = []): void {
        extract($data);
        
        require ROOT_PATH . "/src/Views/partner/layouts/header.php";
        require ROOT_PATH . "/src/Views/{$template}.php";
        require ROOT_PATH . "/src/Views/partner/layouts/footer.php";
    }
}