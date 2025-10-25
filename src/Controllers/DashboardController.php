<?php

namespace Numok\Controllers;

use Numok\Middleware\AuthMiddleware;
use Numok\Database\Database;

class DashboardController extends Controller {
    public function __construct() {
        AuthMiddleware::handle();
    }

    public function index(): void {
        // Get comprehensive stats
        $stats = $this->getStats();
        
        // Get recent conversions
        $conversions = $this->getRecentConversions();
        
        // Get revenue trends for chart
        $revenueTrends = $this->getRevenueTrends();
        
        // Get top performing partners
        $topPartners = $this->getTopPartners();
        
        // Get top performing programs
        $topPrograms = $this->getTopPrograms();
        
        // Get recent activities
        $recentActivities = $this->getRecentActivities();

        $settings = $this->getSettings();
        $this->view('dashboard/index', [
            'title' => 'Dashboard - ' . ($settings['custom_app_name'] ?? 'Numok'),
            'user_name' => $_SESSION['user_name'],
            'stats' => $stats,
            'recent_conversions' => $conversions,
            'revenue_trends' => $revenueTrends,
            'top_partners' => $topPartners,
            'top_programs' => $topPrograms,
            'recent_activities' => $recentActivities
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
             WHERE EXTRACT(MONTH FROM created_at) = EXTRACT(MONTH FROM CURRENT_DATE)
             AND EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE)"
        )->fetch();
        
        // Get last month's revenue for comparison
        $lastMonthRevenue = Database::query(
            "SELECT COALESCE(SUM(amount), 0) as total FROM conversions
             WHERE EXTRACT(MONTH FROM created_at) = EXTRACT(MONTH FROM CURRENT_DATE - INTERVAL '1 month')
             AND EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE - INTERVAL '1 month')"
        )->fetch();
        
        // Get total conversions this month
        $monthlyConversions = Database::query(
            "SELECT COUNT(*) as count FROM conversions
             WHERE EXTRACT(MONTH FROM created_at) = EXTRACT(MONTH FROM CURRENT_DATE)
             AND EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE)"
        )->fetch();
        
        // Get last month's conversions for comparison
        $lastMonthConversions = Database::query(
            "SELECT COUNT(*) as count FROM conversions
             WHERE EXTRACT(MONTH FROM created_at) = EXTRACT(MONTH FROM CURRENT_DATE - INTERVAL '1 month')
             AND EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE - INTERVAL '1 month')"
        )->fetch();
        
        // Get total commission this month
        $monthlyCommission = Database::query(
            "SELECT COALESCE(SUM(commission_amount), 0) as total FROM conversions
             WHERE EXTRACT(MONTH FROM created_at) = EXTRACT(MONTH FROM CURRENT_DATE)
             AND EXTRACT(YEAR FROM created_at) = EXTRACT(YEAR FROM CURRENT_DATE)"
        )->fetch();
        
        // Get pending payouts
        $pendingPayouts = Database::query(
            "SELECT COALESCE(SUM(commission_amount), 0) as total FROM conversions 
             WHERE status IN ('pending', 'payable')"
        )->fetch();
        
        // Calculate trends
        $revenueChange = $lastMonthRevenue['total'] > 0 
            ? (($revenue['total'] - $lastMonthRevenue['total']) / $lastMonthRevenue['total']) * 100 
            : ($revenue['total'] > 0 ? 100 : 0);
            
        $conversionsChange = $lastMonthConversions['count'] > 0 
            ? (($monthlyConversions['count'] - $lastMonthConversions['count']) / $lastMonthConversions['count']) * 100 
            : ($monthlyConversions['count'] > 0 ? 100 : 0);

        return [
            'total_partners' => $partners['count'],
            'active_programs' => $programs['count'],
            'monthly_revenue' => $revenue['total'],
            'monthly_conversions' => $monthlyConversions['count'],
            'monthly_commission' => $monthlyCommission['total'],
            'pending_payouts' => $pendingPayouts['total'],
            'revenue_change' => round($revenueChange, 1),
            'conversions_change' => round($conversionsChange, 1),
            'last_month_revenue' => $lastMonthRevenue['total'],
            'last_month_conversions' => $lastMonthConversions['count']
        ];
    }

    private function getRecentConversions(): array {
        return Database::query(
            "SELECT c.*, p.company_name as partner_name, pr.name as program_name
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             JOIN partners p ON pp.partner_id = p.id
             JOIN programs pr ON pp.program_id = pr.id
             ORDER BY c.created_at DESC
             LIMIT 5"
        )->fetchAll();
    }
    
    private function getRevenueTrends(): array {
        return Database::query(
            "SELECT
                TO_CHAR(created_at, 'YYYY-MM') as month,
                COALESCE(SUM(amount), 0) as revenue,
                COUNT(*) as conversions
             FROM conversions
             WHERE created_at >= CURRENT_DATE - INTERVAL '6 months'
             GROUP BY TO_CHAR(created_at, 'YYYY-MM')
             ORDER BY month ASC"
        )->fetchAll();
    }
    
    private function getTopPartners(): array {
        return Database::query(
            "SELECT 
                p.company_name,
                p.id,
                COUNT(c.id) as total_conversions,
                COALESCE(SUM(c.amount), 0) as total_revenue,
                COALESCE(SUM(c.commission_amount), 0) as total_commission
             FROM partners p
             LEFT JOIN partner_programs pp ON p.id = pp.partner_id
             LEFT JOIN conversions c ON pp.id = c.partner_program_id
             WHERE p.status = 'active'
             GROUP BY p.id, p.company_name
             ORDER BY total_revenue DESC
             LIMIT 5"
        )->fetchAll();
    }
    
    private function getTopPrograms(): array {
        return Database::query(
            "SELECT 
                pr.name,
                pr.id,
                COUNT(DISTINCT pp.partner_id) as partner_count,
                COUNT(c.id) as total_conversions,
                COALESCE(SUM(c.amount), 0) as total_revenue
             FROM programs pr
             LEFT JOIN partner_programs pp ON pr.id = pp.program_id
             LEFT JOIN conversions c ON pp.id = c.partner_program_id
             WHERE pr.status = 'active'
             GROUP BY pr.id, pr.name
             ORDER BY total_revenue DESC
             LIMIT 5"
        )->fetchAll();
    }
    
    private function getRecentActivities(): array {
        $activities = [];
        
        // Recent partner signups
        $newPartners = Database::query(
            "SELECT company_name, created_at, 'partner_signup' as type
             FROM partners
             WHERE created_at >= NOW() - INTERVAL '7 days'
             ORDER BY created_at DESC
             LIMIT 3"
        )->fetchAll();
        
        // Recent program creations
        $newPrograms = Database::query(
            "SELECT name, created_at, 'program_created' as type
             FROM programs
             WHERE created_at >= NOW() - INTERVAL '7 days'
             ORDER BY created_at DESC
             LIMIT 3"
        )->fetchAll();
        
        // Recent high-value conversions
        $bigConversions = Database::query(
            "SELECT c.amount, c.created_at, p.company_name, 'big_conversion' as type
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             JOIN partners p ON pp.partner_id = p.id
             WHERE c.amount >= 100 AND c.created_at >= NOW() - INTERVAL '7 days'
             ORDER BY c.created_at DESC
             LIMIT 3"
        )->fetchAll();
        
        // Merge and sort activities
        $activities = array_merge($newPartners, $newPrograms, $bigConversions);
        usort($activities, function($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });
        
        return array_slice($activities, 0, 8);
    }
}