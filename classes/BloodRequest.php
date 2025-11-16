<?php
class BloodRequest {
    private $conn;
    private $table_name = "blood_requests";

    public $request_id;
    public $hospital_id;
    public $blood_type_needed;
    public $urgency_level;
    public $date_posted;
    public $status; //'open', 'fulfilled', 'closed'

    public $hospital_name;
    public $hospital_address;
    public $hospital_phone;

    /**
     * Constructor with database connection.
     * @param PDO $db The database connection object.
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Creates a new blood request.
     * @return boolean True if creation is successful, false otherwise.
     */
    public function create() {
        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    hospital_id=:hospital_id,
                    blood_type_needed=:blood_type,
                    urgency_level=:urgency,
                    status=:status,
                    date_posted=:date_posted";

        $stmt = $this->conn->prepare($query);

        $this->hospital_id = htmlspecialchars(strip_tags($this->hospital_id));
        $this->blood_type_needed = htmlspecialchars(strip_tags($this->blood_type_needed));
        $this->urgency_level = htmlspecialchars(strip_tags($this->urgency_level));

        $this->status = "open";
        $this->date_posted = date('Y-m-d H:i:s');

        $stmt->bindParam(":hospital_id", $this->hospital_id);
        $stmt->bindParam(":blood_type", $this->blood_type_needed);
        $stmt->bindParam(":urgency", $this->urgency_level);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":date_posted", $this->date_posted);

        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    /**
     * Reads all open blood requests from all hospitals.
     * This is for donors to view.
     * It joins with the hospitals table to get hospital details.
     * @return PDOStatement The statement object with the results.
     */
    public function readOpenRequests() {
        $query = "SELECT
                    r.request_id,
                    r.hospital_id,
                    r.blood_type_needed,
                    r.urgency_level,
                    r.date_posted,
                    r.status,
                    h.hospital_name,
                    h.address as hospital_address,
                    h.phone as hospital_phone
                  FROM
                    " . $this->table_name . " r
                  LEFT JOIN
                    hospitals h ON r.hospital_id = h.hospital_id
                  WHERE
                    r.status = 'open'
                  ORDER BY
                    r.date_posted DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        
        return $stmt;
    }

    /**
     * Reads all blood requests posted by a specific hospital.
     * This is for the hospital's dashboard.
     * @param int $hospital_id The ID of the hospital.
     * @return PDOStatement The statement object with the results.
     */
    public function readByHospital($hospital_id) {
        $query = "SELECT * FROM " . $this->table_name . "
                  WHERE hospital_id = ?
                  ORDER BY date_posted DESC";

        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(1, $hospital_id);

        $stmt->execute();
        
        return $stmt;
    }
    public function countOpen() {
      $query = "SELECT COUNT(*) as total FROM " . $this->table_name . " WHERE status = 'open'";
      $stmt = $this->conn->prepare($query);
      $stmt->execute();
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      return $row['total'];
  }

  /**
   * Reads the details of a single blood request by its ID.
   * Joins with the hospitals table to get full hospital details.
   * @return boolean True if a record was found and properties are set, false otherwise.
   */
  public function readOne() {
    $query = "SELECT
                r.request_id, r.hospital_id, r.blood_type_needed, r.urgency_level, r.date_posted, r.status,
                h.hospital_name, h.address as hospital_address, h.phone as hospital_phone
              FROM
                " . $this->table_name . " r
              LEFT JOIN
                hospitals h ON r.hospital_id = h.hospital_id
              WHERE
                r.request_id = :request_id
              LIMIT 1";

    echo $query; 
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':request_id', $this->request_id);
    $stmt->execute();

    if ($stmt->rowCount() == 1) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->hospital_id = $row['hospital_id'];
        $this->blood_type_needed = $row['blood_type_needed'];
        $this->urgency_level = $row['urgency_level'];
        $this->date_posted = $row['date_posted'];
        $this->status = $row['status'];
        $this->hospital_name = $row['hospital_name'];
        $this->hospital_address = $row['hospital_address'];
        $this->hospital_phone = $row['hospital_phone'];
        return true;
    }
    return false;
  }
}
?>