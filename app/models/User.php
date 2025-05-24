<?php
// Report all MySQLi errors
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class User {
    private $db;

    public function __construct() {
        $this->db = new mysqli("localhost", "root", "toor", "eventpro");
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return false;
        }
        $stmt->bind_param("s", $email);
        $stmt->execute();
    
        // Use get_result() to fetch associative array
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
    
        if ($user && password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            return true;
        }
    
        return false;
    }


   public function register($name, $email, $password) {
    // Check if email already exists
    $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        return false;
    }

    // Insert new user
    $stmt = $this->db->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
    return $stmt->execute([$name, $email, $password]);
}
}
