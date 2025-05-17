<?php
require_once '../app/core/Controller.php';

class AuthController extends Controller {

    
    public function loginView() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    
        if (isset($_SESSION['user_id'])) {
            header("Location: /index.php?url=events/index");
            exit();
        }
    
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    
        $this->view('auth/login', [
            'loginUrl' => '/auth/login',
            'loginActionUrl' => '/auth/login'
        ]);
    }
    
    
    public function registerView() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        $this->view('auth/register', [
            'registerActionUrl' => 'auth/register',
            'loginUrl' => 'auth/login'
        ]);
    }

    public function login() {
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            
            // Validate CSRF token
            // if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
            //     $error = "Invalid CSRF token";
            //     $this->view('auth/login', ['error' => $error]);
            //     return;
            // }

            // Input validation
            $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Please enter a valid email address";
            } elseif (empty($password)) {
                $error = "Please enter your password";
            } else {
                $userModel = $this->model('User');
                if ($userModel->login($email, $password)) {
                    header("Location: /index.php?url=events/index");
                    exit();
                } else {
                    $error = "Invalid credentials.";
                }
            }
        }

        $this->view('auth/login', ['error' => $error]);
    }

    public function register() {
    $error = '';
     
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Validate CSRF token
        // if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        //     $error = "Invalid CSRF token";
        //     $this->view('auth/register', ['error' => $error]);
        //     return;
        // }

        // Input validation and sanitization
        $name = trim(filter_var($_POST['name'] ?? '', FILTER_SANITIZE_STRING));
        $email = filter_var($_POST['email'] ?? '', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validate inputs
        if (empty($name)) {
            $error = "Please enter your full name";
        } elseif (strlen($name) > 100) {
            $error = "Name is too long";
        } elseif (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "Please enter a valid email address";
        } elseif (empty($password) || strlen($password) < 8) {
            $error = "Password must be at least 8 characters";
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
            $error = "Password must contain at least one uppercase letter, one lowercase letter, one number and one special character";
        } elseif ($password !== $confirm_password) {
            $error = "Passwords do not match";
        } else {
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
            $userModel = $this->model('User');
            
            // Make sure to pass parameters in correct order
            if ($userModel->register($name, $email, $hashedPassword)) {
                header("Location: /index.php?url=auth/login");
                exit;
            } else {
                $error = "Registration failed. Email may already be in use.";
            }
        }
    }

    $this->view('auth/register', [
        'error' => $error,
        'name' => $name ?? '',
        'email' => $email ?? ''
    ]);
}
}