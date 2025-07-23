<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class ChapaPaymentController extends Controller
{
    protected $secretKey;
    protected $publicKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.chapa.secret_key');
        $this->publicKey = config('services.chapa.public_key');
        $this->baseUrl = config('services.chapa.base_url');
    }

    public function initiatePayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'currency' => 'required|string|in:ETB,USD',
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'tx_ref' => 'required|string|unique:invoices,chapa_tx_ref', // Ensure unique transaction reference
            'callback_url' => 'required|url',
            'return_url' => 'required|url',
            'invoice_id' => 'required|exists:invoices,id',
        ]);

        $data = [
            'amount' => $request->amount,
            'currency' => $request->currency,
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'tx_ref' => $request->tx_ref,
            'callback_url' => $request->callback_url,
            'return_url' => $request->return_url,
            'customization[title]' => 'Payment for Invoice',
            'customization[description]' => 'Payment for invoice #' . $request->invoice_id,
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . 'initialize', $data);

            if ($response->successful()) {
                $invoice = Invoice::find($request->invoice_id);
                if ($invoice) {
                    $invoice->chapa_tx_ref = $request->tx_ref;
                    $invoice->chapa_checkout_url = $response->json('data.checkout_url');
                    $invoice->save();
                }

                return response()->json($response->json());
            } else {
                Log::error('Chapa Payment Initiation Failed: ' . $response->body());
                return response()->json(['message' => 'Payment initiation failed', 'error' => $response->json()], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Chapa Payment Initiation Exception: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred during payment initiation', 'error' => $e->getMessage()], 500);
        }
    }

    public function verifyPayment(Request $request, $tx_ref)
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . 'verify/' . $tx_ref);

            if ($response->successful()) {
                $paymentData = $response->json('data');

                $invoice = Invoice::where('chapa_tx_ref', $tx_ref)->first();

                if ($invoice && $paymentData['status'] === 'success' && $paymentData['currency'] === $invoice->currency && $paymentData['amount'] >= $invoice->total_amount) {
                    $invoice->payment_status = 'paid';
                    $invoice->chapa_payment_status = $paymentData['status'];
                    $invoice->chapa_transaction_id = $paymentData['id'];
                    $invoice->chapa_payment_method = $paymentData['method'];
                    $invoice->chapa_paid_at = now();
                    $invoice->save();

                    // Optionally update invoice items or related records here
                    // For example, mark associated visit services as paid if applicable

                    return response()->json(['message' => 'Payment verified and invoice updated successfully', 'invoice' => $invoice]);
                } else {
                    Log::warning('Chapa Payment Verification Failed or Mismatched: ' . json_encode($paymentData));
                    return response()->json(['message' => 'Payment verification failed or data mismatch', 'payment_data' => $paymentData], 400);
                }
            } else {
                Log::error('Chapa Payment Verification API Failed: ' . $response->body());
                return response()->json(['message' => 'Payment verification failed with Chapa API', 'error' => $response->json()], $response->status());
            }
        } catch (\Exception $e) {
            Log::error('Chapa Payment Verification Exception: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred during payment verification', 'error' => $e->getMessage()], 500);
        }
    }

    public function handleWebhook(Request $request)
    {
        // Chapa webhooks typically send a POST request with transaction details.
        // You need to verify the webhook signature or the transaction reference.
        // For simplicity, this example assumes you'll verify the transaction using tx_ref.

        $tx_ref = $request->input('tx_ref');

        if (!$tx_ref) {
            return response()->json(['message' => 'Missing tx_ref in webhook payload'], 400);
        }

        // Call the verifyPayment method to handle the verification logic
        return $this->verifyPayment($request, $tx_ref);
    }
}
