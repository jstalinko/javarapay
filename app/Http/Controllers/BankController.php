<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BankController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'bank_name' => 'required|string|max:255',
            'account_name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'is_primary' => 'boolean',
        ]);

        if ($request->is_primary) {
            \App\Models\Bank::where('user_id', auth()->id())->update(['is_primary' => false]);
        }

        \App\Models\Bank::create([
            'user_id' => auth()->id(),
            'bank_name' => $request->bank_name,
            'account_name' => $request->account_name,
            'account_number' => $request->account_number,
            'is_primary' => $request->is_primary ?? false,
        ]);

        return redirect()->back()->with('success', 'Bank added successfully.');
    }

}
