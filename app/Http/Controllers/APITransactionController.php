<?php

namespace App\Http\Controllers;

use App\Http\Services\TripayService;
use App\Models\PaymentMethod;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Project;
use Illuminate\Support\Facades\Log;

class APITransactionController extends Controller
{
    public function create(Request $request)
    {
        /** @var Project $project */
        $project = $request->_project;
        $type = $request->type ?? 'api';

        if ($type == 'link') {
            // ── Payment Link flow (from dashboard) ──
            $order = Order::where('id', $request->order_id)->first();
            if (!$order) return response()->json(['success' => false, 'message' => 'Order not found'], 404);

            // Ensure the order belongs to this project
            if ($order->project_id !== $project->id) {
                return response()->json(['success' => false, 'message' => 'Order does not belong to this project.'], 403);
            }

            $invoice = "JPINV" . time() . substr(strtoupper(md5($project->name)), 0, 8);
            $amount = $order->amount;
            $note = $order->note ?? "Pembayaran Invoice " . $invoice;
        } else {
            // ── API flow (external merchant) ──
            $amount = $request->amount;
            if (!$amount || $amount < 1000) {
                return response()->json([
                    'success' => false,
                    'message' => 'Amount is required and must be at least 1000.',
                ], 422);
            }

            $invoice = $request->merchant_ref ?? "JPINV" . time() . substr(strtoupper(md5($project->name)), 0, 8);
            $note = $request->notes ?? "Pembayaran Invoice " . $invoice;

            // Create Order first to get an auto-generated txid
            $order = new Order();
            $order->project_id = $project->id;
            $order->user_id = $project->user_id;
            $order->amount = $amount;
            $order->note = $note;
            $order->save();
        }

        // ── Resolve payment method ──
        // Support both method_id and method_code
        $method = null;
        if ($request->method_id) {
            $method = PaymentMethod::find($request->method_id);
        } elseif ($request->method_code) {
            $method = PaymentMethod::where('method_code', $request->method_code)->first();
        }

        if (!$method) {
            return response()->json([
                'success' => false,
                'message' => 'Payment method not found.',
            ], 404);
        }

        // ── Build data ──
        $expectedTotalPrice = $amount;
        $customer_name = $request->customer_name ?? fake()->firstNameMale() . ' ' . fake()->lastName();
        $customer_email = $request->customer_email ?? 'customer' . rand(1000, 9999) . '@javara.digital';
        $customer_phone = $request->customer_phone ?? '081234567890';
        $sku = "XSKU-" . $expectedTotalPrice;
        $totalFee = (int) $method->totalFee($expectedTotalPrice);
        $totalAmount = (int) $method->totalPay($expectedTotalPrice);

        $payload = [
            'method'         => $method->method_code,
            'merchant_ref'   => $order->txid,
            'amount'         => $expectedTotalPrice,
            'customer_name'  => $customer_name,
            'customer_email' => $customer_email,
            'customer_phone' => $customer_phone,
            'order_items'    => [
                [
                    'sku'      => $sku,
                    'name'     => $note,
                    'price'    => $expectedTotalPrice,
                    'quantity' => 1,
                ]
            ],
            'expired_time' => time() + (24 * 60 * 60),
        ];

        $tripay = new TripayService();
        $res = $tripay->txcreate($payload);

        if (!($res['success'] ?? false)) {
            Log::error('Tripay txcreate failed', ['response' => $res, 'payload' => $payload]);
            return response()->json([
                'success' => false,
                'message' => $res['message'] ?? 'Failed to create transaction on payment gateway.',
            ], 500);
        }

        $tx = $res['data'] ?? [];

        // ── Save transaction ──
        $transaction = new Transaction();
        $transaction->project_id = $project->id;
        $transaction->merchant_ref = $invoice;
        $transaction->is_production = $project->is_production ?? false;
        $transaction->payment_method_code = $method->method_code;
        $transaction->payment_method_name = $method->method_name;
        $transaction->amount = $expectedTotalPrice;
        $transaction->total_fee = $totalFee;
        $transaction->total_amount = $totalAmount;
        $transaction->notes = $note;
        $transaction->status = 'UNPAID';
        $transaction->paid_at = null;
        $transaction->settled_at = null;

        // ── Always use the Order's txid for the Transaction ──
        // Both 'link' and 'api' flows create an Order first, which auto-generates
        // a txid via booted(). The Transaction shares the same txid.
        $transaction->txid = $order->txid;

        $transaction->save();


        return response()->json([
            'success' => true,
            'message' => 'Transaction created successfully.',
            'data' => [
                'txid'                => $transaction->txid,
                'merchant_ref'        => $transaction->merchant_ref,
                'amount'              => $transaction->amount,
                'total_fee'           => $transaction->total_fee,
                'total_amount'        => $transaction->total_amount,
                'payment_method_code' => $transaction->payment_method_code,
                'payment_method_name' => $transaction->payment_method_name,
                'status'              => $transaction->status,
                'pay_url'             => $tx['pay_url'] ?? null,
                'pay_code'            => $tx['pay_code'] ?? null,
                'qr_url'              => $tx['qr_url'] ?? null,
                'checkout_url'        => $tx['checkout_url'] ?? null,
                'reference'           => $tx['reference'] ?? null,
                'expired_time'        => $tx['expired_time'] ?? null,
            ],
        ]);
    }

    public function detail(Request $request, string $txid)
    {
        /** @var Project $project */
        $project = $request->_project;

        $transaction = Transaction::where('txid', $txid)
            ->where('project_id', $project->id)
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'txid'                => $transaction->txid,
                'merchant_ref'        => $transaction->merchant_ref,
                'amount'              => $transaction->amount,
                'total_fee'           => $transaction->total_fee,
                'total_amount'        => $transaction->total_amount,
                'payment_method_code' => $transaction->payment_method_code,
                'payment_method_name' => $transaction->payment_method_name,
                'notes'               => $transaction->notes,
                'status'              => $transaction->status,
                'paid_at'             => $transaction->paid_at,
                'settled_at'          => $transaction->settled_at,
                'created_at'          => $transaction->created_at,
            ],
        ]);
    }

    public function channels(Request $request)
    {
        /** @var Project $project */
        $project = $request->_project;

        $projectChannels = collect($project->channels ?: []);

        $activeChannels = $projectChannels->filter(function ($channel) {
            return !empty($channel['active']);
        })->map(function ($channel) {
            return [
                'method_code' => $channel['method_code'] ?? null,
                'method_name' => $channel['method_name'] ?? null,
                'group'       => $channel['group'] ?? null,
                'image'       => $channel['image'] ?? null,
                'fee_percent' => $channel['fee_percent'] ?? 0,
                'fee_flat'    => $channel['fee_flat'] ?? 0,
                'min_amount'  => $channel['min_amount'] ?? null,
                'max_amount'  => $channel['max_amount'] ?? null,
            ];
        })->values();

        return response()->json([
            'success' => true,
            'data' => $activeChannels,
        ]);
    }

    public function channelDetail(Request $request, string $code)
    {
        /** @var Project $project */
        $project = $request->_project;

        $channel = collect($project->channels ?: [])->first(function ($channel) use ($code) {
            return ($channel['method_code'] ?? null) === $code;
        });

        if (!$channel) {
            return response()->json([
                'success' => false,
                'message' => 'Payment channel not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'method_code' => $channel['method_code'] ?? null,
                'method_name' => $channel['method_name'] ?? null,
                'group'       => $channel['group'] ?? null,
                'image'       => $channel['image'] ?? null,
                'fee_percent' => $channel['fee_percent'] ?? 0,
                'fee_flat'    => $channel['fee_flat'] ?? 0,
                'min_amount'  => $channel['min_amount'] ?? null,
                'max_amount'  => $channel['max_amount'] ?? null,
                'active'      => !empty($channel['active']),
            ],
        ]);
    }
}
