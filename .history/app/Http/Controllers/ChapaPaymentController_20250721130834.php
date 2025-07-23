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
