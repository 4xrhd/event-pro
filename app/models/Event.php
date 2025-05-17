<?php
class Event {
    private $db;

    public function __construct() {
        $this->db = new mysqli("localhost", "root", "toor", "eventpro");
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
}
