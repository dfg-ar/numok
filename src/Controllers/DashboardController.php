<?php

namespace Numok\Controllers;

use Numok\Middleware\AuthMiddleware;
use Numok\Database\Database;

class DashboardController extends Controller {
    public function __construct() {
        AuthMiddleware::handle();
    }

    public function index(): void {
        // Get basic stats
        $stats = $this->getStats();
        
        // Get recent conversions
        $conversions = $this->getRecentConversions();

        $settings = $this->getSettings();
        $this->view('dashboard/index', [
            'title' => 'Dashboard - ' . ($settings['custom_app_name'] ?? 'Numok'),
            'user_name' => $_SESSION['user_name'],
            'stats' => $stats,
            'recent_conversions' => $conversions
        ]);
    }

    private function getStats(): array {
        // Get total partners
        $partners = Database::query("SELECT COUNT(*) as count FROM partners WHERE status = 'active'")->fetch();
        
        // Get active programs
        $programs = Database::query("SELECT COUNT(*) as count FROM programs WHERE status = 'active'")->fetch();
        
        // Get this month's revenue
        $revenue = Database::query(
            "SELECT COALESCE(SUM(amount), 0) as total FROM conversions 
             WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) 
             AND YEAR(created_at) = YEAR(CURRENT_DATE())"
        )->fetch();

        return [
            'total_partners' => $partners['count'],
            'active_programs' => $programs['count'],
            'monthly_revenue' => $revenue['total']
        ];
    }

    private function getRecentConversions(): array {
        return Database::query(
            "SELECT c.*, p.company_name as partner_name
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             JOIN partners p ON pp.partner_id = p.id
             ORDER BY c.created_at DESC
             LIMIT 5"
        )->fetchAll();
    }
}