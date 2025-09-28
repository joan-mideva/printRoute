<?php
// backend/get_user_orders.php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['loggedin'])) {
    echo json_encode(['success' => false, 'orders' => []]);
    exit;
}

$user_id = $_SESSION['user_id'];
$response = ['success' => true, 'orders' => []];

// UPDATED QUERY: Select status and deposit_amount
$sql = "SELECT order_id, status, deposit_amount, shops.name as shop_name 
        FROM orders 
        JOIN shops ON orders.shop_id = shops.shop_id 
        WHERE orders.user_id = ? 
        ORDER BY orders.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $response['orders'][] = $row;
}

$stmt->close();
$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>