<?php
    session_start();
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
      header("Location: login.php");
     exit;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Create New Order - printRoute</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"><link rel="stylesheet" href="assets/css/styles.css"><script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js"></script></head>
<body>
    <?php require_once 'nav_user.php'; // Include the new minimal navbar ?>

    <main class="container main-content">
        <div class="text-center mb-5"><h1>Create Your Print Order</h1></div>
        <div class="row g-5">
            <div class="col-md-7"><div class="card shadow-sm mb-4"><div class="card-body"><h5 class="card-title"><i class="bi bi-file-earmark-arrow-up-fill"></i> Step 1: Upload Your Document</h5><input class="form-control" type="file" id="pdfFile" accept=".pdf" required></div></div>
                <div class="card shadow-sm mb-4"><div class="card-body"><h5 class="card-title"><i class="bi bi-printer-fill"></i> Step 2: Choose Printing Options</h5><div class="row"><div class="col-lg-4 col-md-6 mb-3"><label for="printType" class="form-label">Print Type</label><select id="printType" class="form-select"><option value="Black & White" data-base-price="10" selected>Black & White</option><option value="Color" data-base-price="25">Color</option></select></div><div class="col-lg-4 col-md-6 mb-3"><label for="pageSize" class="form-label">Page Size</label><select id="pageSize" class="form-select"><option value="A4" data-multiplier="1" selected>A4</option><option value="A3" data-multiplier="2">A3</option><option value="Legal" data-multiplier="1.2">Legal</option></select></div><div class="col-lg-4 col-md-6 mb-3"><label for="paperGsm" class="form-label">Paper GSM</label><select id="paperGsm" class="form-select"><option value="75 gsm" data-multiplier="1" selected>75 gsm</option><option value="80 gsm" data-multiplier="1.1">80 gsm</option><option value="100 gsm" data-multiplier="1.3">100 gsm</option></select></div></div></div></div>
                <div class="card shadow-sm"><div class="card-body"><h5 class="card-title"><i class="bi bi-journal-album"></i> Step 3: Select Binding</h5><div class="row mt-3"><div class="col-6 col-md-3 mb-3"><input type="radio" name="bindingType" id="bindStaple" value="Staple" data-price="0" class="d-none" checked><label for="bindStaple" class="binding-option"><img src="assets/images/staple.png" alt="Staple Icon"><h6>Staple</h6><div class="price">Free</div></label></div><div class="col-6 col-md-3 mb-3"><input type="radio" name="bindingType" id="bindFile" value="File" data-price="30" class="d-none"><label for="bindFile" class="binding-option"><img src="assets/images/file.png" alt="File Icon"><h6>File</h6><div class="price">+ ₹30</div></label></div><div class="col-6 col-md-3 mb-3"><input type="radio" name="bindingType" id="bindSpiral" value="Spiral" data-price="50" class="d-none"><label for="bindSpiral" class="binding-option"><img src="assets/images/spiral.png" alt="Spiral Icon"><h6>Spiral</h6><div class="price">+ ₹50</div></label></div><div class="col-6 col-md-3 mb-3"><input type="radio" name="bindingType" id="bindHard" value="Hardbound" data-price="150" class="d-none"><label for="bindHard" class="binding-option"><img src="assets/images/hardbound.png" alt="Hardbound Icon"><h6>Hardbound</h6><div class="price">+ ₹150</div></label></div></div></div></div>
            </div>
            <div class="col-md-5">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header text-center"><h3>Order Summary</h3></div>
                    <div class="card-body">
                        <p><strong>Wallet Balance:</strong> <span id="walletBalance" class="fw-bold">₹0.00</span></p><hr>
                        <p><strong>File:</strong> <span id="fileName" class="text-muted">N/A</span></p>
                        <p><strong>Pages:</strong> <span id="pageCount">0</span></p>
                        <p><strong>Binding:</strong> <span id="bindingChoice">Staple</span></p>
                        <h4 class="text-center mt-3">Total Deposit</h4>
                        <p class="fs-2 text-center fw-bold" id="depositAmount">₹0.00</p>
                        <div class="form-text text-center mb-3">Don't worry. This amount is Refundable.<br>Final price confirmed by shop.</div></p>
                        <div class="d-grid">
                           <button id="proceedBtn" type="button" class="btn btn-primary btn-lg" disabled>Upload a File</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <footer class="footer mt-5"><div class="container">printRoute 2025 - made by Jenil Revaliya.</div></footer>

<script>
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.worker.min.js';
    // DOM Elements
    const pdfFileInput = document.getElementById('pdfFile');
    const printTypeSelect = document.getElementById('printType');
    const pageSizeSelect = document.getElementById('pageSize');
    const paperGsmSelect = document.getElementById('paperGsm');
    const bindingOptions = document.querySelectorAll('input[name="bindingType"]');
    const proceedBtn = document.getElementById('proceedBtn');
    // Summary Elements
    const walletBalanceSpan = document.getElementById('walletBalance');
    const fileNameSpan = document.getElementById('fileName');
    const pageCountSpan = document.getElementById('pageCount');
    const bindingChoiceSpan = document.getElementById('bindingChoice');
    const depositAmountSpan = document.getElementById('depositAmount');
    // Global variables
    let currentPageCount = 0;
    let totalDeposit = 0;
    let walletBalance = 0;

    // --- Event Listeners ---
    document.addEventListener('DOMContentLoaded', updateSummary);
    pdfFileInput.addEventListener('change', handleFileSelect);
    printTypeSelect.addEventListener('change', updateSummary);
    pageSizeSelect.addEventListener('change', updateSummary);
    paperGsmSelect.addEventListener('change', updateSummary);
    bindingOptions.forEach(option => option.addEventListener('change', updateSummary));
    proceedBtn.addEventListener('click', handleProceed);
    
    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (!file) { return; }
        fileNameSpan.textContent = file.name;
        proceedBtn.textContent = 'Processing...';
        const reader = new FileReader();
        reader.onload = e => {
            pdfjsLib.getDocument(e.target.result).promise.then(pdf => {
                currentPageCount = pdf.numPages;
                updateSummary();
            });
        };
        reader.readAsArrayBuffer(file);
    }

    function updateSummary() {
        walletBalance = parseFloat(localStorage.getItem('userWalletBalance')) || 0;
        walletBalanceSpan.textContent = `₹${walletBalance.toFixed(2)}`;
        
        const selectedBinding = document.querySelector('input[name="bindingType"]:checked');
        const bindingPrice = parseFloat(selectedBinding.dataset.price);
        bindingChoiceSpan.textContent = selectedBinding.value;
        pageCountSpan.textContent = currentPageCount;

        const basePrice = parseFloat(printTypeSelect.options[printTypeSelect.selectedIndex].dataset.basePrice);
        const sizeMultiplier = parseFloat(pageSizeSelect.options[pageSizeSelect.selectedIndex].dataset.multiplier);
        const gsmMultiplier = parseFloat(paperGsmSelect.options[paperGsmSelect.selectedIndex].dataset.multiplier);
        const costPerPage = basePrice * sizeMultiplier * gsmMultiplier;
        totalDeposit = (currentPageCount * costPerPage) + bindingPrice;
        depositAmountSpan.textContent = `₹${totalDeposit.toFixed(2)}`;

        if (currentPageCount > 0) {
            proceedBtn.disabled = false;
            if (walletBalance >= totalDeposit) {
                proceedBtn.className = 'btn btn-success btn-lg';
                proceedBtn.textContent = 'Pay with Wallet & Select Shop';
            } else {
                proceedBtn.className = 'btn btn-primary btn-lg';
                proceedBtn.textContent = 'Add Balance to Proceed';
            }
        } else {
            proceedBtn.disabled = true;
            proceedBtn.textContent = 'Upload a File';
        }
    }
    
    function handleProceed() {
        // Re-check balance just before proceeding to prevent errors
        const currentWalletBalance = parseFloat(localStorage.getItem('userWalletBalance')) || 0;
        
        const orderDetails = {
            fileName: pdfFileInput.files[0].name,
            pageCount: currentPageCount,
            printDetails: `${printTypeSelect.value}, ${pageSizeSelect.value}, ${paperGsmSelect.value}`,
            binding: document.querySelector('input[name="bindingType"]:checked').value,
            totalDeposit: totalDeposit,
            paid: false
        };

        if (currentWalletBalance >= totalDeposit) {
            // SUFFICIENT BALANCE: PAY NOW, THEN SELECT SHOP
            localStorage.setItem('userWalletBalance', currentWalletBalance - totalDeposit);
            orderDetails.paid = true;
            localStorage.setItem('printOrderDetails', JSON.stringify(orderDetails));
            window.location.href = 'select_shop.php';
        } else {
            // INSUFFICIENT BALANCE: GO TO PAYMENT PAGE TO TOP UP
            localStorage.setItem('printOrderDetails', JSON.stringify(orderDetails));
            window.location.href = 'payment.html';
        }
    }
</script>
</body>
</html>





