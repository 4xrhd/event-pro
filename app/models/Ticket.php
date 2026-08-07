<?php
require_once __DIR__ . '/../core/Model.php';

class Ticket extends Model {
    protected $table = 'tickets';
    private $columns;

    public function create($eventId, $name, $email, $quantity, $totalAmount, $userId = null) {
        // Run inside transaction to prevent overselling race condition
        $this->beginTransaction();

        try {
            // Lock event row for capacity check if capacity is set
            $stmtEvent = $this->db->prepare("SELECT capacity FROM events WHERE id = ? FOR UPDATE");
            if ($stmtEvent) {
                $eventIdInt = (int) $eventId;
                $stmtEvent->bind_param("i", $eventIdInt);
                $stmtEvent->execute();
                $eventRow = $stmtEvent->get_result()->fetch_assoc();

                if ($eventRow && $eventRow['capacity'] !== null) {
                    $capacity = (int) $eventRow['capacity'];
                    
                    // Sum booked tickets
                    try {
                        $stmtCount = $this->db->prepare("SELECT COALESCE(SUM(quantity), 0) AS total FROM tickets WHERE event_id = ? AND payment_status != 'refunded'");
                    } catch (Throwable $e) {
                        $stmtCount = $this->db->prepare("SELECT COALESCE(SUM(quantity), 0) AS total FROM tickets WHERE event_id = ?");
                    }
                    if ($stmtCount) {
                        $stmtCount->bind_param("i", $eventIdInt);
                        $stmtCount->execute();
                        $booked = (int) ($stmtCount->get_result()->fetch_assoc()['total'] ?? 0);
                    } else {
                        $booked = 0;
                    }

                    if (($booked + $quantity) > $capacity) {
                        $this->rollback();
                        error_log("Capacity exceeded for event #{$eventIdInt}");
                        return false;
                    }
                }
            }

            $columnMap = [
                'event_id' => ['type' => 'i', 'value' => (int) $eventId],
                'user_id' => ['type' => 'i', 'value' => $userId !== null ? (int) $userId : null],
                'name' => ['type' => 's', 'value' => $name],
                'email' => ['type' => 's', 'value' => $email],
                'quantity' => ['type' => 'i', 'value' => (int) $quantity],
                'total_amount' => ['type' => 'd', 'value' => (float) $totalAmount],
                'payment_status' => ['type' => 's', 'value' => 'completed'],
            ];

            $columns = [];
            $placeholders = [];
            $types = '';
            $values = [];
            foreach ($columnMap as $column => $metadata) {
                if (!$this->hasColumn($column)) {
                    continue;
                }

                $columns[] = $column;
                $placeholders[] = '?';
                $types .= $metadata['type'];
                $values[] = $metadata['value'];
            }

            $stmt = $this->db->prepare(
                sprintf(
                    "INSERT INTO tickets (%s) VALUES (%s)",
                    implode(', ', $columns),
                    implode(', ', $placeholders)
                )
            );
            if (!$stmt) {
                $this->rollback();
                return false;
            }

            $stmt->bind_param($types, ...$values);
            if (!$stmt->execute()) {
                $this->rollback();
                return false;
            }

            $ticketId = $this->db->insert_id;
            $this->commit();
            return $ticketId;

        } catch (Exception $e) {
            $this->rollback();
            error_log("Ticket creation error: " . $e->getMessage());
            return false;
        }
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM tickets WHERE id = ? LIMIT 1");
        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return null;
        }

        $id = (int) $id;
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function getByUserId(int $userId) {
        $stmt = $this->db->prepare("
            SELECT t.*, e.title AS event_title, e.event_date, e.venue 
            FROM tickets t
            JOIN events e ON t.event_id = e.id
            WHERE t.user_id = ?
            ORDER BY t.created_at DESC
        ");
        if (!$stmt) {
            return [];
        }

        $stmt->bind_param("i", $userId);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function checkInTicket(int $ticketId, int $staffUserId) {
        $stmt = $this->db->prepare("
            UPDATE tickets 
            SET is_checked_in = 1, checked_in_at = NOW(), checked_by = ? 
            WHERE id = ? AND is_checked_in = 0
        ");
        if (!$stmt) {
            return false;
        }

        $stmt->bind_param("ii", $staffUserId, $ticketId);
        $stmt->execute();
        return $stmt->affected_rows > 0;
    }

    private function hasColumn($column) {
        return array_key_exists($column, $this->getColumns());
    }

    private function getColumns() {
        if ($this->columns !== null) {
            return $this->columns;
        }

        $this->columns = [];
        $result = $this->db->query("SHOW COLUMNS FROM tickets");
        while ($row = $result->fetch_assoc()) {
            $this->columns[$row['Field']] = $row['Type'];
        }

        return $this->columns;
    }
}
