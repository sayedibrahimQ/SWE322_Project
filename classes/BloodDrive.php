<?php

class BloodDrive {
    private $conn;
    private $table_name = "blood_drives";

    public $drive_id;
    public $hospital_id;
    public $drive_name;
    public $location_address;
    public $start_time;
    public $description;

    public $hospital_name;
    
    /**
     * Constructor with database connection.
     * @param PDO $db The database connection object.
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Creates a new blood drive.
     * @return boolean True if creation is successful, false otherwise.
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    hospital_id=:hospital_id,
                    drive_name=:drive_name,
                    location_address=:location,
                    start_time=:start_time,
                    description=:description";

        $stmt = $this->conn->prepare($query);

        $this->hospital_id = htmlspecialchars(strip_tags($this->hospital_id));
        $this->drive_name = htmlspecialchars(strip_tags($this->drive_name));
        $this->location_address = htmlspecialchars(strip_tags($this->location_address));
        $this->start_time = htmlspecialchars(strip_tags($this->start_time));
        $this->description = htmlspecialchars(strip_tags($this->description));
        
        $stmt->bindParam(":hospital_id", $this->hospital_id);
        $stmt->bindParam(":drive_name", $this->drive_name);
        $stmt->bindParam(":location", $this->location_address);
        $stmt->bindParam(":start_time", $this->start_time);
        $stmt->bindParam(":description", $this->description);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Reads all upcoming blood drives.
     * This is for donors to view. Filters for drives that haven't happened yet.
     * It joins with the hospitals table to get the hospital's name.
     * @return PDOStatement The statement object with the results.
     */
    public function readUpcoming() {
        $query = "SELECT
                    d.drive_id,
                    d.drive_name,
                    d.location_address,
                    d.start_time,
                    d.description,
                    h.hospital_name
                  FROM
                    " . $this->table_name . " d
                  LEFT JOIN
                    hospitals h ON d.hospital_id = h.hospital_id
                  WHERE
                    d.start_time >= CURDATE()
                  ORDER BY
                    d.start_time ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Reads all blood drives organized by a specific hospital.
     * This is for the hospital's dashboard.
     * @param int $hospital_id The ID of the hospital.
     * @return PDOStatement The statement object with the results.
     */
    public function readByHospital($hospital_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE hospital_id = ?
                  ORDER BY start_time DESC";

        $stmt = $this->conn->prepare($query);
        
        // Bind the hospital ID
        $stmt->bindParam(1, $hospital_id);

        $stmt->execute();
        
        return $stmt;
    }
}
?>