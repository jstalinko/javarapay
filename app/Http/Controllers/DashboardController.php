<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
class DashboardController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        
        // Stats calculation
        $totalTransaksi = \App\Models\Transaction::whereHas('project', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->count();

        $totalProject = \App\Models\Project::where('user_id', $userId)->count();

        $user = \App\Models\User::find($userId);
        $saldo = $user->getAvailableBalance();

        $saldoTertunda = \App\Models\Transaction::whereHas('project', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->where('status', 'PAID')->whereNull('settled_at')->sum('total_amount');

        $orders = \App\Models\Order::where('user_id', $userId)
            ->where('created_at', '>=', now()->subHours(24))
            ->with('project')
            ->orderBy('created_at', 'desc')
            ->paginate(5); // Smaller page size for dashboard view

        $projects = \App\Models\Project::where('user_id', $userId)
            ->where('active', true)
            ->get();

        $announcements = \App\Models\Announcement::where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->paginate(3, ['*'], 'announcements_page');

        return Inertia::render('Dashboard', [
            'saldo' => (int) $saldo,
            'saldoTertunda' => (int) $saldoTertunda,
            'totalTransaksi' => $totalTransaksi,
            'totalProject' => $totalProject,
            'orders' => $orders,
            'projects' => $projects,
            'announcements' => $announcements,
        ]);
    }


    public function project()
    {
        $projects = \App\Models\Project::orderBy('id', 'desc')->get();
        return Inertia::render('Project', [
            'projects' => $projects
        ]);
    }

    public function transaction()
    {
        $transactions = \App\Models\Transaction::whereHas('project', function($query) {
            $query->where('user_id', auth()->id());
        })->with('project')->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('Transaction', [
            'transactions' => $transactions
        ]);
    }

    public function withdrawal()
    {
        $user = auth()->user();
        $banks = \App\Models\Bank::where('user_id', $user->id)->orderBy('is_primary', 'desc')->get();
        $withdrawals = \App\Models\Withdrawal::where('user_id', $user->id)->with('bank')->orderBy('created_at', 'desc')->paginate(10);
        
        return Inertia::render('Withdrawal', [
            'balance' => $user->getAvailableBalance(),
            'withdraw_fee_type' => $user->withdraw_fee_type,
            'withdraw_fee' => $user->withdraw_fee,
            'banks' => $banks,
            'withdrawals' => $withdrawals
        ]);
    }
    public function bank()
    {
        $banks = \App\Models\Bank::where('user_id', auth()->id())->orderBy('is_primary', 'desc')->get();
        return Inertia::render('Bank', [
            'banks' => $banks
        ]);
    }

    public function webhook()
    {
        $logs = \App\Models\WebhookLog::whereHas('project', function($query) {
            $query->where('user_id', auth()->id());
        })->with('project')->orderBy('created_at', 'desc')->paginate(10);

        return Inertia::render('WebhookLog', [
            'logs' => $logs
        ]);
    }

   
}
