<?php

namespace Numok\Controllers;

use Numok\Database\Database;
use Numok\Middleware\AuthMiddleware;

class SettingsController extends Controller {
    private string $stripeTestEndpoint = 'https://api.stripe.com/v1/customers';
    public function __construct() {
        AuthMiddleware::adminOnly();
    }

    public function index(): void {
        $settings = $this->getSettings();
        
        $this->view('settings/index', [
            'title' => 'Settings - Numok',
            'settings' => $settings,
            'success' => $_SESSION['settings_success'] ?? null,
            'error' => $_SESSION['settings_error'] ?? null
        ]);
        
        // Clear flash messages
        unset($_SESSION['settings_success'], $_SESSION['settings_error']);
    }

    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/settings');
            exit;
        }

        try {
            Database::transaction(function() {
                $settings = [
                    'app_name' => $_POST['app_name'] ?? 'Numok',
                    'partner_welcome_message' => $_POST['partner_welcome_message'] ?? '',
                    'stripe_secret_key' => $_POST['stripe_secret_key'] ?? '',
                    'stripe_webhook_secret' => $_POST['stripe_webhook_secret'] ?? ''
                ];

                foreach ($settings as $key => $value) {
                    Database::query(
                        "INSERT INTO settings (name, value) 
                         VALUES (?, ?) 
                         ON DUPLICATE KEY UPDATE value = VALUES(value)",
                        [$key, $value]
                    );
                }
            });

            $_SESSION['settings_success'] = 'Settings updated successfully.';
        } catch (\Exception $e) {
            $_SESSION['settings_error'] = 'Failed to update settings. Please try again.';
        }

        header('Location: /admin/settings');
        exit;
    }

    public function testConnection(): void {
        header('Content-Type: application/json');

        $settings = $this->getSettings();
        $response = ['success' => false, 'messages' => []];

        // Test Stripe API Key
        if (!empty($settings['stripe_secret_key'])) {
            $ch = curl_init($this->stripeTestEndpoint);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Authorization: Bearer ' . $settings['stripe_secret_key'],
                    'Stripe-Version: 2023-10-16'
                ]
            ]);
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200) {
                $response['messages'][] = [
                    'type' => 'success',
                    'text' => 'Successfully connected to Stripe API'
                ];
                $response['success'] = true;
            } elseif ($httpCode === 401) {
                $response['messages'][] = [
                    'type' => 'error',
                    'text' => 'Invalid Stripe API key'
                ];
            } else {
                $response['messages'][] = [
                    'type' => 'error',
                    'text' => 'Could not connect to Stripe API'
                ];
            }
        } else {
            $response['messages'][] = [
                'type' => 'warning',
                'text' => 'Stripe API key not configured'
            ];
        }

        // Test Webhook Secret
        if (!empty($settings['stripe_webhook_secret'])) {
            // Create a test signature using the webhook secret
            $timestamp = time();
            $payload = json_encode(['type' => 'test']);
            $signature = hash_hmac('sha256', "{$timestamp}.{$payload}", $settings['stripe_webhook_secret']);
            
            if ($signature) {
                $response['messages'][] = [
                    'type' => 'success',
                    'text' => 'Webhook secret is properly formatted'
                ];
                if (!$response['success']) {
                    $response['success'] = true;
                }
            }
        } else {
            $response['messages'][] = [
                'type' => 'warning',
                'text' => 'Webhook secret not configured'
            ];
        }

        echo json_encode($response);
        exit;
    }

    private function getSettings(): array {
        $stmt = Database::query("SELECT name, value FROM settings");
        $settings = [];
        
        while ($row = $stmt->fetch()) {
            $settings[$row['name']] = $row['value'];
        }

        return $settings;
    }

    public function updateProfile(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/settings');
            exit;
        }
    
        $userId = $_SESSION['user_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newEmail = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
    
        try {
            // Verify current user
            $user = Database::query(
                "SELECT * FROM users WHERE id = ? LIMIT 1",
                [$userId]
            )->fetch();
    
            if (!$user || !password_verify($currentPassword, $user['password'])) {
                $_SESSION['settings_error'] = 'Current password is incorrect';
                header('Location: /admin/settings');
                exit;
            }
    
            $updates = [];
            $params = [];
    
            // Handle email update
            if ($newEmail && $newEmail !== $user['email']) {
                // Check if email is already taken
                $existing = Database::query(
                    "SELECT id FROM users WHERE email = ? AND id != ? LIMIT 1",
                    [$newEmail, $userId]
                )->fetch();
    
                if ($existing) {
                    $_SESSION['settings_error'] = 'Email address is already in use';
                    header('Location: /admin/settings');
                    exit;
                }
    
                $updates[] = "email = ?";
                $params[] = $newEmail;
            }
    
            // Handle password update
            if ($newPassword) {
                if (strlen($newPassword) < 8) {
                    $_SESSION['settings_error'] = 'New password must be at least 8 characters long';
                    header('Location: /admin/settings');
                    exit;
                }
    
                if ($newPassword !== $confirmPassword) {
                    $_SESSION['settings_error'] = 'New passwords do not match';
                    header('Location: /admin/settings');
                    exit;
                }
    
                $updates[] = "password = ?";
                $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
            }
    
            // If there are updates to make
            if (!empty($updates)) {
                $params[] = $userId;
                Database::query(
                    "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?",
                    $params
                );
    
                // Update session if email changed
                if ($newEmail && $newEmail !== $user['email']) {
                    $_SESSION['user_email'] = $newEmail;
                }
    
                $_SESSION['settings_success'] = 'Profile updated successfully';
            }
    
        } catch (\Exception $e) {
            error_log("Profile update error: " . $e->getMessage());
            $_SESSION['settings_error'] = 'Failed to update profile. Please try again.';
        }
    
        header('Location: /admin/settings');
        exit;
    }
}