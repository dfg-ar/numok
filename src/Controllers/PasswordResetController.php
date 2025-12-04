<?php

namespace Numok\Controllers;

use Numok\Database\Database;
use Numok\Services\EmailService;

class PasswordResetController extends Controller
{

    public function showLinkRequestForm(): void
    {
        $settings = $this->getSettings();
        $this->view('auth/passwords/email', [
            'title' => 'Reset Password - ' . ($settings['custom_app_name'] ?? 'Numok')
        ]);
    }

    public function sendResetLinkEmail(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /forgot-password');
            exit;
        }

        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $_SESSION['status'] = 'Please provide a valid email address.';
            header('Location: /forgot-password');
            exit;
        }

        // Check if user exists (partner or admin)
        // For simplicity, we'll check partners first, then users (admins)
        // Or maybe we should have separate reset flows? 
        // The requirement says "password reset features", implying for partners mostly based on context, 
        // but likely both. Let's check partners first as that's the main focus.

        $partner = Database::query("SELECT id FROM partners WHERE email = ?", [$email])->fetch();
        $user = Database::query("SELECT id FROM users WHERE email = ?", [$email])->fetch();

        if (!$partner && !$user) {
            // Don't reveal if user exists
            $_SESSION['status'] = 'If an account exists with this email, a password reset link has been sent.';
            header('Location: /forgot-password');
            exit;
        }

        $token = bin2hex(random_bytes(32));

        // Store token
        Database::query("DELETE FROM password_resets WHERE email = ?", [$email]);
        Database::insert('password_resets', [
            'email' => $email,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Send email
        $config = require ROOT_PATH . '/config/config.php';
        $resetLink = $config['app']['url'] . "/password/reset/" . $token;

        $emailService = new EmailService();
        $emailService->sendPasswordResetEmail($email, $resetLink);

        $_SESSION['status'] = 'If an account exists with this email, a password reset link has been sent.';
        header('Location: /forgot-password');
        exit;
    }

    public function showResetForm(string $token): void
    {
        $record = Database::query(
            "SELECT * FROM password_resets WHERE token = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            [$token]
        )->fetch();

        if (!$record) {
            $_SESSION['error'] = 'This password reset token is invalid or has expired.';
            header('Location: /forgot-password');
            exit;
        }

        $settings = $this->getSettings();
        $this->view('auth/passwords/reset', [
            'title' => 'Reset Password - ' . ($settings['custom_app_name'] ?? 'Numok'),
            'token' => $token,
            'email' => $record['email']
        ]);
    }

    public function reset(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /forgot-password');
            exit;
        }

        $token = $_POST['token'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $passwordConfirmation = $_POST['password_confirmation'] ?? '';

        if (empty($password) || $password !== $passwordConfirmation) {
            $_SESSION['error'] = 'Passwords do not match.';
            header('Location: /password/reset/' . $token);
            exit;
        }

        $record = Database::query(
            "SELECT * FROM password_resets WHERE email = ? AND token = ? AND created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)",
            [$email, $token]
        )->fetch();

        if (!$record) {
            $_SESSION['error'] = 'This password reset token is invalid or has expired.';
            header('Location: /forgot-password');
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Update partner
        $partner = Database::query("SELECT id FROM partners WHERE email = ?", [$email])->fetch();
        if ($partner) {
            Database::update('partners', ['password' => $hashedPassword], 'id = ?', [$partner['id']]);
        }

        // Update user (admin)
        $user = Database::query("SELECT id FROM users WHERE email = ?", [$email])->fetch();
        if ($user) {
            Database::update('users', ['password' => $hashedPassword], 'id = ?', [$user['id']]);
        }

        // Delete token
        Database::query("DELETE FROM password_resets WHERE email = ?", [$email]);

        $_SESSION['success'] = 'Your password has been reset!';
        header('Location: /login');
        exit;
    }
}
