<?php

namespace Numok\Controllers;

use Numok\Database\Database;
use Numok\Middleware\AuthMiddleware;

class ConversionsController extends Controller {
    public function __construct() {
        AuthMiddleware::handle();
    }

    public function index(): void {
        // Get filter parameters
        $status = $_GET['status'] ?? 'all';
        $partnerId = intval($_GET['partner_id'] ?? 0);
        $programId = intval($_GET['program_id'] ?? 0);
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';

        // Build query conditions
        $conditions = [];
        $params = [];

        if ($status !== 'all') {
            $conditions[] = "c.status = ?";
            $params[] = $status;
        }

        if ($partnerId > 0) {
            $conditions[] = "p.id = ?";
            $params[] = $partnerId;
        }

        if ($programId > 0) {
            $conditions[] = "prog.id = ?";
            $params[] = $programId;
        }

        if ($startDate) {
            $conditions[] = "DATE(c.created_at) >= ?";
            $params[] = $startDate;
        }

        if ($endDate) {
            $conditions[] = "DATE(c.created_at) <= ?";
            $params[] = $endDate;
        }

        $whereClause = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

        // Get conversions with joins
        $conversions = Database::query(
            "SELECT c.*, 
                    p.company_name as partner_name,
                    prog.name as program_name,
                    pp.tracking_code
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             JOIN partners p ON pp.partner_id = p.id
             JOIN programs prog ON pp.program_id = prog.id
             {$whereClause}
             ORDER BY c.created_at DESC
             LIMIT 100",
            $params
        )->fetchAll();

        // Get all partners for filter
        $partners = Database::query(
            "SELECT id, company_name FROM partners WHERE status = 'active' ORDER BY company_name"
        )->fetchAll();

        // Get all programs for filter
        $programs = Database::query(
            "SELECT id, name FROM programs WHERE status = 'active' ORDER BY name"
        )->fetchAll();

        // Calculate totals
        $totals = [
            'count' => count($conversions),
            'amount' => array_sum(array_column($conversions, 'amount')),
            'commission' => array_sum(array_column($conversions, 'commission_amount'))
        ];

        $this->view('conversions/index', [
            'title' => 'Conversions - Numok',
            'conversions' => $conversions,
            'partners' => $partners,
            'programs' => $programs,
            'totals' => $totals,
            'filters' => [
                'status' => $status,
                'partner_id' => $partnerId,
                'program_id' => $programId,
                'start_date' => $startDate,
                'end_date' => $endDate
            ]
        ]);
    }

    public function updateStatus(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/conversions');
            exit;
        }

        $id = intval($_POST['id'] ?? 0);
        $status = $_POST['status'] ?? '';

        if (!$id || !in_array($status, ['pending', 'payable', 'rejected', 'paid'])) {
            $_SESSION['error'] = 'Invalid request parameters';
            header('Location: /admin/conversions');
            exit;
        }

        try {
            Database::update(
                'conversions',
                ['status' => $status],
                'id = ?',
                [$id]
            );

            $_SESSION['success'] = 'Conversion status updated successfully.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to update conversion status.';
        }

        header('Location: /admin/conversions');
        exit;
    }

    public function export(): void {
        // Set headers for CSV download
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="conversions.csv"');

        // Create output handle
        $output = fopen('php://output', 'w');

        // Add CSV headers
        fputcsv($output, [
            'Date',
            'Partner',
            'Program',
            'Tracking Code',
            'Customer Email',
            'Amount',
            'Commission',
            'Status'
        ]);

        // Get all conversions
        $conversions = Database::query(
            "SELECT 
                c.created_at,
                p.company_name as partner_name,
                prog.name as program_name,
                pp.tracking_code,
                c.customer_email,
                c.amount,
                c.commission_amount,
                c.status
             FROM conversions c
             JOIN partner_programs pp ON c.partner_program_id = pp.id
             JOIN partners p ON pp.partner_id = p.id
             JOIN programs prog ON pp.program_id = prog.id
             ORDER BY c.created_at DESC"
        )->fetchAll();

        // Add data rows
        foreach ($conversions as $conversion) {
            fputcsv($output, [
                date('Y-m-d H:i:s', strtotime($conversion['created_at'])),
                $conversion['partner_name'],
                $conversion['program_name'],
                $conversion['tracking_code'],
                $conversion['customer_email'],
                number_format($conversion['amount'], 2),
                number_format($conversion['commission_amount'], 2),
                ucfirst($conversion['status'])
            ]);
        }

        fclose($output);
        exit;
    }
}