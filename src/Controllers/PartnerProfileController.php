<?php
// src/Controllers/PartnerProfileController.php

namespace Numok\Controllers;

use Numok\Database\Database;
use Numok\Middleware\PartnerMiddleware;

class PartnerProfileController extends PartnerBaseController {
    public function __construct() {
        PartnerMiddleware::handle();
    }

    public function index(): void {
        // Get current partner data
        $partner = Database::query(
            "SELECT * FROM partners WHERE id = ? LIMIT 1",
            [$_SESSION['partner_id']]
        )->fetch();

        $this->view('partner/settings/index', [
            'title' => 'Account Settings - Numok',
            'partner' => $partner
        ]);
    }

    public function update(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /settings');
            exit;
        }

        $partnerId = $_SESSION['partner_id'];
        $currentPassword = $_POST['current_password'] ?? '';
        $newEmail = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $paymentEmail = filter_var($_POST['payment_email'] ?? '', FILTER_VALIDATE_EMAIL);
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        try {
            // Verify current partner
            $partner = Database::query(
                "SELECT * FROM partners WHERE id = ? LIMIT 1",
                [$partnerId]
            )->fetch();

            if (!$partner || !password_verify($currentPassword, $partner['password'])) {
                $_SESSION['settings_error'] = 'Current password is incorrect';
                header('Location: /settings');
                exit;
            }

            $updates = [];
            $params = [];

            // Handle email update
            if ($newEmail && $newEmail !== $partner['email']) {
                // Check if email is already taken
                $existing = Database::query(
                    "SELECT id FROM partners WHERE email = ? AND id != ? LIMIT 1",
                    [$newEmail, $partnerId]
                )->fetch();

                if ($existing) {
                    $_SESSION['settings_error'] = 'Email address is already in use';
                    header('Location: /settings');
                    exit;
                }

                $updates[] = "email = ?";
                $params[] = $newEmail;
            }

            // Handle payment email update
            if ($paymentEmail && $paymentEmail !== $partner['payment_email']) {
                $updates[] = "payment_email = ?";
                $params[] = $paymentEmail;
            }

            // Handle password update
            if ($newPassword) {
                if (strlen($newPassword) < 8) {
                    $_SESSION['settings_error'] = 'New password must be at least 8 characters long';
                    header('Location: /settings');
                    exit;
                }

                if ($newPassword !== $confirmPassword) {
                    $_SESSION['settings_error'] = 'New passwords do not match';
                    header('Location: /settings');
                    exit;
                }

                $updates[] = "password = ?";
                $params[] = password_hash($newPassword, PASSWORD_DEFAULT);
            }

            // If there are updates to make
            if (!empty($updates)) {
                $params[] = $partnerId;
                Database::query(
                    "UPDATE partners SET " . implode(', ', $updates) . " WHERE id = ?",
                    $params
                );

                // Update session if email changed
                if ($newEmail && $newEmail !== $partner['email']) {
                    $_SESSION['partner_email'] = $newEmail;
                }

                $_SESSION['settings_success'] = 'Profile updated successfully';
            }

        } catch (\Exception $e) {
            error_log("Partner profile update error: " . $e->getMessage());
            $_SESSION['settings_error'] = 'Failed to update profile. Please try again.';
        }

        header('Location: /settings');
        exit;
    }
}