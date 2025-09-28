<?php
// backend/login.php

// 1. Include the database connection and start the session
require_once 'db_connect.php';
session_start();

// 2. Only process POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Get and sanitize user input
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // 4. Basic validation
    if (empty($email) || empty($password)) {
        $_SESSION['message'] = "Email and password are required.";
        header("Location: ../frontend/login.php");
        exit();
    }

    // 5. Prepare a secure SQL statement to find the user
    $stmt = $conn->prepare("SELECT user_id, name, email, password, role, wallet_balance FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    // 6. Check if a user with that email exists
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // 7. Verify the password securely
        if (password_verify($password, $user['password'])) {
            // --- Password is correct: Set session variables ---
            $_SESSION['loggedin'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['wallet_balance'] = $user['wallet_balance'];

            // 8. Redirect based on user role
            if ($user['role'] == 'admin') {
                header("Location: ../frontend/admin_dashboard.php");
            } else if ($user['role'] == 'shop_owner') {
                header("Location: ../frontend/shop_dashboard.php");
            } else {
                header("Location: ../frontend/user_dashboard.php");
            }
            exit();
        } else {
            // --- Password is WRONG ---
            $_SESSION['message'] = "Invalid password. Please try again.";
            header("Location: ../frontend/login.php");
            exit();
        }
    } else {
        // --- User email was NOT FOUND ---
        $_SESSION['message'] = "No user found with that email address.";
        header("Location: ../frontend/login.php");
        exit();
    }

    $stmt->close();
    $conn->close();
} else {
    // Redirect if the script is accessed directly
    header("Location: ../frontend/login.php");
    exit();
}
?>