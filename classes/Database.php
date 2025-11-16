<?php
/**
 * Database Connection Class
 * Singleton pattern for database connections using mysqli
 */
class Database {
    private static $instance = null;
    private $connection;
    
   private $host = "sql113.infinityfree.com";
   // private $host = "localhost";
    private $username = "if0_40185804";
    //private $username = "root";
    private $password = "careerhub12";
    //private $password = "";
    private $database = "if0_40185804_uniconnect_db";
    //private $database = "uniconnect_db";
    
    /**
     * Private constructor to prevent direct instantiation
     */
    private function __construct() {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        
        try {
            $this->connection = new mysqli(
                $this->host,
                $this->username,
                $this->password,
                $this->database
            );
            
            // Set charset to utf8mb4
            $this->connection->set_charset("utf8mb4");
            
        } catch (mysqli_sql_exception $e) {
            error_log("Database Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed. Please try again later.");
        }
    }
    
    /**
     * Get singleton instance
     * @return Database
     */
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Get mysqli connection
     * @return mysqli
     */
    public function getConnection() {
        return $this->connection;
    }
    
    /**
     * Escape string for safe query use
     * @param string $value
     * @return string
     */
    public function escape($value) {
        return $this->connection->real_escape_string($value);
    }
    
    /**
     * Get last insert ID
     * @return int
     */
    public function lastInsertId() {
        return $this->connection->insert_id;
    }
    
    /**
     * Close connection
     */
    public function close() {
        if ($this->connection) {
            $this->connection->close();
        }
    }
    
    /**
     * Prevent cloning
     */
    private function __clone() {}
    
    /**
     * Prevent unserialization
     */
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
    
    /**
     * Destructor - close connection
     */
    public function __destruct() {
        $this->close();
    }
}
