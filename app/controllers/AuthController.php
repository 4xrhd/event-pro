<?php
require_once __DIR__ . '/../core/Controller.php';

class AuthController extends Controller {
    public function loginView() {
        $this->ensureSession();

        if (isset($_SESSION['user_id'])) {
            redirect('events');
        }

        $this->view('auth/login', [
            'loginUrl' => url('auth/login'),
            'loginActionUrl' => url('auth/login')
        ]);
    }

    public function registerView() {
        $this->ensureSession();

        if (isset($_SESSION['user_id'])) {
            redirect('events');
        }

        $this->view('auth/register', [
            'registerActionUrl' => url('auth/register'),
            'loginUrl' => url('auth/login')
        ]);
    }

    public function login() {
        $this->ensureSession();
        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !isset($_POST['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'] ?? '', (string) $_POST['csrf_token'])
            ) {
                $error = "Invalid CSRF token";
                $this->view('auth/login', ['error' => $error]);
                return;
            }

            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "Please enter a valid email address";
            } elseif (empty($password)) {
                $error = "Please enter your password";
            } else {
                $userModel = $this->model('User');
                if ($userModel->login($email, $password)) {
                    $redirectUrl = $_SESSION['redirect_after_login'] ?? url('events');
                    unset($_SESSION['redirect_after_login']);

                    header("Location: " . $redirectUrl);
                    exit();
                }

                $error = "Invalid credentials.";
            }
        }

        $this->view('auth/login', ['error' => $error]);
    }

    public function register() {
        $this->ensureSession();
        $error = '';
        $name = '';
        $email = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (
                !isset($_POST['csrf_token']) ||
                !hash_equals($_SESSION['csrf_token'] ?? '', (string) $_POST['csrf_token'])
            ) {
                $error = "Invalid CSRF token";
                $this->view('auth/register', ['error' => $error]);
                return;
            }

            $name = trim(strip_tags($_POST['name'] ?? ''));
            $email = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

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
            } elseif ($password !== $confirmPassword) {
                $error = "Passwords do not match";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $userModel = $this->model('User');

                if ($userModel->register($name, $email, $hashedPassword)) {
                    redirect('auth/login');
                }

                $error = "Registration failed. Email may already be in use.";
            }
        }

        $this->view('auth/register', [
            'error' => $error,
            'name' => $name,
            'email' => $email,
        ]);
    }

    public function logout() {
        $this->ensureSession();

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        redirect('auth/login');
    }

    private function ensureSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}
