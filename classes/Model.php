<?php
/**
 * Base Model Class
 * Parent class for all models using mysqli
 */
abstract class Model {
    protected $conn;
    protected $table;
    protected $primaryKey = 'id';
    
    public function __construct() {
        // Use global mysqli connection from includes/db.php
        if (!isset($GLOBALS['conn'])) {
            require_once __DIR__ . '/../includes/db.php';
        }
        $this->conn = $GLOBALS['conn'];
    }
    
    /**
     * Find record by ID
     * @param int $id
     * @return array|null
     */
    public function find($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE {$this->primaryKey} = ? LIMIT 1");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        return $row ?: null;
    }
    
    /**
     * Get all records
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function all($limit = 100, $offset = 0) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY created_at DESC LIMIT ? OFFSET ?");
        $stmt->bind_param("ii", $limit, $offset);
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $rows;
    }
    
    /**
     * Create new record
     * @param array $data
     * @return int Last insert ID
     */
    public function create(array $data) {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        
        $sql = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $stmt = $this->conn->prepare($sql);
        
        // Bind parameters dynamically
        $types = '';
        $values = [];
        foreach ($data as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $value;
        }
        
        $stmt->bind_param($types, ...$values);
        $stmt->execute();
        $insertId = $this->conn->insert_id;
        $stmt->close();
        
        return $insertId;
    }
    
    /**
     * Update record
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update($id, array $data) {
        $set = [];
        foreach (array_keys($data) as $key) {
            $set[] = "$key = ?";
        }
        $setClause = implode(', ', $set);
        
        $sql = "UPDATE {$this->table} SET $setClause WHERE {$this->primaryKey} = ?";
        $stmt = $this->conn->prepare($sql);
        
        // Bind parameters
        $types = '';
        $values = [];
        foreach ($data as $value) {
            if (is_int($value)) {
                $types .= 'i';
            } elseif (is_float($value)) {
                $types .= 'd';
            } else {
                $types .= 's';
            }
            $values[] = $value;
        }
        $types .= 'i'; // for the ID
        $values[] = $id;
        
        $stmt->bind_param($types, ...$values);
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }
    
    /**
     * Delete record
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE {$this->primaryKey} = ?");
        $stmt->bind_param("i", $id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
    
    /**
     * Execute custom query with parameters
     * @param string $query
     * @param string $types Types string (e.g., "iss" for int, string, string)
     * @param array $params Parameters array
     * @return array
     */
    protected function query($query, $types = '', array $params = []) {
        $stmt = $this->conn->prepare($query);
        
        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        
        return $rows;
    }
    
    /**
     * Execute query and return single row
     * @param string $query
     * @param string $types
     * @param array $params
     * @return array|null
     */
    protected function queryOne($query, $types = '', array $params = []) {
        $stmt = $this->conn->prepare($query);
        
        if (!empty($types) && !empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        return $row ?: null;
    }
}
