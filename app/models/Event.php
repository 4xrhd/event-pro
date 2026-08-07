<?php
require_once __DIR__ . '/../core/Model.php';

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

class Event extends Model {
    protected $table = 'events';
    private $columns;

    public function create($data) {
        $columns = [];
        $placeholders = [];
        $types = '';
        $values = [];

        $availableColumns = $this->getColumns();
        $columnMap = [
            'user_id' => ['type' => 'i', 'value' => (int) ($data['user_id'] ?? 0)],
            'title' => ['type' => 's', 'value' => $data['title']],
            'description' => ['type' => 's', 'value' => $data['description'] ?? null],
            'event_date' => ['type' => 's', 'value' => $this->formatEventDate($data['event_date'])],
            'venue' => ['type' => 's', 'value' => $data['venue']],
            'price' => ['type' => 'd', 'value' => (float) $data['price']],
            'capacity' => ['type' => 'i', 'value' => isset($data['capacity']) && $data['capacity'] !== '' ? (int) $data['capacity'] : null],
            'status' => ['type' => 's', 'value' => $data['status'] ?? 'published'],
        ];

        foreach ($columnMap as $column => $metadata) {
            if (!array_key_exists($column, $availableColumns)) {
                continue;
            }

            $columns[] = $column;
            $placeholders[] = '?';
            $types .= $metadata['type'];
            $values[] = $metadata['value'];
        }

        $stmt = $this->db->prepare(
            sprintf(
                "INSERT INTO events (%s) VALUES (%s)",
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

    public function updateEvent(int $id, array $data) {
        $setClause = [];
        $types = '';
        $values = [];

        $availableColumns = $this->getColumns();
        $columnMap = [
            'title' => ['type' => 's', 'value' => $data['title'] ?? null],
            'description' => ['type' => 's', 'value' => $data['description'] ?? null],
            'event_date' => ['type' => 's', 'value' => isset($data['event_date']) ? $this->formatEventDate($data['event_date']) : null],
            'venue' => ['type' => 's', 'value' => $data['venue'] ?? null],
            'price' => ['type' => 'd', 'value' => isset($data['price']) ? (float) $data['price'] : null],
            'capacity' => ['type' => 'i', 'value' => isset($data['capacity']) && $data['capacity'] !== '' ? (int) $data['capacity'] : null],
            'status' => ['type' => 's', 'value' => $data['status'] ?? null],
        ];

        foreach ($columnMap as $column => $metadata) {
            if (!array_key_exists($column, $availableColumns) || $metadata['value'] === null) {
                continue;
            }

            $setClause[] = "{$column} = ?";
            $types .= $metadata['type'];
            $values[] = $metadata['value'];
        }

        if (empty($setClause)) {
            return false;
        }

        $types .= 'i';
        $values[] = $id;

        $sql = sprintf("UPDATE events SET %s WHERE id = ?", implode(', ', $setClause));
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return false;
        }

        $stmt->bind_param($types, ...$values);
        return $stmt->execute();
    }

    public function getAll($orderBy = 'event_date ASC', $limit = null) {
        $query = "SELECT * FROM events";
        if (array_key_exists('status', $this->getColumns())) {
            $query .= " WHERE status != 'cancelled'";
        }
        if (!empty($orderBy)) {
            $query .= " ORDER BY " . $orderBy;
        }
        if ($limit !== null) {
            $query .= " LIMIT " . (int) $limit;
        }

        $result = $this->db->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }

    public function getById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM events WHERE id = ? LIMIT 1");
        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return null;
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function getBookedTicketCount(int $eventId): int {
        try {
            $stmt = $this->db->prepare("SELECT COALESCE(SUM(quantity), 0) AS total_booked FROM tickets WHERE event_id = ? AND payment_status != 'refunded'");
            if ($stmt) {
                $stmt->bind_param("i", $eventId);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                return (int) ($row['total_booked'] ?? 0);
            }
        } catch (Throwable $e) {
            try {
                $stmt = $this->db->prepare("SELECT COALESCE(SUM(quantity), 0) AS total_booked FROM tickets WHERE event_id = ?");
                if ($stmt) {
                    $stmt->bind_param("i", $eventId);
                    $stmt->execute();
                    $row = $stmt->get_result()->fetch_assoc();
                    return (int) ($row['total_booked'] ?? 0);
                }
            } catch (Throwable $e2) {
                error_log("getBookedTicketCount error: " . $e2->getMessage());
            }
        }
        return 0;
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM events WHERE id = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    private function formatEventDate($value) {
        $columnType = $this->getColumns()['event_date'] ?? '';
        $timestamp = strtotime($value);
        if ($timestamp === false) {
            return $value;
        }

        if (strpos($columnType, 'date') === 0 && strpos($columnType, 'datetime') !== 0) {
            return date('Y-m-d', $timestamp);
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    private function getColumns() {
        if ($this->columns !== null) {
            return $this->columns;
        }

        $this->columns = [];
        $result = $this->db->query("SHOW COLUMNS FROM events");
        while ($row = $result->fetch_assoc()) {
            $this->columns[$row['Field']] = $row['Type'];
        }

        return $this->columns;
    }
}
