<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WebhookController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $json = $request->getContent();
        $data = json_decode($json, true);

        $merchantRef = $data['merchant_ref'] ?? null;
        $statusRaw   = $data['status'] ?? null;
        $reference   = $data['reference'] ?? null;

        if (!$merchantRef || !$statusRaw) {
            return response()->json(['success' => false, 'message' => 'Invalid payload'], 422);
        }

        $status = strtoupper($statusRaw);

        // Find transaction by txid since we sent txid (with JPAY- prefix) as merchant_ref to Tripay
        $transaction = Transaction::where('txid', $merchantRef)->first();

        if (!$transaction) {
            return response()->json(['success' => false, 'message' => 'Transaction not found'], 404);
        }

        // Idempotency check
        if (in_array($transaction->status, ['PAID', 'SETTLED', 'EXPIRED'])) {
            // Update status if it transitions from PAID to SETTLED
            if ($transaction->status === 'PAID' && $status === 'SETTLED') {
                $transaction->status = $status;
                $transaction->settled_at = now();
                $transaction->save();
                return response()->json(['success' => true, 'message' => 'Transaction settled']);
            }
            
            return response()->json(['success' => true, 'message' => 'Transaction already processed']);
        }

        try {
            $transaction->status = $status;
            
            if ($reference) {
                $transaction->reference = $reference;
            }

            if ($status === 'PAID') {
                $transaction->paid_at = now();
                
                // TODO: We could forward to the project's webhook_url here 
                // if we want to notify the merchant's system.

                $this->sendWebhook($transaction);
            }

            $transaction->save();

            return response()->json(['success' => true, 'message' => 'Webhook processed successfully']);
        } catch (\Throwable $e) {
            Log::error('JavaraPay Webhook Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server Error'], 500);
        }
    }

    public function sendWebhook(Transaction $transaction)
    {
        $project = $transaction->project;
        if (!$project || !$project->webhook_url) {
            return;
        }

        $data = [
            'merchant_ref' => $transaction->merchant_ref,
            'status' => $transaction->status,
            'reference' => $transaction->reference,
            'pay_url' => $transaction->pay_url,
            'pay_code' => $transaction->pay_code,
            'qr_url' => $transaction->qr_url,
            'expired_at' => $transaction->expired_at,
            'paid_at' => $transaction->paid_at,
            'settled_at' => $transaction->settled_at,
        ];

        $webhookLog = \App\Models\WebhookLog::create([
            'project_id' => $project->id,
            'transaction_id' => $transaction->id,
            'payload' => json_encode($data),
            'last_sent_at' => now(),
            'retries' => 1,
        ]);

        try {
            $response = Http::timeout(10)->post($project->webhook_url, $data);
            
            $webhookLog->response_code = $response->status();
            $webhookLog->response_body = $response->body();
            $webhookLog->save();

            if ($response->successful()) {
                Log::info('Webhook sent successfully to ' . $project->webhook_url);
            } else {
                Log::error('Webhook failed to ' . $project->webhook_url . ' with status ' . $response->status());
            }
        } catch (\Throwable $e) {
            $webhookLog->response_code = 500;
            $webhookLog->response_body = $e->getMessage();
            $webhookLog->save();
            
            Log::error('Webhook failed to ' . $project->webhook_url . ' with error ' . $e->getMessage());
        }
    }
}
