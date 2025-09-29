<?php
// backend/get_user_orders.php

// These lines force PHP to display errors, which is useful for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'db_connect.php';
session_start();

// Set the header to indicate the response is JSON
header('Content-Type: application/json');

// Security check: Ensure a user is logged in
if (!isset($_SESSION['loggedin']) || !isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$response = ['success' => true, 'orders' => []];

try {
    // This query selects all columns needed by the dashboard
    $sql = "SELECT o.order_id, o.status, o.deposit_amount, s.name as shop_name 
            FROM orders o 
            JOIN shops s ON o.shop_id = s.shop_id 
            WHERE o.user_id = ? 
            ORDER BY o.created_at DESC";

    $stmt = $conn->prepare($sql);
    
    // Check if the prepare statement failed (e.g., syntax error in SQL)
    if ($stmt === false) {
        throw new Exception('Database prepare error: ' . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response['orders'][] = $row;
    }

    $stmt->close();
    
} catch (Exception $e) {
    // If any part of the process fails, catch the error
    $response['success'] = false;
    $response['message'] = $e->getMessage();
}

$conn->close();

echo json_encode($response);
?>