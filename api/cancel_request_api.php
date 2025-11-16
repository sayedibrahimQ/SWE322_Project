<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hospital') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

require_once __DIR__ . '/../config/Database.php';

// We only need the database connection, not the whole class for a simple delete.
$database = new Database();
$db = $database->connect();

$request_id = $_POST['request_id'] ?? 0;
$hospital_id = $_SESSION['user_id'];

if (empty($request_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid Request ID.']);
    exit();
}

// Ensure the hospital deleting the request is the one who created it
$query = "DELETE FROM blood_requests WHERE request_id = :request_id AND hospital_id = :hospital_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':request_id', $request_id);
$stmt->bindParam(':hospital_id', $hospital_id);

if ($stmt->execute()) {
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Blood request has been cancelled.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Request not found or you do not have permission to cancel it.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>