<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hospital') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

require_once __DIR__ . '/../config/Database.php';

$database = new Database();
$db = $database->connect();

$drive_id = $_POST['drive_id'] ?? 0;
$hospital_id = $_SESSION['user_id'];

if (empty($drive_id)) {
    echo json_encode(['success' => false, 'message' => 'Invalid Drive ID.']);
    exit();
}

//  Ensure the hospital deleting the drive is the one who created it
$query = "DELETE FROM blood_drives WHERE drive_id = :drive_id AND hospital_id = :hospital_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':drive_id', $drive_id);
$stmt->bindParam(':hospital_id', $hospital_id);

if ($stmt->execute()) {
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true, 'message' => 'Blood drive has been cancelled.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Drive not found or you do not have permission to cancel it.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>