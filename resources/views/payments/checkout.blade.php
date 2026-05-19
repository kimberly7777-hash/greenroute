<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Payment - AFIA ORBIT</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #055c5c;
            --secondary-color: #640404;
            --white-color: #ffffff;
            --light-bg: #f8f9fa;
        }
        body {
            background: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 40px;
        }
        .payment-container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        @media(min-width: 768px) {
            .payment-container {
                flex-direction: row;
                min-height: 500px;
            }
        }
        .invoice-summary {
            background: var(--primary-color);
            color: white;
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .payment-form-section {
            flex: 1.5;
            padding: 2rem;
        }
        .provider-option {
            border: 2px solid #eee;
            border-radius: 8px;
            padding: 10px;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            margin-bottom: 10px;
        }
        .provider-option:hover {
            border-color: var(--primary-color);
            background: #f9f9f9;
        }
        .provider-option.selected {
            border-color: var(--primary-color);
            background: rgba(5, 92, 92, 0.05);
            font-weight: bold;
            color: var(--primary-color);
        }
        .btn-pay {
            background: var(--primary-color);
            color: white;
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            font-size: 1.1rem;
            margin-top: 20px;
        }
        .btn-pay:hover {
            background: #044a4a;
        }
        .loader {
            display: none;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="payment-container">
        <!-- Left Side: Invoice Summary -->
        <div class="invoice-summary">
            <h4 class="mb-4"><i class="bi bi-receipt me-2"></i>Payment Details</h4>
            
            <div class="mb-3">
                <small class="text-white-50">INVOICE NUMBER</small>
                <div class="fs-5 fw-bold">{{ $invoice->invoice_number }}</div>
            </div>

            <div class="mb-3">
                <small class="text-white-50">SERVICE</small>
                <div class="fs-5">{{ ucfirst($invoice->service_type) }}</div>
            </div>
            
            <div class="mb-3">
                <small class="text-white-50">DUE DATE</small>
                <div class="fs-5">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : 'N/A' }}</div>
            </div>

            <hr style="border-color: rgba(255,255,255,0.2);">

            <div class="d-flex justify-content-between align-items-center mt-2">
                <span class="fs-5">Total Amount</span>
                <span class="fs-3 fw-bold">TZS {{ number_format($invoice->total_amount, 2) }}</span>
            </div>
            
            <a href="{{ route('client.invoices') }}" class="text-white-50 mt-auto text-decoration-none">
                <i class="bi bi-arrow-left me-1"></i> Cancel & Return
            </a>
        </div>

        <!-- Right Side: Payment Form -->
        <div class="payment-form-section">
            <h3 class="mb-4 text-dark">Select Payment Method</h3>

            @if($invoice->status === 'paid')
                <div class="alert alert-success text-center py-4">
                    <i class="bi bi-check-circle-fill fs-1 d-block mb-2"></i>
                    <h4>This invoice is already paid!</h4>
                    <p>Paid on {{ $invoice->paid_at ? $invoice->paid_at->format('M d, Y') : '' }}</p>
                    <a href="{{ route('client.invoices') }}" class="btn btn-outline-success">Return to Invoices</a>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-clock-history fs-1 text-primary mb-3"></i>
                    <h4 class="mb-3">Processing your payment automatically</h4>
                    <p class="text-muted">Please wait while we complete your payment. You will see a success message shortly.</p>
                </div>

                <div class="loader" id="loader">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Finalizing payment...<br>Please do not refresh or close this page.</p>
                </div>

                <div id="responseMessage" class="mt-3"></div>
            @endif
        </div>
    </div>
    
    <div class="text-center mt-4 text-muted small">
        <i class="bi bi-shield-lock-fill me-1"></i> Secured by AzamPay
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function runAutoPayment() {
        const loader = document.getElementById('loader');
        const msgDiv = document.getElementById('responseMessage');

        loader.style.display = 'block';
        msgDiv.innerHTML = '';

        fetch("{{ route('client.payments.auto', $invoice->id) }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            loader.style.display = 'none';
            if (data.success) {
                msgDiv.innerHTML = `<div class="alert alert-success text-center py-4">
                    <i class="bi bi-check-circle-fill fs-1 mb-2"></i>
                    <h4>Payment was successfully done</h4>
                    <p class="mb-2">${data.message}</p>
                    ${data.reference ? `<div><strong>Transaction Ref:</strong> ${data.reference}</div>` : ''}
                    <a href="{{ route('client.invoices') }}" class="btn btn-outline-primary mt-3">Return to Invoices</a>
                </div>`;
            } else {
                msgDiv.innerHTML = `<div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>${data.message || 'Unable to process payment automatically.'}
                </div>`;
            }
        })
        .catch(error => {
            loader.style.display = 'none';
            msgDiv.innerHTML = `<div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>An error occurred while processing payment. Please try again.</div>`;
            console.error(error);
        });
    }

    const invoicePaid = {{ $invoice->status === 'paid' ? 'true' : 'false' }};

    window.addEventListener('DOMContentLoaded', function() {
        if (!invoicePaid) {
            runAutoPayment();
        }
    });
</script>

</body>
</html>
