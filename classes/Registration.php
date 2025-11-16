<?php

class Registration {
    private $conn;
    private $table_name = "registrations";

    public $registration_id;
    public $donor_id;
    public $drive_id;
    public $registration_date;
    public $status; // 'registered', 'attended', 'cancelled'

    /**
     * Constructor with database connection.
     * @param PDO $db The database connection object.
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Creates a new registration record for a donor to a drive.
     * @return boolean True if registration is successful, false otherwise.
     */
    public function create() {
        if ($this->isAlreadyRegistered()) {
            return false;
        }

        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    donor_id=:donor_id,
                    drive_id=:drive_id,
                    status=:status,
                    registration_date=:reg_date";
        
        $stmt = $this->conn->prepare($query);

        $this->donor_id = htmlspecialchars(strip_tags($this->donor_id));
        $this->drive_id = htmlspecialchars(strip_tags($this->drive_id));

        $this->status = "registered";
        $this->registration_date = date('Y-m-d H:i:s');

        $stmt->bindParam(":donor_id", $this->donor_id);
        $stmt->bindParam(":drive_id", $this->drive_id);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":reg_date", $this->registration_date);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    /**
     * Checks if a donor is already registered for a specific drive.
     * @return boolean True if already registered, false otherwise.
     */
    public function isAlreadyRegistered() {
        $query = "SELECT registration_id FROM " . $this->table_name . "
                  WHERE donor_id = :donor_id AND drive_id = :drive_id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(":donor_id", $this->donor_id);
        $stmt->bindParam(":drive_id", $this->drive_id);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Fetches all drives a specific donor has registered for.
     * @param int $donor_id The ID of the donor.
     * @return PDOStatement The statement object with the results.
     */
    public function readByDonor($donor_id) {
        $query = "SELECT
                    d.drive_name,
                    d.location_address,
                    d.start_time,
                    h.hospital_name,
                    r.status,
                    r.registration_date
                  FROM
                    " . $this->table_name . " r
                  JOIN
                    blood_drives d ON r.drive_id = d.drive_id
                  JOIN
                    hospitals h ON d.hospital_id = h.hospital_id
                  WHERE
                    r.donor_id = ?
                  ORDER BY
                    d.start_time DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $donor_id);
        $stmt->execute();
        
        return $stmt;
    }
}
?>