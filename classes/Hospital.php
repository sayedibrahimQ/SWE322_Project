<?php

require_once 'User.php';

class Hospital extends User {

    protected $table_name = "hospitals";

    public $hospital_name;
    public $address;
    public $is_approved; 

    /**
     * Constructor for the Hospital class.
     * @param PDO $db The database connection object.
     */
    public function __construct($db) {
        parent::__construct($db);
       
        $this->table_name = "hospitals";
        $this->primary_key_column = "hospital_id";
    }

    /**
     * Registers a new hospital in the database.
     * New hospitals are set to 'not approved' by default.
     *
     * @return boolean True if registration is successful, false otherwise.
     */
    public function register() {
        if ($this->emailExists()) {
            return false; 
        }

        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    hospital_name=:hospital_name,
                    email=:email,
                    password=:password,
                    phone=:phone,
                    address=:address,
                    is_approved=:is_approved,
                    reg_date=:reg_date";

        $stmt = $this->conn->prepare($query);

        $this->hospital_name = htmlspecialchars(strip_tags($this->hospital_name));
        $this->email = htmlspecialchars(strip_tags($this->email));
        $this->phone = htmlspecialchars(strip_tags($this->phone));
        $this->address = htmlspecialchars(strip_tags($this->address));

        $this->password = password_hash($this->password, PASSWORD_DEFAULT);

        $this->is_approved = 0; 
        $this->reg_date = date('Y-m-d H:i:s');

        $stmt->bindParam(':hospital_name', $this->hospital_name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':password', $this->password);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':address', $this->address);
        $stmt->bindParam(':is_approved', $this->is_approved);
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