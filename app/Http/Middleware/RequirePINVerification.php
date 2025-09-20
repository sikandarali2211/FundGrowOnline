<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\SecurityController;

class RequirePINVerification
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
            
            // Check if user has completed PIN setup
            if (!$user->hasCompletedPINSetup()) {
                return redirect()->route('security.pin.setup')
                    ->with('info', 'Please set up your security PIN first.');
            }

            // Check if PIN is verified for current session
            if (!SecurityController::isPINVerified()) {
                // Store the intended URL in session
                session(['intended_url' => $request->fullUrl()]);
                
                return redirect()->route('security.pin.verify')
                    ->with('info', 'Please verify your security PIN to continue.');
            }
        }

        return $next($request);
    }
}