<?php

namespace Numok\Controllers;

use Numok\Database\Database;

class TrackingController extends Controller {
    public function script(int $programId): void {
        // Get program
        $program = Database::query(
            "SELECT * FROM programs WHERE id = ? AND status = 'active' LIMIT 1",
            [$programId]
        )->fetch();

        if (!$program) {
            header("HTTP/1.0 404 Not Found");
            echo "Program not found or inactive";
            exit;
        }

        // Set JavaScript content type
        header('Content-Type: application/javascript');
        
        // Set CORS headers to allow the script to be loaded from any domain
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET');
        
        // Cache control - might want to adjust this in production
        header('Cache-Control: public, max-age=3600'); // 1 hour cache
        header('Vary: Origin');

        // Get the script content
        $scriptPath = ROOT_PATH . '/public/assets/js/numok-tracking.js';
        if (!file_exists($scriptPath)) {
            header("HTTP/1.0 500 Internal Server Error");
            echo "Tracking script not found";
            exit;
        }

        // Output the script with program ID
        echo sprintf("const NUMOK_PROGRAM_ID = %d;\n", $programId);
        echo sprintf("const NUMOK_BASE_URL = '%s';\n", rtrim($settings['app_url'] ?? '', '/'));
        echo file_get_contents($scriptPath);
    }

    public function config(int $programId): void {
        // Get program settings
        $program = Database::query(
            "SELECT cookie_days FROM programs WHERE id = ? AND status = 'active' LIMIT 1",
            [$programId]
        )->fetch();

        if (!$program) {
            header("HTTP/1.0 404 Not Found");
            exit;
        }

        // Get tracking settings
        $settings = Database::query(
            "SELECT value FROM settings WHERE name = 'click_tracking_enabled'"
        )->fetch();

        // Format settings
        $config = [
            'cookie_days' => (int)$program['cookie_days'],
            'track_clicks' => !empty($settings['value'])
        ];

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode($config);
    }

    public function click(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("HTTP/1.0 405 Method Not Allowed");
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Get partner_program_id from tracking code
        $partnerProgram = Database::query(
            "SELECT id FROM partner_programs WHERE tracking_code = ? LIMIT 1",
            [$data['tracking_code']]
        )->fetch();

        if (!$partnerProgram) {
            header("HTTP/1.0 400 Bad Request");
            exit;
        }

        try {
            // Generate unique click ID
            $clickId = bin2hex(random_bytes(16));

            // Prepare sub_ids JSON
            $subIds = array_filter([
                'sid' => $data['sid'] ?? null,
                'sid2' => $data['sid2'] ?? null,
                'sid3' => $data['sid3'] ?? null
            ]);

            Database::insert('clicks', [
                'partner_program_id' => $partnerProgram['id'],
                'click_id' => $clickId,
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
                'referer' => $data['referrer'] ?? null,
                'sub_ids' => !empty($subIds) ? json_encode($subIds) : null
            ]);

            header("HTTP/1.1 201 Created");
        } catch (\Exception $e) {
            error_log("Click tracking error: " . $e->getMessage());
            header("HTTP/1.0 500 Internal Server Error");
        }
    }

    public function impression(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("HTTP/1.0 405 Method Not Allowed");
            exit;
        }

        $data = json_decode(file_get_contents('php://input'), true);

        // Validate required fields
        if (!isset($data['program_id'], $data['tracking_code'], $data['url'])) {
            header("HTTP/1.0 400 Bad Request");
            exit;
        }

        try {
            Database::insert('impressions', [
                'program_id' => $data['program_id'],
                'tracking_code' => $data['tracking_code'],
                'url' => $data['url'],
                'ip_address' => $_SERVER['REMOTE_ADDR'],
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
            ]);

            header("HTTP/1.1 201 Created");
        } catch (\Exception $e) {
            header("HTTP/1.0 500 Internal Server Error");
        }
    }
}