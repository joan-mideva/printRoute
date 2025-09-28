<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select a Shop - printRoute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
    <?php require_once 'nav_user.php'; // Include the new minimal navbar ?>

    <main class="container main-content">
        <div class="text-center mb-5"><h1>Select a Nearby Shop</h1><p class="lead">Your deposit is secured. Choose a shop to send your print request.</p></div>
        <div class="alert alert-info d-flex flex-wrap justify-content-around">
            <span><i class="bi bi-file-earmark-pdf"></i> <strong>File:</strong> <span id="summaryFile">...</span></span>
            <span><i class="bi bi-body-text"></i> <strong>Pages:</strong> <span id="summaryPages">...</span></span>
            <span><i class="bi bi-printer"></i> <strong>Print:</strong> <span id="summaryPrint">...</span></span>
            <span><i class="bi bi-journal-album"></i> <strong>Binding:</strong> <span id="summaryBinding">...</span></span>
        </div>
        <div id="shopListContainer" class="row g-4 mt-3">
            <div class="col-12 text-center">
                <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading shops...</span></div>
            </div>
        </div>
    </main>
    <footer class="footer mt-5"><div class="container text-center"><span>&copy; 2025 printRoute. All Rights Reserved.</span></div></footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const savedOrderDetails = localStorage.getItem('printOrderDetails');
        if (!savedOrderDetails) {
            document.getElementById('shopListContainer').innerHTML = '<div class="col-12"><div class="alert alert-danger">No order details found. Please <a href="upload_pdf.html">start a new order</a>.</div></div>';
            return;
        }
        
        const orderDetails = JSON.parse(savedOrderDetails);
        if (!orderDetails.paid) {
            alert('Payment has not been completed for this order.');
            window.location.href = 'upload_pdf.html';
            return;
        }
        
        // Populate summary bar
        document.getElementById('summaryFile').textContent = orderDetails.fileName;
        document.getElementById('summaryPages').textContent = orderDetails.pageCount;
        document.getElementById('summaryPrint').textContent = orderDetails.printDetails;
        document.getElementById('summaryBinding').textContent = orderDetails.binding;

        // Fetch shops from the database and render them
        fetchAndRenderShops(orderDetails);

        // ***** BUTTON FUNCTIONALITY ADDED BACK *****
        // Use event delegation to handle clicks on dynamically created buttons
        document.getElementById('shopListContainer').addEventListener('click', async function(e) {
            if (e.target && e.target.classList.contains('send-request-btn')) {
                const card = e.target.closest('.card');
                const shopId = card.dataset.shopId;

                e.target.disabled = true;
                e.target.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Sending...';

                try {
                    const response = await fetch('../backend/submit_order.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ shopId: shopId, orderDetails: orderDetails })
                    });
                    const result = await response.json();

                    if (result.success) {
                        localStorage.removeItem('printOrderDetails');
                        alert(result.message);
                        window.location.href = 'user_dashboard.php';
                    } else {
                        alert('Error: ' + result.message);
                        e.target.disabled = false;
                        e.target.textContent = 'Send Print Request';
                    }
                } catch (error) {
                    console.error('Fetch Error:', error);
                    alert('A network error occurred. Please try again.');
                    e.target.disabled = false;
                    e.target.textContent = 'Send Print Request';
                }
            }
        });
    });

    // This function fetches the shop list
    async function fetchAndRenderShops(orderDetails) {
        const container = document.getElementById('shopListContainer');
        try {
            const response = await fetch('../backend/get_shops.php');
            if (!response.ok) { throw new Error(`HTTP error! Status: ${response.status}`); }
            const data = await response.json();
            container.innerHTML = ''; 

            if (data.success && data.shops.length > 0) {
                data.shops.forEach(shop => {
                    let statusBadge, button;
                    switch(shop.status) {
                        case 'Idle': statusBadge = `<span class="badge bg-success">Idle</span>`; button = `<button class="btn btn-primary send-request-btn">Send Print Request</button>`; break;
                        case 'Busy': statusBadge = `<span class="badge bg-warning text-dark">Busy</span>`; button = `<button class="btn btn-primary send-request-btn">Send Print Request</button>`; break;
                        default: statusBadge = `<span class="badge bg-danger">Closed</span>`; button = `<button class="btn btn-secondary" disabled>Unavailable</button>`;
                    }
                    
                    const shopCardHTML = `
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm shop-card" data-shop-id="${shop.shop_id}">
                                <img src="${shop.image_path}" class="card-img-top" alt="Photo of ${shop.name}">
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">${shop.name}</h5>
                                    <p class="card-text text-muted mb-2"><i class="bi bi-geo-alt-fill"></i> ${shop.address}</p>
                                    <div>${statusBadge}</div><hr>
                                    <div class="mt-auto text-center">
                                        <p class="mb-1">Your Price</p>
                                        <p class="price shop-price">₹${orderDetails.totalDeposit.toFixed(2)}</p>
                                        <div class="d-grid">${button}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += shopCardHTML;
                });
            } else {
                container.innerHTML = '<div class="col-12"><p class="text-center">No verified shops are currently available.</p></div>';
            }
        } catch (error) {
            console.error('Error fetching shops:', error);
            container.innerHTML = '<div class="col-12"><div class="alert alert-danger">Could not load the list of shops. Please try again later.</div></div>';
        }
    }
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>