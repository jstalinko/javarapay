<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Transaction;
use App\Models\Withdrawal;
use App\Models\Announcement;

class AdminController extends Controller
{
    public function index()
    {
        $totalTransactions = Transaction::count();
        $totalAmountTransactions = Transaction::where('status', 'PAID')->sum('total_amount');
        
        $totalWithdrawals = Withdrawal::count();
        $totalAmountWithdrawals = Withdrawal::whereIn('status', ['PAID', 'PENDING'])->sum('amount');

        return Inertia::render('Admin/Dashboard', [
            'totalTransactions' => $totalTransactions,
            'totalAmountTransactions' => (int) $totalAmountTransactions,
            'totalWithdrawals' => $totalWithdrawals,
            'totalAmountWithdrawals' => (int) $totalAmountWithdrawals,
        ]);
    }

    public function projectsReview()
    {
        $projects = \App\Models\Project::with('user')->where('status', 'in_review')->orderBy('created_at', 'desc')->paginate(10);
        return Inertia::render('Admin/ProjectReview', [
            'projects' => $projects
        ]);
    }

    public function updateProjectStatus(Request $request, \App\Models\Project $project)
    {
        $request->validate([
            'status' => 'required|in:approve,decline,banned,in_review'
        ]);

        $project->update([
            'status' => $request->status,
            'active' => $request->status === 'approve'
        ]);

        return back()->with('success', 'Project status updated successfully.');
    }

    public function usersManagement(Request $request)
    {
        $query = \App\Models\User::orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(10)->withQueryString();
        
        return Inertia::render('Admin/UserManagement', [
            'users' => $users,
            'filters' => $request->only('search')
        ]);
    }

    public function updateUser(Request $request, \App\Models\User $user)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,banned',
            'change_password' => 'nullable|min:8'
        ]);

        $user->status = $request->status;
        
        if ($request->filled('change_password')) {
            $user->password = \Illuminate\Support\Facades\Hash::make($request->change_password);
        }

        $user->save();

        return back()->with('success', 'User updated successfully.');
    }

    public function transactionsManagement(Request $request)
    {
        $query = \App\Models\Transaction::with(['project.user'])->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('txid', 'like', "%{$search}%")
                  ->orWhere('merchant_ref', 'like', "%{$search}%")
                  ->orWhereHas('project', function($qProject) use ($search) {
                      $qProject->where('name', 'like', "%{$search}%")
                               ->orWhereHas('user', function($qUser) use ($search) {
                                   $qUser->where('name', 'like', "%{$search}%");
                               });
                  });
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $transactions = $query->paginate(10)->withQueryString();

        return Inertia::render('Admin/Transactions', [
            'transactions' => $transactions,
            'filters' => $request->only(['search', 'start_date', 'end_date'])
        ]);
    }

    public function withdrawalsManagement(Request $request)
    {
        $query = \App\Models\Withdrawal::with(['user', 'bank'])->orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhereHas('user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $withdrawals = $query->paginate(10)->withQueryString();

        return Inertia::render('Admin/Withdrawals', [
            'withdrawals' => $withdrawals,
            'filters' => $request->only(['search', 'start_date', 'end_date'])
        ]);
    }

    public function updateWithdrawalStatus(Request $request, \App\Models\Withdrawal $withdrawal)
    {
        $request->validate([
            'status' => 'required|in:PAID,PENDING,REJECTED,CANCELLED',
            'notes' => 'nullable|string'
        ]);

        $withdrawal->update([
            'status' => $request->status,
            'notes' => $request->notes
        ]);

        return back()->with('success', 'Withdrawal updated successfully.');
    }

    public function announcementsManagement(Request $request)
    {
        $query = Announcement::orderBy('created_at', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $announcements = $query->paginate(10)->withQueryString();

        return Inertia::render('Admin/Announcements', [
            'announcements' => $announcements,
            'filters' => $request->only('search')
        ]);
    }

    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'content' => 'required|string',
            'is_published' => 'boolean'
        ]);

        $validated['category'] = $validated['category'] ?: 'common';
        $validated['is_published'] = $request->boolean('is_published');

        Announcement::create($validated);

        return back()->with('success', 'Announcement created successfully.');
    }

    public function updateAnnouncement(Request $request, Announcement $announcement)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'content' => 'required|string',
            'is_published' => 'boolean'
        ]);

        $validated['category'] = $validated['category'] ?: 'common';
        $validated['is_published'] = $request->boolean('is_published');

        $announcement->update($validated);

        return back()->with('success', 'Announcement updated successfully.');
    }

    public function deleteAnnouncement(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Announcement deleted successfully.');
    }

    public function paymentChannel(Request $request)
    {
        return Inertia::render('Admin/PaymentChannels', [
            'paymentChannels' => \App\Models\PaymentMethod::all()
        ]);
    }

    public function syncAllProjectsChannels()
    {
        $this->performSync();
        return back()->with('success', 'All project channels synchronized successfully.');
    }

    protected function performSync()
    {
        $allMethods = \App\Models\PaymentMethod::all();
        $projects = \App\Models\Project::all();

        foreach ($projects as $project) {
            $currentChannels = $project->channels ?: [];
            $newChannels = [];

            foreach ($allMethods as $method) {
                // If globally inactive, exclude from project channels
                if (!$method->active) {
                    continue;
                }

                $existing = collect($currentChannels)->firstWhere('method_code', $method->method_code);

                $newChannels[] = [
                    'method_code' => $method->method_code,
                    'method_name' => $method->method_name,
                    'group'       => $method->group,
                    'image'       => $method->image,
                    'fee_percent' => (string) $method->fee_percent,
                    'fee_flat'    => (int) $method->fee_flat,
                    'min_amount'  => $method->min_amount !== null ? (int) $method->min_amount : null,
                    'max_amount'  => $method->max_amount !== null ? (int) $method->max_amount : null,
                    'active'      => $existing ? (bool)($existing['active'] ?? true) : true
                ];
            }

            $project->update([
                'channels' => $newChannels
            ]);
        }
    }

    public function storePaymentChannel(Request $request)
    {
        $request->validate([
            'group' => 'nullable|string|max:255',
            'method_code' => 'required|string|max:255|unique:payment_methods,method_code',
            'method_name' => 'required|string|max:255',
            'gateway' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'is_qris' => 'boolean',
            'min_amount' => 'nullable|integer|min:0',
            'max_amount' => 'nullable|integer|min:0',
            'fee_percent' => 'required|numeric|min:0|max:100',
            'fee_flat' => 'required|integer|min:0',
            'active' => 'boolean',
        ]);

        $imagePath = $request->image;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dir = public_path('images/channels');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $file->move($dir, $fileName);
            $imagePath = 'images/channels/' . $fileName;
        }

        \App\Models\PaymentMethod::create([
            'group' => $request->group,
            'method_code' => $request->method_code,
            'method_name' => $request->method_name,
            'gateway' => $request->gateway,
            'image' => $imagePath,
            'description' => $request->description,
            'is_qris' => $request->boolean('is_qris'),
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
            'fee_percent' => $request->fee_percent,
            'fee_flat' => $request->fee_flat,
            'active' => $request->boolean('active'),
            'destination' => 'automatic',
            'destination_name' => 'automatic',
        ]);

        $this->performSync();

        return back()->with('success', 'Payment channel created successfully.');
    }

    public function updatePaymentChannel(Request $request, $id)
    {
        $paymentChannel = \App\Models\PaymentMethod::findOrFail($id);

        $request->validate([
            'group' => 'nullable|string|max:255',
            'method_code' => 'required|string|max:255|unique:payment_methods,method_code,' . $paymentChannel->id,
            'method_name' => 'required|string|max:255',
            'gateway' => 'required|string|max:255',
            'image' => 'nullable|string|max:255',
            'image_file' => 'nullable|image|max:2048',
            'description' => 'nullable|string',
            'is_qris' => 'boolean',
            'min_amount' => 'nullable|integer|min:0',
            'max_amount' => 'nullable|integer|min:0',
            'fee_percent' => 'required|numeric|min:0|max:100',
            'fee_flat' => 'required|integer|min:0',
            'active' => 'boolean',
        ]);

        $imagePath = $request->image;
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $dir = public_path('images/channels');
            if (!file_exists($dir)) {
                mkdir($dir, 0755, true);
            }
            $file->move($dir, $fileName);
            $imagePath = 'images/channels/' . $fileName;
        }

        $paymentChannel->update([
            'group' => $request->group,
            'method_code' => $request->method_code,
            'method_name' => $request->method_name,
            'gateway' => $request->gateway,
            'image' => $imagePath,
            'description' => $request->description,
            'is_qris' => $request->boolean('is_qris'),
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
            'fee_percent' => $request->fee_percent,
            'fee_flat' => $request->fee_flat,
            'active' => $request->boolean('active'),
        ]);

        $this->performSync();

        return back()->with('success', 'Payment channel updated successfully.');
    }

    public function deletePaymentChannel($id)
    {
        $paymentChannel = \App\Models\PaymentMethod::findOrFail($id);
        $paymentChannel->delete();

        $this->performSync();

        return back()->with('success', 'Payment channel deleted successfully.');
    }
}
