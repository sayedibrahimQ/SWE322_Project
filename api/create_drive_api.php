<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hospital') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/BloodDrive.php';

$database = new Database();
$db = $database->connect();
$drive = new BloodDrive($db);

$drive->hospital_id = $_SESSION['user_id'];
$drive->drive_name = $_POST['drive_name'] ?? '';
$drive->location_address = $_POST['location_address'] ?? '';
$drive->start_time = $_POST['start_time'] ?? '';
$drive->description = $_POST['description'] ?? '';

if ($drive->create()) {
    echo json_encode(['success' => true, 'message' => 'Blood drive scheduled successfully!']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to schedule the blood drive. Please check your inputs.']);
}
?>