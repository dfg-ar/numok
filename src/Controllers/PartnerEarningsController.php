<?php

namespace Numok\Controllers;

use Numok\Database\Database;
use Numok\Middleware\PartnerMiddleware;

class PartnerEarningsController extends PartnerBaseController {
    public function __construct() {
        PartnerMiddleware::handle();
    }

    public function index(): void {
        $partnerId = $_SESSION['partner_id'];
        
        // Get filter parameters
        $status = $_GET['status'] ?? 'all';
        $program = $_GET['program'] ?? 'all';
        $period = $_GET['period'] ?? '30';
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        // Get earnings summary
        $summary = $this->getEarningsSummary($partnerId, $status, $program, $period);
        
        // Get detailed conversions with filters
        $conversions = $this->getConversions($partnerId, $status, $program, $period, $perPage, $offset);
        
        // Get total count for pagination
        $totalCount = $this->getConversionsCount($partnerId, $status, $program, $period);
        
        // Get available programs for filter
        $programs = $this->getPartnerPrograms($partnerId);
        
        // Get monthly earnings for chart
        $monthlyEarnings = $this->getMonthlyEarnings($partnerId, $period);
        
        // Calculate pagination
        $totalPages = ceil($totalCount / $perPage);

        $settings = $this->getSettings();
        $this->view('partner/earnings/index', [
            'title' => 'Earnings - ' . ($settings['custom_app_name'] ?? 'Numok'),
            'summary' => $summary,
            'conversions' => $conversions,
            'programs' => $programs,
            'monthly_earnings' => $monthlyEarnings,
            'filters' => [
                'status' => $status,
                'program' => $program,
                'period' => $period
            ],
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total_count' => $totalCount,
                'per_page' => $perPage
            ]
        ]);
    }

    private function getEarningsSummary(int $partnerId, string $status, string $program, string $period): array {
        $whereConditions = ["pp.partner_id = ?"];
        $params = [$partnerId];
        
        // Add status filter
        if ($status !== 'all') {
            $whereConditions[] = "c.status = ?";
            $params[] = $status;
        }
        
        // Add program filter
        if ($program !== 'all') {
            $whereConditions[] = "pp.program_id = ?";
            $params[] = $program;
        }
        
        // Add period filter
        if ($period !== 'all') {
            $whereConditions[] = "c.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
            $params[] = $period;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        $summary = Database::query(
            "SELECT 
                COUNT(c.id) as total_conversions,
                COALESCE(SUM(c.amount), 0) as total_revenue,
                COALESCE(SUM(c.commission_amount), 0) as total_commission,
                COALESCE(AVG(c.commission_amount), 0) as avg_commission,
                COUNT(CASE WHEN c.status = 'pending' THEN 1 END) as pending_count,
                COUNT(CASE WHEN c.status = 'payable' THEN 1 END) as payable_count,
                COUNT(CASE WHEN c.status = 'paid' THEN 1 END) as paid_count,
                COALESCE(SUM(CASE WHEN c.status = 'pending' THEN c.commission_amount END), 0) as pending_amount,
                COALESCE(SUM(CASE WHEN c.status = 'payable' THEN c.commission_amount END), 0) as payable_amount,
                COALESCE(SUM(CASE WHEN c.status = 'paid' THEN c.commission_amount END), 0) as paid_amount
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             WHERE {$whereClause}",
            $params
        )->fetch();

        return $summary ?: [
            'total_conversions' => 0,
            'total_revenue' => 0,
            'total_commission' => 0,
            'avg_commission' => 0,
            'pending_count' => 0,
            'payable_count' => 0,
            'paid_count' => 0,
            'pending_amount' => 0,
            'payable_amount' => 0,
            'paid_amount' => 0
        ];
    }

    private function getConversions(int $partnerId, string $status, string $program, string $period, int $limit, int $offset): array {
        $whereConditions = ["pp.partner_id = ?"];
        $params = [$partnerId];
        
        // Add status filter
        if ($status !== 'all') {
            $whereConditions[] = "c.status = ?";
            $params[] = $status;
        }
        
        // Add program filter
        if ($program !== 'all') {
            $whereConditions[] = "pp.program_id = ?";
            $params[] = $program;
        }
        
        // Add period filter
        if ($period !== 'all') {
            $whereConditions[] = "c.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
            $params[] = $period;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        $params[] = $limit;
        $params[] = $offset;
        
        return Database::query(
            "SELECT c.*, p.name as program_name, p.commission_type, p.commission_value,
                    pp.tracking_code
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             JOIN programs p ON pp.program_id = p.id
             WHERE {$whereClause}
             ORDER BY c.created_at DESC
             LIMIT ? OFFSET ?",
            $params
        )->fetchAll();
    }

    private function getConversionsCount(int $partnerId, string $status, string $program, string $period): int {
        $whereConditions = ["pp.partner_id = ?"];
        $params = [$partnerId];
        
        // Add status filter
        if ($status !== 'all') {
            $whereConditions[] = "c.status = ?";
            $params[] = $status;
        }
        
        // Add program filter
        if ($program !== 'all') {
            $whereConditions[] = "pp.program_id = ?";
            $params[] = $program;
        }
        
        // Add period filter
        if ($period !== 'all') {
            $whereConditions[] = "c.created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)";
            $params[] = $period;
        }
        
        $whereClause = implode(' AND ', $whereConditions);
        
        $result = Database::query(
            "SELECT COUNT(c.id) as count
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             WHERE {$whereClause}",
            $params
        )->fetch();

        return $result['count'] ?? 0;
    }

    private function getPartnerPrograms(int $partnerId): array {
        return Database::query(
            "SELECT p.id, p.name
             FROM programs p
             JOIN partner_programs pp ON p.id = pp.program_id
             WHERE pp.partner_id = ? AND pp.status = 'active'
             ORDER BY p.name",
            [$partnerId]
        )->fetchAll();
    }

    private function getMonthlyEarnings(int $partnerId, string $period): array {
        $months = $period === 'all' ? 12 : min(12, ceil($period / 30));
        
        return Database::query(
            "SELECT 
                DATE_FORMAT(c.created_at, '%Y-%m') as month,
                COALESCE(SUM(c.commission_amount), 0) as earnings,
                COUNT(c.id) as conversions
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             WHERE pp.partner_id = ? 
             AND c.created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL ? MONTH)
             GROUP BY DATE_FORMAT(c.created_at, '%Y-%m')
             ORDER BY month ASC",
            [$partnerId, $months]
        )->fetchAll();
    }
} 