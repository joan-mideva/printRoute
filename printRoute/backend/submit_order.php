<?php
// backend/submit_order.php

// These lines force PHP to display errors, which is useful for debugging.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connect.php';
session_start();

$response = ['success' => false, 'message' => 'An unknown error occurred.'];

// Security Check: Ensure a user is logged in
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['user_id'])) {
    $response['message'] = 'Authentication error: You must be logged in.';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Get data from frontend
$data = json_decode(file_get_contents('php://input'), true);

$user_id = $_SESSION['user_id'];
$shop_id = $data['shopId'] ?? null;
$orderDetails = $data['orderDetails'] ?? null;

// Validation
if (!$shop_id || !$orderDetails) {
    $response['message'] = 'Validation error: Missing order data.';
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

try {
    $file_path_placeholder = 'uploads/documents/placeholder.pdf';
    
    $sql = "INSERT INTO orders (user_id, shop_id, file_path, options_json, deposit_amount, status) VALUES (?, ?, ?, ?, ?, 'Pending')";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        throw new Exception('Database prepare failed: ' . htmlspecialchars($conn->error));
    }

    $printDetails = explode(', ', $orderDetails['printDetails']);
    $options_json = json_encode([
    'pages' => $orderDetails['pageCount'],
    'printType' => $printDetails[0] ?? 'N/A',
    'pageSize' => $printDetails[1] ?? 'N/A',
    'paperGsm' => $printDetails[2] ?? 'N/A',
    'binding' => $orderDetails['binding']
    ]);
    
    // Correct types: user_id(i), shop_id(i), file_path(s), options_json(s), deposit_amount(d)
    $bind_result = $stmt->bind_param("iisdd", $user_id, $shop_id, $file_path_placeholder, $options_json, $orderDetails['totalDeposit']);
    
    if ($bind_result === false) {
        throw new Exception('Database bind param failed: ' . htmlspecialchars($stmt->error));
    }
    
    if ($stmt->execute()) {
        $response['success'] = true;
        $response['message'] = 'Order submitted successfully!';
    } else {
        throw new Exception('Database execute failed: ' . htmlspecialchars($stmt->error));
    }

} catch (Exception $e) {
    $response['message'] = 'An exception occurred: ' . $e->getMessage();
}

if(isset($stmt)) $stmt->close();
$conn->close();

header('Content-Type: application/json');
echo json_encode($response);
?>