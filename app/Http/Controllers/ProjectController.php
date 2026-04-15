<?php

namespace App\Http\Controllers;

use App\Models\PaymentMethod;
use Illuminate\Http\Request;
use Inertia\inertia;
use App\Models\Project;

class ProjectController extends Controller
{
    public function store(Request $request)
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
    public function update(Request $request, \App\Models\Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'fee_type' => 'required|in:merchant,customer',
            'webhook_url' => 'nullable|url',
            'is_production' => 'boolean',
            'active' => 'boolean',
        ]);

        $project->update([
            'name' => $request->name,
            'fee_type' => $request->fee_type,
            'webhook_url' => $request->webhook_url,
            'is_production' => $request->is_production,
            'active' => $request->active,
        ]);

        return redirect()->back()->with('success', 'Project updated successfully.');
    }

    public function toggleProduction(\App\Models\Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        $project->update([
            'is_production' => !$project->is_production
        ]);

        return redirect()->back()->with('success', 'Project environment toggled successfully.');
    }

    public function channels(int $id)
    {
        $project = Project::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        
        $availableMethods = PaymentMethod::where('active', true)->get();
        $projectChannels = $project->channels ?: [];
        
        $channels = $availableMethods->map(function($method) use ($projectChannels) {
            $projectChannel = collect($projectChannels)->firstWhere('method_code', $method->method_code);
            
            return [
                'method_code' => $method->method_code,
                'method_name' => $method->method_name,
                'group' => $method->group,
                'image' => $method->image,
                'fee_percent' => $method->fee_percent,
                'fee_flat' => $method->fee_flat,
                'min_amount' => $method->min_amount,
                'max_amount' => $method->max_amount,
                'active' => $projectChannel ? (bool)($projectChannel['active'] ?? true) : true
            ];
        });

        return Inertia::render('Channels', [
            'project' => $project,
            'channels' => $channels
        ]);
    }

    public function updateChannels(Request $request, Project $project)
    {
        if ($project->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'channels' => 'required|array',
        ]);

        $project->update([
            'channels' => $request->channels
        ]);

        return redirect()->back()->with('success', 'Project channels updated successfully.');
    }

}
