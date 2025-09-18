<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    /**
     * Redirect the user to Google's OAuth page
     */
    public function redirectToGoogle()
    {
        $ref = request()->get('ref'); // ✅ Get referral code from URL
        session(['google_ref' => $ref]); // ✅ Save in session
        return Socialite::driver('google')->redirect(); // redirect to Google login
    }

    /**
     * Handle the callback from Google
     */
    public function handleGoogleCallback()
    {
        try {
            // ✅ stateless to avoid session issue, user data from Google
            $googleUser = Socialite::driver('google')->stateless()->user();

            // ✅ Get referral code from session
            $refCode = session('google_ref');
            $referrer = User::where('referral_code', $refCode)->first();

            // ✅ Check if user already exists by email
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // ✅ New Google user — create with referral
                $user = User::create([
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => bcrypt(Str::random(24)), // Random password
                    'phone' => '', // ✅ Add phone field with empty string
                    'utype' => 'USR', // ✅ Add default user type
                    'referral_code' => $this->generateUniqueReferralCode(),
                    'referred_by' => $referrer ? $referrer->id : null, // ✅ save referrer if found
                    'referral' => $refCode ?? '', // ✅ Add referral field
                    'level' => 1, // ✅ Add default level
                ]);
            } else {
                // ✅ Update existing user if fields are missing
                if (empty($user->google_id)) {
                    $user->google_id = $googleUser->getId();
                }

                if (empty($user->referral_code)) {
                    $user->referral_code = $this->generateUniqueReferralCode();
                }

                if (empty($user->referred_by) && $referrer) {
                    $user->referred_by = $referrer->id;
                }

                if (empty($user->referral) && $refCode) {
                    $user->referral = $refCode;
                }

                if (empty($user->level)) {
                    $user->level = 1;
                }

                if (empty($user->phone)) {
                    $user->phone = '';
                }

                if (empty($user->utype)) {
                    $user->utype = 'USR';
                }

                $user->save();
            }

            Auth::login($user);
            return redirect()->intended('/User-dashboard');
        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Failed to login with Google: ' . $e->getMessage());
        }
    }

    /**
     * Generate a unique referral code
     */
    private function generateUniqueReferralCode(): string
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (User::where('referral_code', $code)->exists());

        return $code;
    }
}
