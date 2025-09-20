<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class SecurityController extends Controller
{
    /**
     * Show PIN setup page after registration
     */
    public function showPINSetup()
    {
        $user = Auth::user();
        
        // If user has already completed PIN setup, redirect to dashboard
        if ($user->hasCompletedPINSetup()) {
            return redirect()->route('user.index');
        }

        return view('auth.security-pin-setup');
    }

    /**
     * Handle PIN setup with OTP verification
     */
    public function setupPIN(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'security_pin' => ['required', 'string', 'min:6', 'max:6', 'regex:/^[0-9]{6}$/'],
            'security_pin_confirmation' => ['required', 'same:security_pin'],
            'otp_code' => ['required', 'string', 'min:6', 'max:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'security_pin.required' => 'Security PIN is required.',
            'security_pin.min' => 'Security PIN must be exactly 6 digits.',
            'security_pin.max' => 'Security PIN must be exactly 6 digits.',
            'security_pin.regex' => 'Security PIN must contain only numbers.',
            'security_pin_confirmation.same' => 'PIN confirmation does not match.',
            'otp_code.required' => 'OTP code is required.',
            'otp_code.min' => 'OTP code must be exactly 6 digits.',
            'otp_code.max' => 'OTP code must be exactly 6 digits.',
            'otp_code.regex' => 'OTP code must contain only numbers.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Verify OTP
        if (!$user->verifyOTP($request->otp_code)) {
            return back()->withErrors(['otp_code' => 'Invalid or expired OTP code.'])->withInput();
        }

        // Set security PIN
        $user->setSecurityPIN($request->security_pin);
        $user->clearOTP();

        return redirect()->route('user.index')->with('success', 'Security PIN has been set up successfully!');
    }

    /**
     * Send OTP for PIN setup (Email only)
     */
    public function sendOTPForPINSetup(Request $request)
    {
        $user = Auth::user();
        
        // Send OTP via email only
        $results = $user->sendOTP(['email']);
        
        if (isset($results['email']) && $results['email']) {
            $message = 'OTP has been sent to your email address successfully!';
            $success = true;
        } else {
            $message = 'Failed to send OTP via email. Please try again.';
            $success = false;
        }

        return response()->json([
            'success' => $success,
            'message' => $message,
            'results' => $results
        ]);
    }

    /**
     * Show PIN verification page for sensitive operations
     */
    public function showPINVerification()
    {
        $user = Auth::user();
        
        if (!$user->hasCompletedPINSetup()) {
            return redirect()->route('security.pin.setup');
        }

        return view('auth.security-pin-verification');
    }

    /**
     * Verify PIN for sensitive operations
     */
    public function verifyPIN(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'security_pin' => ['required', 'string', 'min:6', 'max:6', 'regex:/^[0-9]{6}$/'],
        ], [
            'security_pin.required' => 'Security PIN is required.',
            'security_pin.min' => 'Security PIN must be exactly 6 digits.',
            'security_pin.max' => 'Security PIN must be exactly 6 digits.',
            'security_pin.regex' => 'Security PIN must contain only numbers.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator);
        }

        // Verify PIN
        if (!$user->verifySecurityPIN($request->security_pin)) {
            return back()->withErrors(['security_pin' => 'Invalid security PIN.']);
        }

        // Store PIN verification in session for 5 minutes
        session(['pin_verified' => true, 'pin_verified_at' => now()]);

        // Redirect to intended URL or dashboard
        $intendedUrl = session('intended_url', route('user.index'));
        session()->forget('intended_url');
        
        return redirect($intendedUrl)->with('success', 'Security PIN verified successfully!');
    }

    /**
     * Check if PIN is verified for current session
     */
    public static function isPINVerified(): bool
    {
        if (!session('pin_verified')) {
            return false;
        }

        $verifiedAt = session('pin_verified_at');
        if (!$verifiedAt) {
            return false;
        }

        // PIN verification expires after 5 minutes
        return now()->diffInMinutes($verifiedAt) < 5;
    }

    /**
     * Clear PIN verification from session
     */
    public function clearPINVerification()
    {
        session()->forget(['pin_verified', 'pin_verified_at']);
        
        return response()->json(['success' => true]);
    }

    /**
     * Show PIN change page
     */
    public function showPINChange()
    {
        $user = Auth::user();
        
        if (!$user->hasCompletedPINSetup()) {
            return redirect()->route('security.pin.setup');
        }

        return view('auth.security-pin-change');
    }

    /**
     * Handle PIN change
     */
    public function changePIN(Request $request)
    {
        $user = Auth::user();
        
        $validator = Validator::make($request->all(), [
            'current_pin' => ['required', 'string', 'min:6', 'max:6', 'regex:/^[0-9]{6}$/'],
            'new_pin' => ['required', 'string', 'min:6', 'max:6', 'regex:/^[0-9]{6}$/'],
            'new_pin_confirmation' => ['required', 'same:new_pin'],
        ], [
            'current_pin.required' => 'Current PIN is required.',
            'current_pin.min' => 'Current PIN must be exactly 6 digits.',
            'current_pin.max' => 'Current PIN must be exactly 6 digits.',
            'current_pin.regex' => 'Current PIN must contain only numbers.',
            'new_pin.required' => 'New PIN is required.',
            'new_pin.min' => 'New PIN must be exactly 6 digits.',
            'new_pin.max' => 'New PIN must be exactly 6 digits.',
            'new_pin.regex' => 'New PIN must contain only numbers.',
            'new_pin_confirmation.same' => 'New PIN confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        // Verify current PIN
        if (!$user->verifySecurityPIN($request->current_pin)) {
            return back()->withErrors(['current_pin' => 'Current PIN is incorrect.'])->withInput();
        }

        // Set new PIN
        $user->security_pin = $request->new_pin;
        $user->save();

        return redirect()->route('user.index')->with('success', 'Security PIN has been changed successfully!');
    }
}