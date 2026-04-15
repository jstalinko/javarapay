<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use App\Models\Order;
use App\Models\Project;
use App\Http\Services\TripayService;
use Illuminate\Support\Facades\Log;
class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'amount' => 'required|numeric|min:1000',
            'note' => 'nullable|string|max:255',
        ]);

        \App\Models\Order::create([
            'user_id' => auth()->id(),
            'project_id' => $request->project_id,
            'amount' => $request->amount,
            'note' => $request->note,
        ]);

        return redirect()->back()->with('success', 'Payment link created successfully.');
    }

    public function invoice(Request $request)
    {
        $order = Order::with('project')->where('project_id', $request->project_id)->where('txid', $request->txid)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        // Check if transaction already exists for this txid
        $transaction = Transaction::where('txid', $request->txid)->first();
        if ($transaction) {
            return Inertia::render('Invoice', [
                'transaction' => $transaction,
                'order' => $order,
            ]);
        }

        $projectChannels = $order->project->channels;
        
        if ($projectChannels) {
            $activeCodes = collect($projectChannels)->where('active', true)->pluck('method_code')->toArray();
        } else {
            $activeCodes = PaymentMethod::where('active', true)->pluck('method_code')->toArray();
        }

        $methods = PaymentMethod::whereIn('method_code', $activeCodes)->where('active', true)->get();

        $channels = $methods->filter(function($m) use ($order) {
            // Standard payment gateway logic: amount must be between min and max
            $min = $m->min_amount ?: 0;
            $max = $m->max_amount ?: PHP_INT_MAX;
            return $order->amount >= $min && $order->amount <= $max;
        })->map(function($m) {
            return [
                'code' => $m->method_code,
                'name' => $m->method_name,
                'type' => $m->group,
                'fee_percent' => $m->fee_percent,
                'fee_flat' => $m->fee_flat,
                'icon_url' => $m->image ? '/' . $m->image : null,
                'active' => true,
            ];
        });

        return Inertia::render('Payment', [
            'order' => $order,
            'channels' => $channels->values()->all(),
        ]);
    }

    public function pay(Request $request, $project_id, $txid)
    {
        $request->validate([
            'method_code' => 'required|string|exists:payment_methods,method_code',
        ]);

        $order = Order::with('project')->where('project_id', $project_id)->where('txid', $txid)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'Order not found.');
        }

        // Double check if transaction already exists
        $existing = Transaction::where('txid', $txid)->first();
        if ($existing) {
            return redirect()->route('payment.invoice', [$project_id, $txid]);
        }

        $method = PaymentMethod::where('method_code', $request->method_code)->first();
        
        $project = $order->project;
        $amount = $order->amount;
        $invoice = "JPINV" . time() . substr(strtoupper(md5($project->name)), 0, 8);
        $note = $order->note ?? "Pembayaran Invoice " . $invoice;

        // Build data for Tripay
        $totalFee = (int) $method->totalFee($amount);
        $totalAmount = (int) $method->totalPay($amount);

        $payload = [
            'method'         => $method->method_code,
            'merchant_ref'   => $order->txid,
            'amount'         => $amount,
            'customer_name'  => auth()->check() ? auth()->user()->name : 'Customer',
            'customer_email' => auth()->check() ? auth()->user()->email : 'customer@javara.digital',
            'customer_phone' => '081234567890',
            'order_items'    => [
                [
                    'sku'      => 'INV-' . $txid,
                    'name'     => $note,
                    'price'    => $amount,
                    'quantity' => 1,
                ]
            ],
            'expired_time' => time() + (24 * 60 * 60),
        ];

        $tripay = new TripayService();
        $res = $tripay->txcreate($payload);

        if (!($res['success'] ?? false)) {
            Log::error('Tripay txcreate failed in OrderController', ['response' => $res, 'payload' => $payload]);
            return redirect()->back()->with('error', $res['message'] ?? 'Failed to create transaction on payment gateway.');
        }

        $txData = $res['data'] ?? [];

        // Save transaction
        $transaction = new Transaction();
        $transaction->project_id = $project->id;
        $transaction->merchant_ref = $invoice;
        $transaction->is_production = $project->is_production ?? false;
        $transaction->payment_method_code = $method->method_code;
        $transaction->payment_method_name = $method->method_name;
        $transaction->amount = $amount;
        $transaction->total_fee = $totalFee;
        $transaction->total_amount = $totalAmount;
        $transaction->notes = $note;
        $transaction->status = 'UNPAID';
        
        // Link to order via txid
        $transaction->txid = $order->txid;
        
        // Store gateway details
        $transaction->reference = $txData['reference'] ?? null;
        $transaction->pay_url = $txData['pay_url'] ?? null;
        $transaction->pay_code = $txData['pay_code'] ?? null;
        $transaction->qr_url = $txData['qr_url'] ?? null;
        $transaction->expired_at = isset($txData['expired_time']) ? date('Y-m-d H:i:s', $txData['expired_time']) : null;
        
        $transaction->save();
        
        // Save additional details to some other place or maybe I should have added columns to Transaction.
        // Actually, APITransactionController returns these but doesn't seem to store them in DB.
        // Wait, if they are not in DB, how does Invoice page show them?
        // Ah, maybe I should add columns to transactions table.
        
        return redirect()->route('payment.invoice', [$project_id, $txid]);
    }




}
