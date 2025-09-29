<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard - printRoute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>

    <?php require_once 'nav_user.php'; // Include the new minimal navbar ?>

    <main class="container main-content">
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
            <h1 class="mb-2 mb-md-0">Dashboard</h1>
            <div class="card shadow-sm"><div class="card-body p-3"><div class="d-flex align-items-center"><div class="me-3"><h6 class="card-title mb-0 text-muted">Wallet Balance</h6><p class="fs-4 fw-bold mb-0">₹<?php echo number_format($_SESSION['wallet_balance'], 2); ?></p></div><div class="d-flex flex-column"><a href="add_balance.html" class="btn btn-success btn-sm mb-1"><i class="bi bi-plus-circle"></i> Add</a><a href="withdraw.html" class="btn btn-outline-danger btn-sm"><i class="bi bi-arrow-up-right-circle"></i> Withdraw</a></div></div></div></div>
        </div>
        
        <div class="card text-center mb-4 shadow-sm">
            <div class="card-body p-4">
                <h5 class="card-title">Ready to Print?</h5>
                <p class="card-text text-muted">Find a nearby shop and upload your documents in seconds.</p>
                <a href="upload_pdf.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-file-earmark-arrow-up-fill"></i> Start a New Order
                </a>
            </div>
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header"><h3>Active & Recent Orders</h3></div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead><tr><th>Order ID</th><th>Shop Name</th><th>Status</th><th class="text-end">Action</th></tr></thead>
                        <tbody id="activeOrdersTable">
                            </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
    
    <footer class="footer mt-5"><div class="container text-center"><span>printRoute 2025 - made by Jenil Revaliya.</span></div></footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
 <script>
    document.addEventListener('DOMContentLoaded', function() {
        renderOrders();

        // Use event delegation for cancel buttons
        document.getElementById('activeOrdersTable').addEventListener('click', function(e) {
            if (e.target && e.target.classList.contains('cancel-btn')) {
                const orderId = e.target.dataset.orderId;
                if (confirm(`Are you sure you want to cancel order #${orderId}? The deposit will be refunded.`)) {
                    cancelOrder(orderId);
                }
            }
        });
    });

    async function cancelOrder(orderId) {
        try {
            const response = await fetch('../backend/cancel_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ order_id: orderId })
            });
            const result = await response.json();
            alert(result.message);
            if (result.success) {
                // Reload the page to show updated orders and wallet balance
                window.location.reload();
            }
        } catch (error) {
            alert('An error occurred. Please try again.');
            console.error('Cancellation Error:', error);
        }
    }

    async function renderOrders() {
        const tableBody = document.getElementById('activeOrdersTable');
        try {
            const response = await fetch('../backend/get_user_orders.php');
            const result = await response.json();
            
            tableBody.innerHTML = ''; // Clear loading message

            if (result.success && result.orders.length > 0) {
                result.orders.forEach(order => {
                    let statusBadge, actionButton;

                    // Trim status string to be safe
                    const status = order.status ? order.status.trim() : '';

                    switch(status) {
                        case 'Pending':
                            statusBadge = `<span class="badge bg-secondary">Pending</span>`;
                            actionButton = `<button class="btn btn-sm btn-danger cancel-btn" data-order-id="${order.order_id}">Cancel</button>`;
                            break;
                        case 'Printing':
                            statusBadge = `<span class="badge bg-warning text-dark">Printing</span>`;
                            actionButton = `<button class="btn btn-sm btn-outline-secondary" disabled>Track</button>`;
                            break;
                        case 'Ready':
                            statusBadge = `<span class="badge bg-info text-dark">Ready</span>`;
                            actionButton = `<a href="#" class="btn btn-sm btn-primary">Get QR Code</a>`;
                            break;
                        case 'Completed':
                            statusBadge = `<span class="badge bg-success">Completed</span>`;
                            actionButton = `<a href="#" class="btn btn-sm btn-outline-info">Invoice</a>`;
                            break;
                        case 'Cancelled':
                            statusBadge = `<span class="badge bg-light text-dark text-decoration-line-through">Cancelled</span>`;
                            actionButton = ``; // No action
                            break;
                        default:
                            statusBadge = `<span class="badge bg-light text-dark">${status}</span>`;
                            actionButton = '';
                    }
                    
                    const row = `
                        <tr>
                            <td>#${order.order_id}</td>
                            <td>${order.shop_name}</td>
                            <td>${statusBadge}</td>
                            <td class="text-end">${actionButton}</td>
                        </tr>`;
                    tableBody.innerHTML += row;
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="4" class="text-center">You have no active orders.</td></tr>';
            }
        } catch (error) {
            console.error("Failed to fetch orders:", error);
        }
    }
</script>
</body>
</html>