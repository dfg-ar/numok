<?php

namespace Numok\Middleware;

class AuthMiddleware {
    public static function handle(): void {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
    }

    public static function adminOnly(): void {
        self::handle();
        
        // Check if user is admin
        if (!($_SESSION['is_admin'] ?? false)) {
            header('Location: /dashboard');
            exit;
        }
    }
}