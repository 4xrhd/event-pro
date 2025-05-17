<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class Event {
    private $db;

    public function __construct() {
        $this->db = new mysqli("localhost", "root", "toor", "eventpro");

        if ($this->db->connect_error) {
            die("Database connection failed: " . $this->db->connect_error);
        }
    }

    public function create($title, $date, $venue, $price) {
        $stmt = $this->db->prepare("INSERT INTO events (title, event_date, venue, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sssd", $title, $date, $venue, $price);
        return $stmt->execute();
    }

    public function getAll() {
        $result = $this->db->query("SELECT * FROM events ORDER BY event_date DESC");
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    public function getById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM events WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc(); // Returns single event or null
    }
    
}
