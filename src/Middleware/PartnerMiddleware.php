<?php

namespace Numok\Middleware;

class PartnerMiddleware {
    public static function handle(): void {
        // Start session if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Check if partner is logged in
        if (!isset($_SESSION['partner_id'])) {
            header('Location: /login');
            exit;
        }

        // Check if partner's status is active by querying the database
        $partner = \Numok\Database\Database::query(
            "SELECT status FROM partners WHERE id = ? LIMIT 1",
            [$_SESSION['partner_id']]
        )->fetch();

        if (!$partner || $partner['status'] !== 'active') {
            // Clear session and redirect
            $_SESSION = [];
            if (isset($_COOKIE[session_name()])) {
                setcookie(session_name(), '', time() - 3600, '/');
            }
            session_destroy();
            
            header('Location: /login');
            exit;
        }
    }
}