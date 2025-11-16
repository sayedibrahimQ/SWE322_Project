<?php
session_start();
header('Content-Type: application/json'); 

// ensure user is a logged-in hospital
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hospital') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/BloodRequest.php';

$database = new Database();
$db = $database->connect();
$request = new BloodRequest($db);

$request->hospital_id = $_SESSION['user_id'];
$request->blood_type_needed = $_POST['blood_type'] ?? '';
$request->urgency_level = $_POST['urgency'] ?? '';

if ($request->create()) {
    echo json_encode(['success' => true, 'message' => 'Blood request posted successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to post blood request.']);
}
?>