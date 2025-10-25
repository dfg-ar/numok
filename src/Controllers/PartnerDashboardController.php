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

        // Get comprehensive stats
        $stats = $this->getStats($partnerId);
        
        // Get recent conversions
        $conversions = $this->getRecentConversions($partnerId);

        // Get active programs
        $programs = $this->getActivePrograms($partnerId);
        
        // Get earnings trends for chart
        $earningsTrends = $this->getEarningsTrends($partnerId);
        
        // Get program performance
        $programPerformance = $this->getProgramPerformance($partnerId);
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities($partnerId);
        
        // Get pending payouts
        $pendingPayouts = $this->getPendingPayouts($partnerId);

        $settings = $this->getSettings();
        $this->view('partner/dashboard/index', [
            'title' => 'Partner Dashboard - ' . ($settings['custom_app_name'] ?? 'Numok'),
            'stats' => $stats,
            'conversions' => $conversions,
            'programs' => $programs,
            'earnings_trends' => $earningsTrends,
            'program_performance' => $programPerformance,
            'recent_activities' => $recentActivities,
            'pending_payouts' => $pendingPayouts
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
             AND EXTRACT(MONTH FROM c.created_at) = EXTRACT(MONTH FROM CURRENT_DATE)
             AND EXTRACT(YEAR FROM c.created_at) = EXTRACT(YEAR FROM CURRENT_DATE)",
            [$partnerId]
        )->fetch();
        
        // Get last month's stats for comparison
        $lastMonthStats = Database::query(
            "SELECT
                COUNT(c.id) as conversions,
                COALESCE(SUM(c.amount), 0) as revenue,
                COALESCE(SUM(c.commission_amount), 0) as commission
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             WHERE pp.partner_id = ?
             AND EXTRACT(MONTH FROM c.created_at) = EXTRACT(MONTH FROM CURRENT_DATE - INTERVAL '1 month')
             AND EXTRACT(YEAR FROM c.created_at) = EXTRACT(YEAR FROM CURRENT_DATE - INTERVAL '1 month')",
            [$partnerId]
        )->fetch();

        // Get active programs count
        $programs = Database::query(
            "SELECT COUNT(*) as count 
             FROM partner_programs 
             WHERE partner_id = ? AND status = 'active'",
            [$partnerId]
        )->fetch();
        
        // Get pending commission
        $pendingCommission = Database::query(
            "SELECT COALESCE(SUM(c.commission_amount), 0) as total
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             WHERE pp.partner_id = ? AND c.status IN ('pending', 'payable')",
            [$partnerId]
        )->fetch();
        
        // Calculate trends
        $commissionChange = $lastMonthStats['commission'] > 0 
            ? (($monthlyStats['commission'] - $lastMonthStats['commission']) / $lastMonthStats['commission']) * 100 
            : ($monthlyStats['commission'] > 0 ? 100 : 0);
            
        $conversionsChange = $lastMonthStats['conversions'] > 0 
            ? (($monthlyStats['conversions'] - $lastMonthStats['conversions']) / $lastMonthStats['conversions']) * 100 
            : ($monthlyStats['conversions'] > 0 ? 100 : 0);

        return [
            'total_conversions' => $stats['total_conversions'] ?? 0,
            'total_revenue' => $stats['total_revenue'] ?? 0,
            'total_commission' => $stats['total_commission'] ?? 0,
            'monthly_conversions' => $monthlyStats['conversions'] ?? 0,
            'monthly_revenue' => $monthlyStats['revenue'] ?? 0,
            'monthly_commission' => $monthlyStats['commission'] ?? 0,
            'pending_commission' => $pendingCommission['total'] ?? 0,
            'active_programs' => $programs['count'] ?? 0,
            'commission_change' => round($commissionChange, 1),
            'conversions_change' => round($conversionsChange, 1),
            'last_month_commission' => $lastMonthStats['commission'] ?? 0,
            'last_month_conversions' => $lastMonthStats['conversions'] ?? 0
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
    
    private function getEarningsTrends(int $partnerId): array {
        return Database::query(
            "SELECT
                TO_CHAR(c.created_at, 'YYYY-MM') as month,
                COALESCE(SUM(c.commission_amount), 0) as earnings,
                COUNT(c.id) as conversions
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             WHERE pp.partner_id = ?
             AND c.created_at >= CURRENT_DATE - INTERVAL '6 months'
             GROUP BY TO_CHAR(c.created_at, 'YYYY-MM')
             ORDER BY month ASC",
            [$partnerId]
        )->fetchAll();
    }
    
    private function getProgramPerformance(int $partnerId): array {
        return Database::query(
            "SELECT 
                p.name as program_name,
                p.id as program_id,
                COUNT(c.id) as total_conversions,
                COALESCE(SUM(c.amount), 0) as total_revenue,
                COALESCE(SUM(c.commission_amount), 0) as total_commission,
                COALESCE(AVG(c.commission_amount), 0) as avg_commission
             FROM partner_programs pp
             JOIN programs p ON pp.program_id = p.id
             LEFT JOIN conversions c ON c.partner_program_id = pp.id
             WHERE pp.partner_id = ? AND pp.status = 'active'
             GROUP BY p.id, p.name
             ORDER BY total_commission DESC
             LIMIT 5",
            [$partnerId]
        )->fetchAll();
    }
    
    private function getRecentActivities(int $partnerId): array {
        $activities = [];
        
        // Recent conversions
        $recentConversions = Database::query(
            "SELECT c.amount, c.commission_amount, c.created_at, p.name as program_name, 'conversion' as type
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             JOIN programs p ON pp.program_id = p.id
             WHERE pp.partner_id = ? AND c.created_at >= NOW() - INTERVAL '7 days'
             ORDER BY c.created_at DESC
             LIMIT 5",
            [$partnerId]
        )->fetchAll();
        
        // Recent program joins
        $recentJoins = Database::query(
            "SELECT pp.created_at, p.name as program_name, 'program_join' as type
             FROM partner_programs pp
             JOIN programs p ON pp.program_id = p.id
             WHERE pp.partner_id = ? AND pp.created_at >= NOW() - INTERVAL '7 days'
             ORDER BY pp.created_at DESC
             LIMIT 3",
            [$partnerId]
        )->fetchAll();
        
        // Merge and sort activities
        $activities = array_merge($recentConversions, $recentJoins);
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($activities, 0, 8);
    }
    
    private function getPendingPayouts(int $partnerId): array {
        return Database::query(
            "SELECT 
                c.status,
                COUNT(c.id) as count,
                COALESCE(SUM(c.commission_amount), 0) as total_amount
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             WHERE pp.partner_id = ? AND c.status IN ('pending', 'payable')
             GROUP BY c.status",
            [$partnerId]
        )->fetchAll();
    }
}