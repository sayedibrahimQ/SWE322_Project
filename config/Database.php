<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'blood_donation_db';
    private $username = 'root';
    private $password = ''; 

    private $conn;

    /**
     * Establishes a database connection using PDO.
     * @return PDO|null The PDO connection object or null on failure.
     */
    public function connect() {
        $this->conn = null;

        try {
            $dsn = 'mysql:host=' . $this->host . ';dbname=' . $this->db_name;

            $this->conn = new PDO($dsn, $this->username, $this->password);

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            echo 'Connection Error: ' . $e->getMessage();
        }

        return $this->conn;
    }
}
?>