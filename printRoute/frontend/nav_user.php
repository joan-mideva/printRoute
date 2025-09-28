<?php
// frontend/nav_user.php
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<nav class="navbar navbar-expand-lg navbar-light sticky-top shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="user_dashboard.php">
            <img src="assets/images/logo.png" alt="printRoute Logo" style="height: 40px;">
            <img src="assets/images/name.jpg" alt="printRoute Name">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#userNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="userNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i> Hello, <?php echo isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'User'; ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="user_dashboard.php"><i class="bi bi-grid-fill"></i> My Orders</a></li>
                        <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person-lines-fill"></i> My Profile</a></li>
                        <li><a class="dropdown-item" href="add_balance.html"><i class="bi bi-wallet2"></i> Add Money</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="../backend/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>