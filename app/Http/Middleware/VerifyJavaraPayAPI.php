<?php

namespace App\Http\Middleware;

use App\Models\Project;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyJavaraPayAPI
{
    /**
     * Validate X-JAVARAPAY-API and X-JAVARAPAY-MERCHANT-CODE headers,
     * resolve the Project, and bind it to the request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $apiKey = $request->header('X-JAVARAPAY-API');
        $merchantCode = $request->header('X-JAVARAPAY-MERCHANT-CODE');

        if (!$apiKey || !$merchantCode) {
            return response()->json([
                'success' => false,
                'message' => 'Missing authentication headers: X-JAVARAPAY-API and X-JAVARAPAY-MERCHANT-CODE are required.',
            ], 401);
        }

        $project = Project::where('apikey', $apiKey)
            ->where('merchant_code', $merchantCode)
            ->first();

        if (!$project) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid API Key or Merchant Code.',
            ], 401);
        }

        if (!$project->active) {
            return response()->json([
                'success' => false,
                'message' => 'Project is not active.',
            ], 403);
        }

        // Bind project to request so controllers can access it
        $request->merge(['_project' => $project]);

        return $next($request);
    }
}
