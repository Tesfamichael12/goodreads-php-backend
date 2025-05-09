<?php
/**
 * Database connection class
 */
class Database {
    private $host = 'localhost';
    private $db_name = 'goodreads_clone';
    private $username = 'root';
    private $password = '';
    private $conn;
    // private $port = 3308; ";port=" .  $this->port .

    /**
     * Get the database connection
     * 
     * @return PDO The database connection
     */
    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false
                ]
            );
        } catch(PDOException $exception) {
            throw new Exception("Database connection failed: " . $exception->getMessage());
        }

        return $this->conn;
    }
}
?>
