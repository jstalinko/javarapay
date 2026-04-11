<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard');
    }

    public function project()
    {
        $projects = \App\Models\Project::orderBy('id', 'desc')->get();
        return Inertia::render('Project', [
            'projects' => $projects
        ]);
    }

    public function projectStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'fee_type' => 'required|in:merchant,customer',
            'webhook_url' => 'nullable|url',
        ]);

        $project = new \App\Models\Project();
        $project->user_id = auth()->id();
        $project->name = $request->name;
        $project->fee_type = $request->fee_type;
        $project->webhook_url = $request->webhook_url;
        $project->apikey = \Illuminate\Support\Str::random(32);
        $project->merchant_code = 'MC' . strtoupper(\Illuminate\Support\Str::random(10));
        $project->status = 'in_review';
        $project->active = true;
        $project->save();

        return redirect()->back()->with('success', 'Project created successfully.');
    }
    public function transaction()
    {
        return Inertia::render('Transaction');
    }
    public function withdrawal()
    {
        return Inertia::render('Withdrawal');
    }
    public function bank()
    {
        return Inertia::render('Bank');
    }
    public function webhook()
    {
        return Inertia::render('Webhook');
    }

   
}
