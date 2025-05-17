<?php
class User {
    private $db;

    public function __construct() {
        $this->db = new mysqli("localhost", "root", "toor", "eventpro");
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        return $result && password_verify($password, $result['password']);
    }

    public function register($email, $passwordHash) {
        // Check if user already exists
        $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO users (email, password) VALUES (?, ?)");
        $stmt->bind_param("ss", $email, $passwordHash);
        return $stmt->execute();
    }
}
