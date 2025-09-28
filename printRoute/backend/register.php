<?php
// backend/register.php
require_once 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $conn->begin_transaction();
    try {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt_user = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt_user->bind_param("ssss", $name, $email, $hashed_password, $role);
        $stmt_user->execute();
        
        if ($role == 'shop_owner') {
            $owner_id = $conn->insert_id;
            $shop_name = $_POST['shop_name'];
            $address = $_POST['address'];
            $image_path = 'assets/images/shop.jpg';

            if (isset($_FILES['shop_image']) && $_FILES['shop_image']['error'] == 0) {
                $upload_dir = '../frontend/assets/images/'; // Save directly to assets
                $file_ext = strtolower(pathinfo($_FILES['shop_image']['name'], PATHINFO_EXTENSION));
                $new_filename = uniqid('shop_', true) . '.' . $file_ext;
                if (move_uploaded_file($_FILES['shop_image']['tmp_name'], $upload_dir . $new_filename)) {
                    $image_path = 'assets/images/' . $new_filename;
                }
            }
            $lat = 23.0225; $lng = 72.5714; // Placeholder geo-coordinates
            $stmt_shop = $conn->prepare("INSERT INTO shops (owner_id, name, address, latitude, longitude, image_path) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt_shop->bind_param("issdds", $owner_id, $shop_name, $address, $lat, $lng, $image_path);
            $stmt_shop->execute();
        }

        $conn->commit();
        $_SESSION['message'] = "Registration successful! Please log in.";
        header("Location: ../frontend/login.php");
    } catch (mysqli_sql_exception $exception) {
        $conn->rollback();
        $_SESSION['message'] = ($conn->errno == 1062) ? "This email is already registered." : "An error occurred.";
        header("Location: ../frontend/register.html");
    }
    $conn->close();
}
?>