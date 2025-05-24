<?php
class Ticket {
    private $db;

    public function __construct() {
        $this->db = new mysqli("localhost", "root", "toor", "eventpro");
    }

    public function create($eventId, $name, $email) {
        $stmt = $this->db->prepare("INSERT INTO tickets (event_id, name, email) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $eventId, $name, $email);
        $stmt->execute();
        return $this->db->insert_id;
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tickets WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
