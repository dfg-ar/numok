<?php

namespace Numok\Controllers;

use Numok\Database\Database;
use Numok\Middleware\AuthMiddleware;

class PartnersController extends Controller {
    public function __construct() {
        AuthMiddleware::handle();
    }

    public function index(): void {
        $partners = Database::query(
            "SELECT p.*, 
                    COUNT(DISTINCT pp.program_id) as total_programs,
                    COUNT(DISTINCT c.id) as total_conversions,
                    COALESCE(SUM(c.amount), 0) as total_revenue,
                    COALESCE(SUM(c.commission_amount), 0) as total_commission
             FROM partners p
             LEFT JOIN partner_programs pp ON p.id = pp.partner_id
             LEFT JOIN conversions c ON pp.id = c.partner_program_id
             GROUP BY p.id
             ORDER BY p.created_at DESC"
        )->fetchAll();

        $this->view('partners/index', [
            'title' => 'Partners - Numok',
            'partners' => $partners
        ]);
    }

    public function create(): void {
        $this->view('partners/create', [
            'title' => 'Create Partner - Numok'
        ]);
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/partners');
            exit;
        }

        // Validate email
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $_SESSION['error'] = 'Valid email address is required';
            header('Location: /admin/partners/create');
            exit;
        }

        // Check if email already exists
        $existing = Database::query(
            "SELECT id FROM partners WHERE email = ?",
            [$email]
        )->fetch();

        if ($existing) {
            $_SESSION['error'] = 'A partner with this email already exists';
            header('Location: /admin/partners/create');
            exit;
        }

        $data = [
            'email' => $email,
            'company_name' => $_POST['company_name'] ?? '',
            'contact_name' => $_POST['contact_name'] ?? '',
            'payment_email' => $_POST['payment_email'] ?? $email,
            'status' => 'pending',
            'password' => password_hash($_POST['password'] ?? bin2hex(random_bytes(8)), PASSWORD_DEFAULT)
        ];

        try {
            Database::insert('partners', $data);
            $_SESSION['success'] = 'Partner created successfully.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to create partner. Please try again.';
        }

        header('Location: /admin/partners');
        exit;
    }

    public function edit(int $id): void {
        $partner = Database::query(
            "SELECT * FROM partners WHERE id = ? LIMIT 1",
            [$id]
        )->fetch();

        if (!$partner) {
            $_SESSION['error'] = 'Partner not found';
            header('Location: /admin/partners');
            exit;
        }

        // Get partner's programs
        $programs = Database::query(
            "SELECT pp.*, p.name as program_name, p.commission_type, p.commission_value
             FROM partner_programs pp
             JOIN programs p ON pp.program_id = p.id
             WHERE pp.partner_id = ?
             ORDER BY p.name",
            [$id]
        )->fetchAll();

        // Get available programs (not yet assigned)
        $availablePrograms = Database::query(
            "SELECT p.* 
             FROM programs p 
             WHERE p.status = 'active'
             AND p.id NOT IN (
                 SELECT program_id 
                 FROM partner_programs 
                 WHERE partner_id = ?
             )
             ORDER BY p.name",
            [$id]
        )->fetchAll();

        $this->view('partners/edit', [
            'title' => 'Edit Partner - Numok',
            'partner' => $partner,
            'programs' => $programs,
            'availablePrograms' => $availablePrograms
        ]);
    }

    public function update(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/partners');
            exit;
        }

        // Check if partner exists
        $partner = Database::query(
            "SELECT id FROM partners WHERE id = ? LIMIT 1",
            [$id]
        )->fetch();

        if (!$partner) {
            $_SESSION['error'] = 'Partner not found';
            header('Location: /admin/partners');
            exit;
        }

        $data = [
            'payment_email' => $_POST['payment_email'] ?? '',
            'status' => $_POST['status'] ?? 'pending'
        ];

        if (!empty($_POST['company_name']))
        {
            $data['company_name'] = $_POST['company_name'] ?? '';
        }
        
        if (!empty($_POST['contact_name']))
        {
            $data['contact_name'] = $_POST['contact_name'] ?? '';
        }

        try {
            Database::update('partners', $data, 'id = ?', [$id]);
            $_SESSION['success'] = 'Partner updated successfully.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to update partner. Please try again.';
        }

        header('Location: /admin/partners');
        exit;
    }

    public function delete(int $id): void {
        try {
            Database::query(
                "DELETE FROM partners WHERE id = ?",
                [$id]
            );
            $_SESSION['success'] = 'Partner deleted successfully.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to delete partner. Please try again.';
        }

        header('Location: /admin/partners');
        exit;
    }

    public function assignProgram(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/partners/' . $id . '/edit');
            exit;
        }

        $programId = $_POST['program_id'] ?? 0;
        
        // Validate program exists
        $program = Database::query(
            "SELECT id FROM programs WHERE id = ? AND status = 'active'",
            [$programId]
        )->fetch();

        if (!$program) {
            $_SESSION['error'] = 'Invalid program selected';
            header('Location: /admin/partners/' . $id . '/edit');
            exit;
        }

        // Generate unique tracking code
        $trackingCode = bin2hex(random_bytes(8));

        try {
            Database::insert('partner_programs', [
                'partner_id' => $id,
                'program_id' => $programId,
                'tracking_code' => $trackingCode,
                'status' => 'active'
            ]);
            $_SESSION['success'] = 'Program assigned successfully.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to assign program. Please try again.';
        }

        header('Location: /admin/partners/' . $id . '/edit');
        exit;
    }
}