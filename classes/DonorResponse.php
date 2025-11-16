<?php
class DonorResponse {
    private $conn;
    private $table_name = "donor_responses";

    public $response_id;
    public $donor_id;
    public $request_id;
    public $response_date;
    public $status; // 'pending', 'accepted', 'completed', 'rejected'

    /**
     * Constructor with database connection.
     * @param PDO $db The database connection object.
     */
    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Creates a new donor response to a blood request.
     * @return boolean True if creation is successful, false otherwise.
     */
    public function create() {
        if ($this->hasAlreadyResponded()) {
            return false; 
        }

        $query = "INSERT INTO " . $this->table_name . "
                  SET
                    donor_id=:donor_id,
                    request_id=:request_id,
                    status=:status,
                    response_date=:response_date";
        
        $stmt = $this->conn->prepare($query);

        $this->donor_id = htmlspecialchars(strip_tags($this->donor_id));
        $this->request_id = htmlspecialchars(strip_tags($this->request_id));

        $this->status = "pending";
        $this->response_date = date('Y-m-d H:i:s');

        $stmt->bindParam(":donor_id", $this->donor_id);
        $stmt->bindParam(":request_id", $this->request_id);
        $stmt->bindParam(":status", $this->status);
        $stmt->bindParam(":response_date", $this->response_date);
        
        if ($stmt->execute()) {
            return true;
        }
        
        return false;
    }

    /**
     * Checks if a donor has already responded to a specific request.
     * @return boolean True if a response exists, false otherwise.
     */
    public function hasAlreadyResponded() {
        $query = "SELECT response_id FROM " . $this->table_name . "
                  WHERE donor_id = :donor_id AND request_id = :request_id
                  LIMIT 1";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":donor_id", $this->donor_id);
        $stmt->bindParam(":request_id", $this->request_id);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
    
    /**
     * Fetches all donor responses for a specific blood request.
     * This is for a hospital to view who has offered to donate.
     * @param int $request_id The ID of the blood request.
     * @return PDOStatement The statement object with the results.
     */
    public function readByRequest($request_id) {
        $query = "SELECT
                    r.response_id,
                    r.status,
                    r.response_date,
                    d.full_name,
                    d.blood_type,
                    d.phone,
                    d.email
                  FROM
                    " . $this->table_name . " r
                  JOIN
                    donors d ON r.donor_id = d.donor_id
                  WHERE
                    r.request_id = ?
                  ORDER BY
                    r.response_date DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $request_id);
        $stmt->execute();
        
        return $stmt;
    }
}
?>