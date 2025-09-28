<?php
// backend/get_shops.php

require_once 'db_connect.php';

// Add these lines to help see any future errors
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Use the correct syntax "=>" for associative arrays
$response = ['success' => false, 'shops' => []];

// Query to select all verified shops.
$sql = "SELECT shop_id, name, address, status, image_path FROM shops WHERE is_verified = 1";
$result = $conn->query($sql);

if ($result) {
    $response['success'] = true;
    while ($row = $result->fetch_assoc()) {
        $response['shops'][] = $row;
    }
} else {
    // If the query fails, add the error message to the response
    $response['message'] = "Database query failed: " . $conn->error;
}

$conn->close();

// Set the header to indicate the response is JSON
header('Content-Type: application/json');

// Output the response as a JSON string
echo json_encode($response);
?>