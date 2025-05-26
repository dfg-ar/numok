<?php

namespace Numok\Controllers;

class PartnerBaseController extends Controller {
    protected function view(string $template, array $data = []): void {
        // Always include settings in view data
        if (!isset($data['settings'])) {
            $data['settings'] = $this->getSettings();
        }
        
        extract($data);
        
        require ROOT_PATH . "/src/Views/partner/layouts/header.php";
        require ROOT_PATH . "/src/Views/{$template}.php";
        require ROOT_PATH . "/src/Views/partner/layouts/footer.php";
    }
}