<?php

namespace Numok\Services;

use Resend;

class EmailService
{
    private $resend;
    private $fromEmail;
    private $appName;

    public function __construct()
    {
        global $config;

        $apiKey = $config['email']['resend_api_key'] ?? getenv('RESEND_API_KEY');
        if (!$apiKey) {
            // Fallback or handle error - for now we might log or just let it fail when used
            // In a real app we might want to throw an exception if the service is required
        }
        $this->resend = Resend::client($apiKey);
        $this->fromEmail = $config['email']['from_address'] ?? getenv('MAIL_FROM_ADDRESS') ?: 'onboarding@resend.dev';
        $this->appName = $config['app']['name'] ?? getenv('APP_NAME') ?: 'Numok';
    }

    public function sendWelcomeEmail(string $to, string $name): void
    {
        try {
            $this->resend->emails->send([
                'from' => "{$this->appName} <{$this->fromEmail}>",
                'to' => [$to],
                'subject' => "Welcome to {$this->appName}",
                'html' => "
                    <h1>Welcome, {$name}!</h1>
                    <p>We are excited to have you on board at {$this->appName}.</p>
                    <p>You can now log in to your partner dashboard and start promoting our programs.</p>
                    <p>If you have any questions, feel free to reply to this email.</p>
                    <br>
                    <p>Best regards,</p>
                    <p>The {$this->appName} Team</p>
                ",
            ]);
        } catch (\Exception $e) {
            error_log("Failed to send welcome email to {$to}: " . $e->getMessage());
        }
    }

    public function sendPasswordResetEmail(string $to, string $resetLink): void
    {
        try {
            $this->resend->emails->send([
                'from' => "{$this->appName} <{$this->fromEmail}>",
                'to' => [$to],
                'subject' => "Reset Your Password",
                'html' => "
                    <h1>Password Reset Request</h1>
                    <p>We received a request to reset your password for your {$this->appName} account.</p>
                    <p>Click the link below to reset your password:</p>
                    <p><a href=\"{$resetLink}\">Reset Password</a></p>
                    <p>If you did not request this, please ignore this email.</p>
                    <p>This link will expire in 60 minutes.</p>
                    <br>
                    <p>Best regards,</p>
                    <p>The {$this->appName} Team</p>
                ",
            ]);
        } catch (\Exception $e) {
            error_log("Failed to send password reset email to {$to}: " . $e->getMessage());
        }
    }
}
