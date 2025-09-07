<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request; // NEW
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule; // NEW

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     * URL can be /register?ref=ABC123
     */
    public function showRegistrationForm(Request $request)
    {
        // pass ref to view (auto-fill + lock)
        $ref = $request->query('ref');
        return view('auth.register', compact('ref'));
    }

    protected function validator(array $data)
    {
        // NOTE: Agar ?ref= hai to hum referral ko required kar dete hain (hidden ref_lock field se)
        return Validator::make($data, [
            // Referral code is OPTIONAL normally, REQUIRED if ref_lock present (i.e., came from referral link)
            'referral' => [
                'nullable',
                'string',
                'max:255',
                'exists:users,referral_code',
                Rule::requiredIf(fn() => isset($data['ref_lock']) && $data['ref_lock'] === '1'),
            ],
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone'    => ['required', 'string', 'max:20'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'referral.exists' => 'Invalid referral code.',
            'referral.required' => 'Referral code is required from this link.',
        ]);
    }

    protected function create(array $data)
    {
        $referrer = null;
        if (!empty($data['referral'])) {
            $referrer = \App\Models\User::where('referral_code', $data['referral'])->first();
        }

        return \App\Models\User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'],
            'password'      => \Illuminate\Support\Facades\Hash::make($data['password']),
            'referral_code' => \App\Models\User::generateReferralCode(),
            'referred_by'   => $referrer?->id,

            // 🔧 HOTFIX: store empty string instead of NULL
            'referral'      => $data['referral'] ?? '',
        ]);
    }
}
