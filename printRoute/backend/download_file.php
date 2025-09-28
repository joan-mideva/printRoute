<?php
// backend/download_file.php
require_once 'db_connect.php';
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'shop_owner') {
    die("Access Denied.");
}

$order_id = $_GET['order_id'] ?? 0;
$owner_id = $_SESSION['user_id'];

if (!$order_id) {
    die("Invalid order ID.");
}

// Security Check: Verify this order belongs to this shop owner
$stmt = $conn->prepare("
    SELECT o.file_path FROM orders o
    JOIN shops s ON o.shop_id = s.shop_id
    WHERE o.order_id = ? AND s.owner_id = ?
");
$stmt->bind_param("ii", $order_id, $owner_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 1) {
    $order = $result->fetch_assoc();
    $file_path = __DIR__ . '/../' . $order['file_path']; // Create absolute path

    // NOTE: Decryption logic would go here in a real application
    
    if (file_exists($file_path)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        exit;
    } else {
        die("File not found.");
    }
} else {
    die("You do not have permission to access this file.");
}
?>