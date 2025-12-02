<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\PaymentTransaction;
use App\Services\AzamPayService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $azamPay;

    public function __construct(AzamPayService $azamPay)
    {
        $this->azamPay = $azamPay;
    }

    /**
     * Show Payment Options for Invoice
     */
    public function checkout(Invoice $invoice)
    {
        // Security check: Ensure user can view this invoice
        // (Assuming middleware or policy handles this, but simple check here)
        if (auth()->check() && auth()->user()->isClient()) {
             // Check invoice ownership logic here
        }

        return view('payments.checkout', compact('invoice'));
    }

    /**
     * Process Mobile Money Payment
     */
    public function payMobile(Request $request, Invoice $invoice)
    {
        $request->validate([
            'provider' => 'required|in:Airtel,Tigo,Halotel,AzamPesa',
            'phone_number' => 'required|string', // Format 255...
        ]);

        try {
            $reference = 'TXN-' . Str::random(12);
            
            // Create Transaction Record
            $transaction = PaymentTransaction::create([
                'invoice_id' => $invoice->id,
                'transaction_reference' => $reference,
                'amount' => $invoice->balance_due, // or request amount
                'currency' => 'TZS',
                'provider' => $request->provider,
                'account_number' => $request->phone_number,
                'status' => 'pending',
            ]);

            // Initiate Payment
            $response = $this->azamPay->mobileCheckout(
                $request->phone_number,
                $transaction->amount,
                $reference,
                $request->provider
            );

            if ($response['success'] ?? false) {
                $transaction->update(['external_reference' => $response['transactionId'] ?? null]);
                return response()->json([
                    'success' => true,
                    'message' => 'Payment request sent to your phone. Please enter PIN to confirm.',
                    'reference' => $reference
                ]);
            } else {
                $transaction->update([
                    'status' => 'failed', 
                    'payment_details' => $response
                ]);
                return response()->json([
                    'success' => false, 
                    'message' => $response['message'] ?? 'Payment failed'
                ], 400);
            }

        } catch (\Exception $e) {
            Log::error('Payment Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'System error processing payment'], 500);
        }
    }

    /**
     * Handle Callback from AzamPay
     */
    public function callback(Request $request)
    {
        // Verify signature if applicable, AzamPay usually sends JSON body
        $data = $request->all();
        Log::info('AzamPay Callback:', $data);

        $externalId = $data['externalId'] ?? null; // Matches our transaction_reference
        $status = $data['status'] ?? null; // success, failed

        if ($externalId) {
            $transaction = PaymentTransaction::where('transaction_reference', $externalId)->first();

            if ($transaction) {
                if ($status === 'success' || $data['success'] === true) {
                    $transaction->update([
                        'status' => 'success',
                        'paid_at' => now(),
                        'payment_details' => $data
                    ]);

                    // Mark Invoice as Paid
                    $invoice = $transaction->invoice;
                    $invoice->markAsPaid('AzamPay - ' . $transaction->provider);
                } else {
                    $transaction->update([
                        'status' => 'failed',
                        'payment_details' => $data
                    ]);
                }
            }
        }

        return response()->json(['success' => true]);
    }
}
