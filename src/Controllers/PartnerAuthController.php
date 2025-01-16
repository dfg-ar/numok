<?php

namespace Numok\Controllers;

use Numok\Database\Database;

class PartnerAuthController extends PartnerBaseController {
    public function index(): void {
        // If already logged in, redirect to partner dashboard
        if ($this->isLoggedIn()) {
            header('Location: /dashboard');
            exit;
        }
        
        $error = $_SESSION['login_error'] ?? '';
        unset($_SESSION['login_error']);
        
        $this->view('partner/auth/login', [
            'title' => 'Partner Login - Numok',
            'error' => $error
        ]);
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Email and password are required';
            header('Location: /login');
            exit;
        }
        
        $partner = Database::query(
            "SELECT * FROM partners WHERE email = ? AND status != 'rejected' LIMIT 1", 
            [$email]
        )->fetch();

        if (!$partner) {
            $_SESSION['login_error'] = 'Account not found';
            header('Location: /login');
            exit;
        }

        if (!password_verify($password, $partner['password'])) {
            $_SESSION['login_error'] = 'Invalid password';
            header('Location: /login');
            exit;
        }

        if ($partner['status'] === 'pending') {
            $_SESSION['login_error'] = 'Your account is pending approval';
            header('Location: /login');
            exit;
        }

        if ($partner['status'] === 'suspended') {
            $_SESSION['login_error'] = 'Your account has been suspended';
            header('Location: /login');
            exit;
        }

        // Set partner session
        $_SESSION['partner_id'] = $partner['id'];
        $_SESSION['partner_email'] = $partner['email'];
        $_SESSION['partner_company'] = $partner['company_name'];
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        header('Location: /dashboard');
        exit;
    }

    public function register(): void {
        $this->view('partner/auth/register', [
            'title' => 'Register - Numok'
        ]);
    }

    public function store(): void {
        // Debug logging
        error_log('Partner registration attempt - POST data: ' . print_r($_POST, true));
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /register');
            exit;
        }

        // Validate required fields
        $required = ['email', 'password', 'company_name', 'contact_name'];
        foreach ($required as $field) {
            if (empty($_POST[$field])) {
                $_SESSION['register_error'] = 'All fields are required';
                header('Location: /register');
                exit;
            }
        }

        // Validate email
        $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
        if (!$email) {
            $_SESSION['register_error'] = 'Valid email address is required';
            header('Location: /register');
            exit;
        }

        // Check if email already exists
        $existing = Database::query(
            "SELECT id FROM partners WHERE email = ?",
            [$email]
        )->fetch();

        if ($existing) {
            $_SESSION['register_error'] = 'This email is already registered';
            header('Location: /register');
            exit;
        }

        try {
            // Sanitize inputs
            $companyName = trim($_POST['company_name']);
            $contactName = trim($_POST['contact_name']);
            
            if (empty($companyName) || empty($contactName)) {
                $_SESSION['register_error'] = 'Company name and contact name are required';
                header('Location: /register');
                exit;
            }
            
            Database::insert('partners', [
                'email' => $email,
                'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                'company_name' => $companyName,
                'contact_name' => $contactName,
                'payment_email' => $email,
                'status' => 'active'  // Automatically activate partners
            ]);
            
            // Log successful registration
            error_log("Partner registered successfully - Email: $email, Company: $companyName");

            $_SESSION['register_success'] = 'Registration successful!';
            header('Location: /login');
        } catch (\Exception $e) {
            $_SESSION['register_error'] = 'Registration failed. Please try again.';
            header('Location: /register');
        }
        exit;
    }

    public function logout(): void {
        // Clear session
        $_SESSION = [];
        
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
        
        header('Location: /login');
        exit;
    }

    private function isLoggedIn(): bool {
        return isset($_SESSION['partner_id']);
    }
}