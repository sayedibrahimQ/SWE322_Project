<?php
class User {
    protected $conn;
    protected $table_name; 
    protected $primary_key_column;
    public $id;
    public $email;
    public $password;
    public $phone;
    public $city;
    public $reg_date;

    /**
     * Constructor to initialize the User object with a database connection.
     * @param PDO $db The database connection object from Database.php
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * A generic login function for any user type.
     * It relies on the child class setting the $table_name property.
     *
     * @return array|false Returns the user's data as an array if login is successful, otherwise returns false.
     */
    public function login() {
        $query = 'SELECT * FROM ' . $this->table_name . ' WHERE email = :email LIMIT 1';

        $stmt = $this->conn->prepare($query);

        $this->email = htmlspecialchars(strip_tags($this->email));

        $stmt->bindParam(':email', $this->email);

        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($this->password, $user['password'])) {
                return $user;
            }
        }

        return false;
    }

    /**
     * Checks if an email already exists in the user table.
     * This is useful to prevent duplicate registrations.
     *
     * @return boolean True if email exists, false otherwise.
     */
    public function emailExists() {
        $query = 'SELECT ' . $this->primary_key_column . ' FROM ' . $this->table_name . ' WHERE email = :email LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $this->email = htmlspecialchars(strip_tags($this->email));
        $stmt->bindParam(':email', $this->email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true; 
        }

        return false; 
    }

    public function getPrimaryKeyColumn() {
        return $this->primary_key_column;
    }
}
?>