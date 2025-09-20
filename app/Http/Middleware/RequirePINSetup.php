<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequirePINSetup
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user needs to setup PIN
            if (!$user->hasCompletedPINSetup()) {
                // Skip PIN setup check for PIN setup routes and logout
                if (!$request->routeIs('security.pin.*') && !$request->routeIs('logout')) {
                    return redirect()->route('security.pin.setup')
                        ->with('info', 'Please set up your security PIN to continue.');
                }
            }
        }

        return $next($request);
    }
}