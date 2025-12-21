<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckPermission
{
    /**
     * Handle an incoming request.
     * Usage: ->middleware('permission:create,BUILDING')
     */
    public function handle(Request $request, Closure $next, $action, $moduleCode)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $hasPermission = $user->roles()
            ->whereHas('permissions', function ($q) use ($action, $moduleCode) {
                $q->where('action', $action)
                  ->whereHas('module', function ($q2) use ($moduleCode) {
                      $q2->where('code', $moduleCode);
                  });
            })
            ->exists();

        if (!$hasPermission) {
            return response()->json([
                'message' => "Access denied: {$action} permission required for {$moduleCode}"
            ], 403);
        }

        return $next($request);
    }
}

