<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - printRoute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
       <?php require_once 'nav_user.php'; // Include the new minimal navbar ?>

    <main class="container main-content">
        <h1 class="mb-4">My Profile</h1>
        <div class="row g-4">
            <div class="col-md-7">
                <div class="card shadow-sm mb-4"><div class="card-header d-flex justify-content-between align-items-center"><h5 class="mb-0">Personal Details</h5><button class="btn btn-outline-primary btn-sm">Edit Profile</button></div><div class="card-body"><div class="mb-3"><label class="form-label text-muted">Full Name</label><p class="form-control-plaintext fs-5"><?php echo htmlspecialchars($_SESSION['name']); ?></p></div><div><label class="form-label text-muted">Email Address</label><p class="form-control-plaintext fs-5"><?php echo htmlspecialchars($_SESSION['email']); ?></p></div></div></div>
                <div class="card shadow-sm"><div class="card-header"><h5 class="mb-0">Wallet</h5></div><div class="card-body"><div class="d-flex justify-content-between align-items-center"><div><p class="text-muted mb-0">Available Balance</p><p class="fs-2 fw-bold" id="walletBalance">₹<?php echo number_format($_SESSION['wallet_balance'], 2); ?></p></div><div class="btn-group"><a href="add_balance.html" class="btn btn-success">Add</a><a href="withdraw.html" class="btn btn-danger">Withdraw</a></div></div><hr><a href="#">View Transaction History</a></div></div>
            </div>
            <div class="col-md-5">
                 <div class="card shadow-sm"><div class="card-header"><h5 class="mb-0">Security</h5></div><div class="card-body"><div class="d-grid gap-2"><button class="btn btn-primary">Change Password</button><button class="btn btn-secondary">Manage Devices</button></div></div></div>
            </div>
        </div>
    </main>
    <footer class="footer mt-5"><div class="container text-center"><span>&copy; 2025 printRoute. All Rights Reserved.</span></div></footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>