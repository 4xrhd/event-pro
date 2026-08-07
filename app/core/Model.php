<?php
/**
 * Base Model Class
 * 
 * Provides database connection and common CRUD operations
 * All models should extend this class
 */

class Model {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->connect();
    }
    
    /**
     * Establish database connection
     */
    protected function connect() {
        $this->db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($this->db->connect_error) {
            error_log("Database connection failed: " . $this->db->connect_error);
            throw new Exception("Database connection failed. Please try again later.");
        }
        
        // Set charset
        $this->db->set_charset(DB_CHARSET);
    }
    
    /**
     * Get all records
     */
    public function getAll($orderBy = 'id DESC', $limit = null) {
        $query = "SELECT * FROM {$this->table}";
        if ($orderBy) {
            $query .= " ORDER BY " . preg_replace('/[^a-zA-Z0-9_,\s]/', '', $orderBy);
        }
        if ($limit) {
            $query .= " LIMIT " . intval($limit);
        }
        
        $result = $this->db->query($query);
        return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    }
    
    /**
     * Get record by ID
     */
    public function getById(int $id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        if (!$stmt) {
            return null;
        }
        
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    
    /**
     * Get records by condition safely
     */
    public function getByCondition($column, $value, $operator = '=') {
        $cleanColumn = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
        $allowedOperators = ['=', '!=', '>', '<', '>=', '<=', 'LIKE'];
        if (!in_array(strtoupper($operator), $allowedOperators, true)) {
            $operator = '=';
        }

        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE `{$cleanColumn}` {$operator} ?");
        if (!$stmt) {
            return [];
        }
        
        $stmt->bind_param("s", $value);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
    
    /**
     * Insert a new record
     */
    public function insert($data) {
        if (empty($data)) {
            return false;
        }
        
        $cleanColumns = array_map(function($c) {
            return '`' . preg_replace('/[^a-zA-Z0-9_]/', '', $c) . '`';
        }, array_keys($data));

        $columns = implode(', ', $cleanColumns);
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $values = array_values($data);
        
        $query = "INSERT INTO {$this->table} ({$columns}) VALUES ({$placeholders})";
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            return false;
        }
        
        $types = str_repeat('s', count($values));
        $stmt->bind_param($types, ...$values);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        
        return false;
    }
    
    /**
     * Update a record
     */
    public function update(int $id, $data) {
        if (empty($data)) {
            return false;
        }
        
        $setParts = [];
        foreach (array_keys($data) as $column) {
            $cleanCol = preg_replace('/[^a-zA-Z0-9_]/', '', $column);
            $setParts[] = "`{$cleanCol}` = ?";
        }
        $setClause = implode(', ', $setParts);
        $values = array_values($data);
        
        $query = "UPDATE {$this->table} SET {$setClause} WHERE id = ?";
        $stmt = $this->db->prepare($query);
        
        if (!$stmt) {
            return false;
        }
        
        $values[] = $id;
        $types = str_repeat('s', count($values));
        $stmt->bind_param($types, ...$values);
        
        return $stmt->execute();
    }
    
    /**
     * Delete a record
     */
    public function delete(int $id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        if (!$stmt) {
            return false;
        }
        
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
    
    /**
     * Execute a custom query
     */
    public function query($sql, $params = [], $types = '') {
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            return false;
        }
        
        if (!empty($params)) {
            if (empty($types)) {
                $types = str_repeat('s', count($params));
            }
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        return $stmt->get_result();
    }
    
    /**
     * Begin transaction
     */
    public function beginTransaction() {
        $this->db->begin_transaction();
    }
    
    /**
     * Commit transaction
     */
    public function commit() {
        $this->db->commit();
    }
    
    /**
     * Rollback transaction
     */
    public function rollback() {
        $this->db->rollback();
    }
    
    /**
     * Get last insert ID
     */
    public function getLastInsertId() {
        return $this->db->insert_id;
    }
    
    /**
     * Sanitize input
     */
    protected function sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
