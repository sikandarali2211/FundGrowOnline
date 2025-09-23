<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is authenticated
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        
        // Check if user has any of the required roles
        if (!in_array($user->role, $roles)) {
            // If user doesn't have required role, redirect to appropriate page
            if ($user->role === 'moderator') {
                return redirect()->route('admin.index')->with('error', 'You do not have permission to access this page.');
            }
            
            // For other roles or no role, redirect to dashboard
            return redirect()->route('admin.index')->with('error', 'Access denied. Insufficient permissions.');
        }

        return $next($request);
    }
}