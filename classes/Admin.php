<?php
require_once 'User.php';

class Admin extends User {

    protected $table_name = "admins";
    protected $primary_key_column = "admin_id";

    public $username;

    /**
     * Constructor for the Admin class.
     * @param PDO $db The database connection object.
     */
    public function __construct($db) {
        parent::__construct($db);
        $this->table_name = "admins";
        $this->primary_key_column = "admin_id";
    }

    /**
     * OVERRIDE the parent login method.
     * Allows admin to log in with either email OR username.
     * The field submitted from the form should be assigned to the $this->email property.
     * @return array|false Returns user data on success, false on failure.
     */
    public function login() {
        $login_identifier = htmlspecialchars(strip_tags($this->email));

        $query = 'SELECT * FROM ' . $this->table_name . ' WHERE email = :login OR username = :login LIMIT 1';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':login', $login_identifier);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verify the provided password against the hashed password
            if (password_verify($this->password, $user['password'])) {
                return $user; 
            }
        }

        return false; 
    }


    /**
     * Fetches all hospitals that are awaiting approval.
     * @return PDOStatement The statement object containing the results.
     */
    public function getPendingHospitals() {
        $query = "SELECT hospital_id, hospital_name, email, phone, address, reg_date 
                  FROM hospitals 
                  WHERE is_approved = 0 
                  ORDER BY reg_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Approves a hospital by setting its is_approved status to 1.
     * @param int $hospital_id The ID of the hospital to approve.
     * @return boolean True on success, false on failure.
     */
    public function approveHospital($hospital_id) {
        $query = "UPDATE hospitals SET is_approved = 1 WHERE hospital_id = :hospital_id";

        $stmt = $this->conn->prepare($query);
        $hospital_id = htmlspecialchars(strip_tags($hospital_id));
        $stmt->bindParam(':hospital_id', $hospital_id);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
}
?>