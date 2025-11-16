<?php
require_once 'User.php';

class Donor extends User {

    protected $table_name = "donors";

    public $full_name;
    public $blood_type;

    /**
     * Constructor for the Donor class.
     * @param PDO $db The database connection object.
     */
    public function __construct($db) {
        parent::__construct($db);
        $this->table_name = "donors";
        $this->primary_key_column = "donor_id";
    }

    /**
     * Registers a new donor in the database.
     *
     * @return boolean True if registration is successful, false otherwise.
     */
    public function register() {
    
        if ($this->emailExists()) {
            return false; 
        }

        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    full_name=:full_name,
                    email=:email,
                    password=:password,
                    blood_type=:blood_type,
                    phone=:phone,
                    city=:city,
                    reg_date=:reg_date";

        $stmt = $this->conn->prepare($query);

        $this->full_name = htmlspecialchars(strip_tags($this->full_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->blood_type = htmlspecialchars(strip_tags($this->blood_type));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->city = htmlspecialchars(strip_tags($this->city));

        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $this->reg_date = date('Y-m-d H:i:s');

        $stmt->bindParam(':full_name', $this->full_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':blood_type', $this->blood_type);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':city', $this->city);
        $stmt->bindParam(':reg_date', $this->reg_date);

        if ($stmt->execute()) {
            return true;
        }

        printf("Error: %s.\n", $stmt->error);
        return false;
    }
    public function countAll() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }
}
?>