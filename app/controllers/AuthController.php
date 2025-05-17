<?php
require_once '../app/core/Controller.php';

class AuthController extends Controller {

    public function login() {
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = $_POST['password'];

            $userModel = $this->model('User');
            if ($userModel->login($email, $password)) {
                header("Location: /event/index");
                exit;
            } else {
                $error = "Invalid credentials.";
            }
        }

        $this->view('auth/login', ['error' => $error]);
    }

    public function register() {
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'];
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

            $userModel = $this->model('User');
            if ($userModel->register($email, $password)) {
                header("Location: /auth/login");
                exit;
            } else {
                $error = "User already exists or registration failed.";
            }
        }

        $this->view('auth/register', ['error' => $error]);
    }
}
