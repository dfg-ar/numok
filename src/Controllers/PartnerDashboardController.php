<?php

namespace Numok\Controllers;

use Numok\Database\Database;
use Numok\Middleware\PartnerMiddleware;

class PartnerDashboardController extends PartnerBaseController {
    public function __construct() {
        PartnerMiddleware::handle();
    }

    public function index(): void {
        $partnerId = $_SESSION['partner_id'];

        // Get basic stats
        $stats = $this->getStats($partnerId);
        
        // Get recent conversions
        $conversions = $this->getRecentConversions($partnerId);

        // Get active programs
        $programs = $this->getActivePrograms($partnerId);

        $settings = $this->getSettings();
        $this->view('partner/dashboard/index', [
            'title' => 'Partner Dashboard - ' . ($settings['custom_app_name'] ?? 'Numok'),
            'stats' => $stats,
            'conversions' => $conversions,
            'programs' => $programs
        ]);
    }

    private function getStats(int $partnerId): array {
        // Get total conversions and revenue
        $stats = Database::query(
            "SELECT 
                COUNT(c.id) as total_conversions,
                COALESCE(SUM(c.amount), 0) as total_revenue,
                COALESCE(SUM(c.commission_amount), 0) as total_commission
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             WHERE pp.partner_id = ?",
            [$partnerId]
        )->fetch();

        // Get this month's conversions
        $monthlyStats = Database::query(
            "SELECT 
                COUNT(c.id) as conversions,
                COALESCE(SUM(c.amount), 0) as revenue,
                COALESCE(SUM(c.commission_amount), 0) as commission
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             WHERE pp.partner_id = ?
             AND MONTH(c.created_at) = MONTH(CURRENT_DATE())
             AND YEAR(c.created_at) = YEAR(CURRENT_DATE())",
            [$partnerId]
        )->fetch();

        // Get active programs count
        $programs = Database::query(
            "SELECT COUNT(*) as count 
             FROM partner_programs 
             WHERE partner_id = ? AND status = 'active'",
            [$partnerId]
        )->fetch();

        return [
            'total_conversions' => $stats['total_conversions'] ?? 0,
            'total_revenue' => $stats['total_revenue'] ?? 0,
            'total_commission' => $stats['total_commission'] ?? 0,
            'monthly_conversions' => $monthlyStats['conversions'] ?? 0,
            'monthly_revenue' => $monthlyStats['revenue'] ?? 0,
            'monthly_commission' => $monthlyStats['commission'] ?? 0,
            'active_programs' => $programs['count'] ?? 0
        ];
    }

    private function getRecentConversions(int $partnerId): array {
        return Database::query(
            "SELECT c.*, p.name as program_name, pp.tracking_code
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             JOIN programs p ON pp.program_id = p.id
             WHERE pp.partner_id = ?
             ORDER BY c.created_at DESC
             LIMIT 5",
            [$partnerId]
        )->fetchAll();
    }

    private function getActivePrograms(int $partnerId): array {
        return Database::query(
            "SELECT pp.*, p.name as program_name, p.description,
                    p.commission_type, p.commission_value,
                    COUNT(c.id) as total_conversions,
                    COALESCE(SUM(c.amount), 0) as total_revenue,
                    COALESCE(SUM(c.commission_amount), 0) as total_commission
             FROM partner_programs pp
             JOIN programs p ON pp.program_id = p.id
             LEFT JOIN conversions c ON c.partner_program_id = pp.id
             WHERE pp.partner_id = ? AND pp.status = 'active'
             GROUP BY pp.id
             ORDER BY total_revenue DESC",
            [$partnerId]
        )->fetchAll();
    }
}