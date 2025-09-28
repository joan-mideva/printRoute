<?php
session_start();
// Security check: ensure user is logged in and is a shop owner
if (!isset($_SESSION['loggedin']) || $_SESSION['role'] !== 'shop_owner') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop Dashboard - printRoute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#"><img src="assets/images/logo.png" alt="printRoute Logo" style="height: 40px;"></a>
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link active" href="shop_dashboard.php">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Earnings</a></li>
                <li class="nav-item"><a class="nav-link" href="../backend/login.php">Logout</a></li>
            </ul>
        </div>
    </nav>

    <main class="container main-content">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h1 class="mb-2 mb-md-0">Shop Dashboard</h1>
            <div id="statusControlGroup" class="btn-group status-btn-group" role="group">
                <button type="button" class="btn btn-outline-success" data-status="Idle">Idle</button>
                <button type="button" class="btn btn-outline-warning" data-status="Busy">Busy</button>
                <button type="button" class="btn btn-outline-danger" data-status="Closed">Closed</button>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-md-4"><div class="card text-center shadow-sm"><div class="card-body"><h5 class="card-title">Pending Orders</h5><p id="pendingCount" class="card-text fs-2 fw-bold">0</p></div></div></div>
            <div class="col-md-4"><div class="card text-center shadow-sm"><div class="card-body"><h5 class="card-title">Completed Today</h5><p class="card-text fs-2 fw-bold">0</p></div></div></div>
            <div class="col-md-4"><div class="card text-center shadow-sm"><div class="card-body"><h5 class="card-title">Today's Earnings</h5><p class="card-text fs-2 fw-bold">₹0</p></div></div></div>
        </div>
        <div class="card shadow-sm"><div class="card-header"><h3>New Orders Awaiting Action</h3></div><div class="card-body"><div class="table-responsive"><table class="table table-hover"><thead><tr><th>Order ID</th><th>Customer</th><th>File</th><th>Options</th><th>Actions</th></tr></thead><tbody id="ordersTableBody"><tr><td colspan="5" class="text-center">Loading orders...</td></tr></tbody></table></div></div></div>
    </main>
    <footer class="footer mt-5"><div class="container text-center"><span>&copy; 2025 printRoute. All Rights Reserved.</span></div></footer>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const statusGroup = document.getElementById('statusControlGroup');
            const buttons = statusGroup.querySelectorAll('.btn');
            
            // Fetch initial data when page loads
            fetchDashboardData();

            // Handle status button clicks
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    const newStatus = this.dataset.status;
                    updateStatusOnServer(newStatus);
                });
            });
        });

        // Fetches all necessary data from the backend
        async function fetchDashboardData() {
            try {
                const response = await fetch('../backend/get_shop_dashboard_data.php');
                const result = await response.json();

                if (result.success) {
                    const data = result.data;
                    document.getElementById('pendingCount').textContent = data.pending_count;
                    updateStatusButtonsUI(data.current_status);
                    populateOrdersTable(data.orders);
                } else {
                    alert('Could not load dashboard data: ' + result.message);
                }
            } catch (error) {
                console.error('Error fetching dashboard data:', error);
                document.getElementById('ordersTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading data.</td></tr>';
            }
        }

        // Populates the orders table with data from the server
        function populateOrdersTable(orders) {
    const tableBody = document.getElementById('ordersTableBody');
    tableBody.innerHTML = ''; // Clear loading message

    if (orders.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No new orders.</td></tr>';
        return;
    }

    orders.forEach(order => {
        // This part now correctly reads the structured JSON
        const options = JSON.parse(order.options_json || '{}');
        const optionsText = `${options.pages || '?'}p, ${options.printType || ''}, ${options.binding || ''}`;
        const downloadLink = `../backend/download_file.php?order_id=${order.order_id}`;
        
        const row = `
            <tr>
                <td>#${order.order_id}</td>
                <td>${order.customer_name}</td>
                <td><a href="${downloadLink}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i> Download PDF</a></td>
                <td>${optionsText}</td>
                <td>
                    <button class="btn btn-sm btn-success">Accept</button>
                    <button class="btn btn-sm btn-danger">Reject</button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}
        // Updates the color/style of the status buttons
        function updateStatusButtonsUI(activeStatus) {
            const buttons = document.querySelectorAll('#statusControlGroup .btn');
            buttons.forEach(btn => {
                const status = btn.dataset.status;
                let currentClasses = btn.className;

                // Reset both classes first
                currentClasses = currentClasses.replace('btn-success', 'btn-outline-success');
                currentClasses = currentClasses.replace('btn-warning', 'btn-outline-warning');
                currentClasses = currentClasses.replace('btn-danger', 'btn-outline-danger');
                
                btn.className = currentClasses;

                // Apply the active class
                if (status === activeStatus) {
                    btn.className = btn.className.replace('outline-', '');
                }
            });
        }

        // Sends the new status to the server
        async function updateStatusOnServer(newStatus) {
            try {
                const response = await fetch('../backend/update_shop_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ status: newStatus })
                });
                const result = await response.json();

                if (result.success) {
                    updateStatusButtonsUI(newStatus);
                } else {
                    alert('Failed to update status.');
                }
            } catch (error) {
                console.error('Error updating status:', error);
                alert('An error occurred while updating status.');
            }
        }
    </script>
</body>
</html>