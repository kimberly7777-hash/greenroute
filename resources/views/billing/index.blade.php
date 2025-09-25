<x-guest-layout>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="text-success">Billing & Payments</h4>
            <a href="{{ route('billing.create') }}" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Create Invoice
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-primary">{{ $stats['total_invoices'] }}</h5>
                        <small class="text-muted">Total Invoices</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-success">{{ $stats['paid_invoices'] }}</h5>
                        <small class="text-muted">Paid Invoices</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-danger">{{ $stats['overdue_invoices'] }}</h5>
                        <small class="text-muted">Overdue</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="text-info">${{ number_format($stats['total_revenue'], 2) }}</h5>
                        <small class="text-muted">Total Revenue</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoices Table -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Invoices</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Invoice #</th>
                                <th>Client</th>
                                <th>Service</th>
                                <th>Amount</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($invoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_number }}</td>
                                <td>{{ $invoice->client->name }}</td>
                                <td>{{ $invoice->service_type }}</td>
                                <td>${{ number_format($invoice->total_amount, 2) }}</td>
                                <td>{{ $invoice->due_date->format('M d, Y') }}</td>
                                <td>
                                    @if($invoice->status === 'paid')
                                        <span class="badge bg-success">Paid</span>
                                    @elseif($invoice->is_overdue)
                                        <span class="badge bg-danger">Overdue</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('billing.show', $invoice) }}" class="btn btn-outline-primary">View</a>
                                        @if($invoice->status !== 'paid')
                                            <button class="btn btn-outline-success" onclick="markPaid({{ $invoice->id }})">Mark Paid</button>
                                            <button class="btn btn-outline-info" onclick="sendInvoice({{ $invoice->id }})">Send</button>
                                            @if($invoice->is_overdue)
                                                <button class="btn btn-outline-danger" onclick="sendReminder({{ $invoice->id }})">Remind</button>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No invoices found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Mark Paid Modal -->
    <div class="modal fade" id="markPaidModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Record Payment</h5>
                    <button type="button" class="btn-close" onclick="closeModal()"></button>
                </div>
                <form id="paymentForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="check">Check</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Amount Paid</label>
                            <input type="number" name="amount_paid" class="form-control" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                        <button type="submit" class="btn btn-success">Record Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function markPaid(invoiceId) {
            const form = document.getElementById('paymentForm');
            form.action = `/billing/${invoiceId}/mark-paid`;
            const modal = document.getElementById('markPaidModal');
            modal.style.display = 'block';
            modal.classList.add('show');
            document.body.classList.add('modal-open');
        }
        
        function closeModal() {
            const modal = document.getElementById('markPaidModal');
            modal.style.display = 'none';
            modal.classList.remove('show');
            document.body.classList.remove('modal-open');
        }
        
        // Close modal when clicking backdrop
        document.getElementById('markPaidModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });

        function sendInvoice(invoiceId) {
            if (confirm('Send invoice to client?')) {
                fetch(`/billing/${invoiceId}/send`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                }).then(() => location.reload());
            }
        }

        function sendReminder(invoiceId) {
            if (confirm('Send payment reminder to client?')) {
                fetch(`/billing/${invoiceId}/remind`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    }
                }).then(() => location.reload());
            }
        }
    </script>
</x-guest-layout>