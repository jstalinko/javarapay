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

        $saldo = \App\Models\Transaction::whereHas('project', function($q) use ($userId) {
            $q->where('user_id', $userId);
        })->whereNotNull('settled_at')->sum('total_amount');

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

        return Inertia::render('Dashboard', [
            'saldo' => (int) $saldo,
            'saldoTertunda' => (int) $saldoTertunda,
            'totalTransaksi' => $totalTransaksi,
            'totalProject' => $totalProject,
            'orders' => $orders,
            'projects' => $projects,
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
        return Inertia::render('Withdrawal');
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
