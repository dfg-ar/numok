<?php

namespace Numok\Controllers;

use Numok\Database\Database;
use Numok\Middleware\AuthMiddleware;
use Numok\Services\ProgramScriptGenerator;

class ProgramsController extends Controller {
    public function __construct() {
        AuthMiddleware::handle();
    }

    public function index(): void {
        $programs = Database::query(
            "SELECT p.*, 
                    COUNT(DISTINCT pp.partner_id) as total_partners,
                    COUNT(DISTINCT c.id) as total_conversions,
                    COALESCE(SUM(c.amount), 0) as total_revenue
             FROM programs p
             LEFT JOIN partner_programs pp ON p.id = pp.program_id
             LEFT JOIN conversions c ON pp.id = c.partner_program_id
             GROUP BY p.id
             ORDER BY p.created_at DESC"
        )->fetchAll();

        $this->view('programs/index', [
            'title' => 'Programs - Numok',
            'programs' => $programs
        ]);
    }

    public function create(): void {
        $this->view('programs/create', [
            'title' => 'Create Program - Numok'
        ]);
    }

    public function store(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/programs');
            exit;
        }
    
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'terms' => $_POST['terms'] ?? null,
            'commission_type' => $_POST['commission_type'] ?? 'percentage',
            'commission_value' => floatval($_POST['commission_value'] ?? 0),
            'cookie_days' => intval($_POST['cookie_days'] ?? 30),
            'is_recurring' => isset($_POST['is_recurring']) ? 1 : 0,
            'reward_days' => intval($_POST['reward_days'] ?? 0),
            'landing_page' => $_POST['landing_page'] ?? '',
            'status' => 'active'
        ];
    
        try {
            Database::transaction(function() use ($data) {
                // Insert the program
                $id = Database::insert('programs', $data);
                
                // Get the created program
                $program = Database::query(
                    "SELECT * FROM programs WHERE id = ?",
                    [$id]
                )->fetch();
                
                // Generate tracking script
                ProgramScriptGenerator::generate($program, $_SERVER['HTTP_HOST']);
            });
    
            $_SESSION['success'] = 'Program created successfully.';
        } catch (\Exception $e) {
            error_log("Failed to create program: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to create program. Please try again.';
        }
    
        header('Location: /admin/programs');
        exit;
    }

    public function edit(int $id): void {
        $program = Database::query(
            "SELECT * FROM programs WHERE id = ? LIMIT 1",
            [$id]
        )->fetch();

        if (!$program) {
            $_SESSION['error'] = 'Program not found';
            header('Location: /admin/programs');
            exit;
        }

        $this->view('programs/edit', [
            'title' => 'Edit Program - Numok',
            'program' => $program
        ]);
    }

    public function integration(int $id): void {
        $program = Database::query(
            "SELECT * FROM programs WHERE id = ? AND status = 'active' LIMIT 1",
            [$id]
        )->fetch();

        if (!$program) {
            $_SESSION['error'] = 'Program not found or inactive';
            header('Location: /admin/programs');
            exit;
        }

        // Get settings for the app URL
        $settings = Database::query("SELECT * FROM settings WHERE name = 'app_url' LIMIT 1")->fetch();

        $this->view('programs/integration', [
            'title' => 'Integration Guide - Numok',
            'program' => $program,
            'settings' => $settings
        ]);
    }

    public function update(int $id): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/programs');
            exit;
        }

        // First check if program exists
        $program = Database::query(
            "SELECT id FROM programs WHERE id = ? LIMIT 1",
            [$id]
        )->fetch();

        if (!$program) {
            $_SESSION['error'] = 'Program not found';
            header('Location: /admin/programs');
            exit;
        }

        // Prepare data for update
        $data = [
            'name' => $_POST['name'] ?? '',
            'description' => $_POST['description'] ?? '',
            'terms' => $_POST['terms'] ?? null,
            'commission_type' => $_POST['commission_type'] ?? 'percentage',
            'commission_value' => floatval($_POST['commission_value'] ?? 0),
            'cookie_days' => intval($_POST['cookie_days'] ?? 30),
            'is_recurring' => isset($_POST['is_recurring']) ? 1 : 0,
            'reward_days' => intval($_POST['reward_days'] ?? 0),
            'landing_page' => $_POST['landing_page'] ?? '',
            'status' => $_POST['status'] ?? 'active'
        ];

        try {
            Database::transaction(function() use ($id, $data) {
                // Update program
                Database::update('programs', $data, 'id = ?', [$id]);
                
                // Get updated program data for script generation
                $program = Database::query(
                    "SELECT * FROM programs WHERE id = ?",
                    [$id]
                )->fetch();
                
                // Generate tracking script
                ProgramScriptGenerator::generate($program, $_SERVER['HTTP_HOST']);
            });

            $_SESSION['success'] = 'Program updated successfully.';
        } catch (\Exception $e) {
            error_log("Failed to update program: " . $e->getMessage());
            $_SESSION['error'] = 'Failed to update program. Please try again.';
        }

        header('Location: /admin/programs');
        exit;
    }

    public function delete(int $id): void {
        try {
            Database::query(
                "DELETE FROM programs WHERE id = ?",
                [$id]
            );
            $_SESSION['success'] = 'Program deleted successfully.';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to delete program. Please try again.';
        }

        header('Location: /admin/programs');
        exit;
    }
}