<x-guest-layout>
    <div class="container py-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="text-success mb-0">Invoice Details</h5>
                <div>
                    @if($invoice->status !== 'paid')
                        <button class="btn btn-success btn-sm" onclick="markPaid({{ $invoice->id }})">Mark Paid</button>
                        <button class="btn btn-info btn-sm" onclick="sendInvoice({{ $invoice->id }})">Send Invoice</button>
                        @if($invoice->is_overdue)
                            <button class="btn btn-danger btn-sm" onclick="sendReminder({{ $invoice->id }})">Send Reminder</button>
                        @endif
                    @endif
                    <a href="{{ route('billing.index') }}" class="btn btn-secondary btn-sm">Back</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-success">Invoice Information</h6>
                        <p><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</p>
                        <p><strong>Client:</strong> {{ $invoice->client->name }}</p>
                        <p><strong>Service Type:</strong> {{ ucfirst(str_replace('_', ' ', $invoice->service_type)) }}</p>
                        <p><strong>Status:</strong> 
                            @if($invoice->status === 'paid')
                                <span class="badge bg-success">Paid</span>
                            @elseif($invoice->is_overdue)
                                <span class="badge bg-danger">Overdue</span>
                            @else
                                <span class="badge bg-warning">{{ ucfirst($invoice->status) }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-success">Dates & Payment</h6>
                        <p><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('M d, Y') }}</p>
                        <p><strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}</p>
                        @if($invoice->paid_at)
                            <p><strong>Paid Date:</strong> {{ $invoice->paid_at->format('M d, Y H:i') }}</p>
                            <p><strong>Payment Method:</strong> {{ ucfirst(str_replace('_', ' ', $invoice->payment_method)) }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="text-success">Description</h6>
                        <p>{{ $invoice->description }}</p>
                        @if($invoice->notes)
                            <h6 class="text-success">Notes</h6>
                            <p>{{ $invoice->notes }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6 offset-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Subtotal:</strong></td>
                                <td class="text-end">${{ number_format($invoice->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tax ({{ $invoice->tax_rate }}%):</strong></td>
                                <td class="text-end">${{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                            <tr class="border-top">
                                <td><strong>Total Amount:</strong></td>
                                <td class="text-end"><strong>${{ number_format($invoice->total_amount, 2) }}</strong></td>
                            </tr>
                            <tr>
                                <td><strong>Amount Paid:</strong></td>
                                <td class="text-end">${{ number_format($invoice->amount_paid, 2) }}</td>
                            </tr>
                            <tr class="border-top">
                                <td><strong>Balance Due:</strong></td>
                                <td class="text-end"><strong>${{ number_format($invoice->balance_due, 2) }}</strong></td>
                            </tr>
                        </table>
                    </div>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                            <input type="number" name="amount_paid" class="form-control" step="0.01" value="{{ $invoice->balance_due }}" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-success">Record Payment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function markPaid(invoiceId) {
            document.getElementById('paymentForm').action = `/billing/${invoiceId}/mark-paid`;
            new bootstrap.Modal(document.getElementById('markPaidModal')).show();
        }

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