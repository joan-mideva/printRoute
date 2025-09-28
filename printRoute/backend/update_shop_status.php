<?php
// backend/update_shop_status.php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'shop_owner') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Get the new status from the POST request
$data = json_decode(file_get_contents('php://input'), true);
$new_status = $data['status'] ?? '';
$valid_statuses = ['Idle', 'Busy', 'Closed'];

if (!in_array($new_status, $valid_statuses)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status value.']);
    exit;
}

$owner_id = $_SESSION['user_id'];
$response = ['success' => false];

// Update the status in the database
$stmt = $conn->prepare("UPDATE shops SET status = ? WHERE owner_id = ?");
$stmt->bind_param("si", $new_status, $owner_id);
if ($stmt->execute()) {
    $response['success'] = true;
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>