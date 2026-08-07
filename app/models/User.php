<?php
require_once __DIR__ . '/../core/Model.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class User extends Model {
    protected $table = 'users';
    private $columns;

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if (!$user || !password_verify($password, $user['password'])) {
            return false;
        }

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'] ?? null;

        return true;
    }

    public function register($name, $email, $password) {
        $existingUser = $this->db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        if (!$existingUser) {
            error_log("Prepare failed: " . $this->db->error);
            return false;
        }

        $existingUser->bind_param("s", $email);
        $existingUser->execute();
        if ($existingUser->get_result()->fetch_assoc()) {
            return false;
        }

        $columns = ['name', 'email', 'password'];
        $placeholders = ['?', '?', '?'];
        $types = 'sss';
        $values = [$name, $email, $password];

        if ($this->hasColumn('role')) {
            $columns[] = 'role';
            $placeholders[] = '?';
            $types .= 's';
            $values[] = 'customer';
        }

        $stmt = $this->db->prepare(
            sprintf(
                "INSERT INTO users (%s) VALUES (%s)",
                implode(', ', $columns),
                implode(', ', $placeholders)
            )
        );
        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return false;
        }

        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    private function hasColumn($column) {
        return array_key_exists($column, $this->getColumns());
    }

    private function getColumns() {
        if ($this->columns !== null) {
            return $this->columns;
        }

        $this->columns = [];
        $result = $this->db->query("SHOW COLUMNS FROM users");
        while ($row = $result->fetch_assoc()) {
            $this->columns[$row['Field']] = $row['Type'];
        }

        return $this->columns;
    }
}
