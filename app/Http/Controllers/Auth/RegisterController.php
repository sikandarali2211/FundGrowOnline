<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/security/pin/setup';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm(Request $request)
    {
        $ref = $request->query('ref');
        return view('auth.register', compact('ref'));
    }

    protected function validator(array $data)
    {
        // ✅ Allowed public domains (add/remove as you like)
        $allowedDomains = [
            'gmail.com', 'googlemail.com',
            'yahoo.com', 'yahoo.com.pk', 'yahoo.co.uk',
            'hotmail.com', 'outlook.com', 'live.com', 'msn.com',
            'icloud.com',
            'proton.me', 'protonmail.com',
            'aol.com',
            'gmx.com', 'gmx.net',
            'mail.com',
        ];

        return Validator::make($data, [
            'referral' => [
                'nullable','string','max:255','exists:users,referral_code',
                Rule::requiredIf(fn() => isset($data['ref_lock']) && $data['ref_lock'] === '1'),
            ],
            'name'  => ['required','string','max:255'],
            // 👇 email:rfc,dns = valid format + domain must have DNS (MX/A)
            'email' => [
                'required','string','email:rfc,dns','max:255','unique:users',
                function ($attribute, $value, $fail) use ($allowedDomains) {
                    $parts = explode('@', strtolower($value));
                    if (count($parts) !== 2) {
                        return $fail('Please enter a valid email address.');
                    }

                    $domain = $parts[1];

                    // Allow any yahoo.<tld> (e.g., yahoo.in, yahoo.com.pk, yahoo.co.uk)
                    $isYahoo = preg_match('/^yahoo\.[a-z.]+$/i', $domain) === 1;

                    if (!in_array($domain, $allowedDomains, true) && !$isYahoo) {
                        return $fail('Please use a public email like Gmail, Yahoo, Hotmail/Outlook, etc.');
                    }
                },
            ],
            'phone'    => ['required','string','max:20'],
            'password' => ['required','string','min:8','confirmed'],
        ], [
            'referral.exists'    => 'Invalid referral code.',
            'referral.required'  => 'Referral code is required from this link.',
            'email.email'        => 'Please enter a valid email address.',
            'email.unique'       => 'This email is already registered.',
        ]);
    }

    protected function create(array $data)
    {
        $referrer = null;
        if (!empty($data['referral'])) {
            $referrer = User::where('referral_code', $data['referral'])->first();
        }

        return User::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'],
            'password'      => Hash::make($data['password']),
            'referral_code' => User::generateReferralCode(),
            'referred_by'   => $referrer?->id,
            'referral'      => $data['referral'] ?? '',
        ]);
    }
}
