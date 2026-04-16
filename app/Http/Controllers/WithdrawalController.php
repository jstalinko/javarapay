<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Withdrawal;
use App\Models\Bank;
use Illuminate\Validation\ValidationException;

class WithdrawalController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:50000',
            'bank_id' => 'required|exists:banks,id',
        ]);

        $user = auth()->user();
        
        // Ensure bank belongs to user
        $bank = Bank::where('id', $request->bank_id)->where('user_id', $user->id)->firstOrFail();

        $amount = (int) $request->amount;

        // Check if user has sufficient available balance
        if ($user->getAvailableBalance() < $amount) {
            throw ValidationException::withMessages([
                'amount' => 'Insufficient available balance.',
            ]);
        }

        // Calculate fee
        $fee = $user->calculateWithdrawalFee($amount);
        $receivedAmount = $amount - $fee;

        if ($receivedAmount <= 0) {
            throw ValidationException::withMessages([
                'amount' => 'Withdrawal amount must be greater than the fee.',
            ]);
        }

        // Create the withdrawal record (status defaults to PENDING)
        Withdrawal::create([
            'user_id' => $user->id,
            'bank_id' => $bank->id,
            'amount' => $amount,
            'fee' => $fee,
            'received_amount' => $receivedAmount,
            'reference' => 'WD-' . strtoupper(uniqid()),
            'status' => 'PENDING',
        ]);

        return redirect()->back()->with('success', 'Withdrawal request submitted successfully.');
    }
}
