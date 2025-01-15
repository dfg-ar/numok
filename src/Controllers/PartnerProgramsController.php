<?php

namespace Numok\Controllers;

use Numok\Database\Database;
use Numok\Middleware\PartnerMiddleware;

class PartnerProgramsController extends PartnerBaseController {
    public function __construct() {
        PartnerMiddleware::handle();
    }

    public function index(): void {
        $partnerId = $_SESSION['partner_id'];

        // Get all active programs
        $programs = Database::query(
            "SELECT p.*, 
                    CASE 
                        WHEN pp.id IS NOT NULL THEN 'joined'
                        ELSE 'available'
                    END as status,
                    pp.tracking_code
             FROM programs p
             LEFT JOIN partner_programs pp ON p.id = pp.program_id 
                AND pp.partner_id = ?
             WHERE p.status = 'active'
             ORDER BY p.name",
            [$partnerId]
        )->fetchAll();

        $this->view('partner/programs/index', [
            'title' => 'Available Programs - Numok',
            'programs' => $programs
        ]);
    }

    public function join(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /partner/programs');
            exit;
        }

        $partnerId = $_SESSION['partner_id'];
        $programId = $_POST['program_id'] ?? 0;

        // Validate program exists and is active
        $program = Database::query(
            "SELECT id FROM programs WHERE id = ? AND status = 'active'",
            [$programId]
        )->fetch();

        if (!$program) {
            $_SESSION['error'] = 'Invalid program selected';
            header('Location: /partner/programs');
            exit;
        }

        // Check if already joined
        $existing = Database::query(
            "SELECT id FROM partner_programs 
             WHERE partner_id = ? AND program_id = ?",
            [$partnerId, $programId]
        )->fetch();

        if ($existing) {
            $_SESSION['error'] = 'You have already joined this program';
            header('Location: /partner/programs');
            exit;
        }

        // Generate unique tracking code
        $trackingCode = bin2hex(random_bytes(8));

        try {
            Database::insert('partner_programs', [
                'partner_id' => $partnerId,
                'program_id' => $programId,
                'tracking_code' => $trackingCode,
                'status' => 'active'
            ]);

            $_SESSION['success'] = 'Successfully joined the program!';
        } catch (\Exception $e) {
            $_SESSION['error'] = 'Failed to join program. Please try again.';
        }

        header('Location: /partner/programs');
        exit;
    }
}