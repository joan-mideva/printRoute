<?php
// backend/get_shop_dashboard_data.php
require_once 'db_connect.php';
session_start();

// Ensure a shop owner is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'shop_owner') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$owner_id = $_SESSION['user_id'];
$response = [
    'success' => false,
    'data' => []
];

try {
    // 1. Find the shop_id for the logged-in owner
    $stmt_shop = $conn->prepare("SELECT shop_id, status FROM shops WHERE owner_id = ?");
    $stmt_shop->bind_param("i", $owner_id);
    $stmt_shop->execute();
    $result_shop = $stmt_shop->get_result();
    if ($result_shop->num_rows === 0) {
        throw new Exception("Shop not found for this owner.");
    }
    $shop = $result_shop->fetch_assoc();
    $shop_id = $shop['shop_id'];
    $current_status = $shop['status'];

    // 2. Get the count of pending orders
    $stmt_count = $conn->prepare("SELECT COUNT(*) as pending_count FROM orders WHERE shop_id = ? AND status = 'Pending'");
    $stmt_count->bind_param("i", $shop_id);
    $stmt_count->execute();
    $pending_count = $stmt_count->get_result()->fetch_assoc()['pending_count'];

    // 3. Get the details of new (pending) orders
    $stmt_orders = $conn->prepare("
        SELECT o.order_id, o.options_json, u.name as customer_name, o.file_path
        FROM orders o
        JOIN users u ON o.user_id = u.user_id
        WHERE o.shop_id = ? AND o.status = 'Pending'
        ORDER BY o.created_at ASC
    ");
    $stmt_orders->bind_param("i", $shop_id);
    $stmt_orders->execute();
    $orders_result = $stmt_orders->get_result();
    $pending_orders = [];
    while($row = $orders_result->fetch_assoc()) {
        $pending_orders[] = $row;
    }

    // Assemble the data
    $response['success'] = true;
    $response['data'] = [
        'current_status' => $current_status,
        'pending_count' => $pending_count,
        'orders' => $pending_orders
        // You can add earnings calculations here later
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>