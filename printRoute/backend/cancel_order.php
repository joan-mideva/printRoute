<?php
// backend/cancel_order.php
require_once 'db_connect.php';
session_start();

$response = ['success' => false, 'message' => 'An error occurred.'];

if (!isset($_SESSION['loggedin']) || !isset($_SESSION['user_id'])) {
    $response['message'] = 'Unauthorized';
    echo json_encode($response); exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$order_id = $data['order_id'] ?? null;
$user_id = $_SESSION['user_id'];

if (!$order_id) {
    $response['message'] = 'Invalid Order ID.';
    echo json_encode($response); exit;
}

$conn->begin_transaction();
try {
    // 1. Get the order details and lock the row for update
    $stmt = $conn->prepare("SELECT status, deposit_amount FROM orders WHERE order_id = ? AND user_id = ? FOR UPDATE");
    $stmt->bind_param("ii", $order_id, $user_id);
    $stmt->execute();
    $order = $stmt->get_result()->fetch_assoc();

    if (!$order) {
        throw new Exception("Order not found or you don't have permission to cancel it.");
    }
    if ($order['status'] !== 'Pending') {
        throw new Exception("This order cannot be cancelled as it is already being processed.");
    }

    // 2. Refund the deposit to the user's wallet
    $refund_amount = $order['deposit_amount'];
    $stmt = $conn->prepare("UPDATE users SET wallet_balance = wallet_balance + ? WHERE user_id = ?");
    $stmt->bind_param("di", $refund_amount, $user_id);
    $stmt->execute();

    // 3. Update the order status to 'Cancelled'
    $stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE order_id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();

    // If all queries succeed, commit the transaction
    $conn->commit();
    $response['success'] = true;
    $response['message'] = 'Order cancelled and deposit refunded.';

} catch (Exception $e) {
    // If any query fails, roll back all changes
    $conn->rollback();
    $response['message'] = $e->getMessage();
}

$conn->close();
header('Content-Type: application/json');
echo json_encode($response);
?>