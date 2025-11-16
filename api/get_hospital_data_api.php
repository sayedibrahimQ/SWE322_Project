<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'hospital') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/BloodRequest.php';
require_once __DIR__ . '/../classes/BloodDrive.php';

$database = new Database();
$db = $database->connect();

$request = new BloodRequest($db);
$drive = new BloodDrive($db);

// Fetch requests and convert to array
$requests_stmt = $request->readByHospital($_SESSION['user_id']);
$requests = $requests_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch drives and convert to array
$drives_stmt = $drive->readByHospital($_SESSION['user_id']);
$drives = $drives_stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'success' => true,
    'requests' => $requests,
    'drives' => $drives
]);
?>