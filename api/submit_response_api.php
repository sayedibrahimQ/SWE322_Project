<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'donor') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../classes/DonorResponse.php';

$database = new Database();
$db = $database->connect();
$response = new DonorResponse($db);

$response->donor_id = $_SESSION['user_id'];
$response->request_id = $_POST['request_id'] ?? 0;

if ($response->create()) {
    echo json_encode(['success' => true, 'message' => 'Your response has been sent! The hospital will be notified.']);
} else {
    echo json_encode(['success' => false, 'message' => 'You have already responded to this request.']);
}
?>