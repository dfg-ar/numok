<?php

namespace Numok\Controllers;

use Numok\Database\Database;

class AuthController extends Controller {
    public function index(): void {
        // If already logged in, redirect to dashboard
        if ($this->isLoggedIn()) {
            header('Location: /admin/dashboard');
            exit;
        }
        
        $error = $_SESSION['login_error'] ?? '';
        unset($_SESSION['login_error']);
        
        $settings = $this->getSettings();
        $this->view('auth/login', [
            'title' => 'Login - ' . ($settings['custom_app_name'] ?? 'Numok'),
            'error' => $error
        ]);
    }

    public function login(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/login');
            exit;
        }

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['login_error'] = 'Email and password are required';
            header('Location: /admin/login');
            exit;
        }
        
        $stmt = Database::query(
            "SELECT * FROM users WHERE email = ? LIMIT 1", 
            [$email]
        );
        
        $user = $stmt->fetch();

        if (!$user) {
            $_SESSION['login_error'] = 'User not found';
            header('Location: /admin/login');
            exit;
        }

        if (!password_verify($password, $user['password'])) {
            $_SESSION['login_error'] = 'Invalid password';
            header('Location: /admin/login');
            exit;
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['is_admin'] = $user['is_admin'];
        
        // Regenerate session ID for security
        session_regenerate_id(true);
        
        header('Location: /admin/dashboard');
        exit;
    }

    public function logout(): void {
        // Clear session
        $_SESSION = [];
        
        // Clear session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        // Destroy session
        session_destroy();
        
        header('Location: /admin/login');
        exit;
    }

    private function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }
}