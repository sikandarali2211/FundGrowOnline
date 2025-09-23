<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\ActivationInfo;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /** Mass assignable */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'role_updated_at',
        'referral',          // legacy raw code (optional)
        'referral_code',     // user ka apna unique code
        'referred_by',       // jis user ne refer kiya (users.id)
        'level',             // 👈 NEW: user level
        'google_id',         // Google OAuth ID
        'wallet_address',    // Admin wallet address
        'security_pin',      // Security PIN for sensitive operations
        'otp_code',          // OTP for verification
        'otp_expires_at',    // OTP expiration time
        'pin_setup_required', // Whether user needs to setup PIN
        'pin_setup_completed_at', // When PIN setup was completed
    ];

    /** Hidden */
    protected $hidden = ['password', 'remember_token', 'security_pin', 'otp_code'];

    /** Casts */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'otp_expires_at' => 'datetime',
            'pin_setup_completed_at' => 'datetime',
            'role_updated_at' => 'datetime',
            'pin_setup_required' => 'boolean',
        ];
    }
    public function activationInfo()
    {
        return $this->hasOne(ActivationInfo::class, 'user_id');
    }
    /** Relationships */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }
    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by');
    }

    /** Unique referral code */
    public static function generateReferralCode(int $length = 8): string
    {
        do {
            $code = strtoupper(Str::random($length));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /** Threshold map: kis level ke liye kitne direct chahiye */
    public static function levelThresholds(): array
    {
        return [
            2 => 12,   // 12 directs → Level 2
            // future: 3 => 36, 4 => 60, ...
        ];
    }

    /** Sponsor ka level dobara calculate karke update karna */
    public static function recalcAndUpgradeLevel(User $sponsor): void
    {
        $directCount = self::where('referred_by', $sponsor->id)->count();

        $newLevel = 1;
        foreach (self::levelThresholds() as $level => $need) {
            if ($directCount >= $need) {
                $newLevel = max($newLevel, $level);
            }
        }

        if ((int)$sponsor->level !== (int)$newLevel) {
            $sponsor->level = $newLevel;
            // saveQuietly taake created event loop na banay
            $sponsor->saveQuietly();
        }
    }

    /** Security PIN and OTP methods */
    
    /**
     * Generate a 6-digit OTP
     */
    public static function generateOTP(): string
    {
        return str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a 6-digit security PIN
     */
    public static function generateSecurityPIN(): string
    {
        return str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Send OTP to user via multiple channels (email, WhatsApp, SMS)
     */
    public function sendOTP(array $channels = ['email']): array
    {
        $otp = self::generateOTP();
        $this->otp_code = $otp;
        $this->otp_expires_at = now()->addMinutes(10); // OTP expires in 10 minutes
        $this->save();

        $otpService = app(\App\Services\OTPService::class);
        $results = $otpService->sendOTPMultipleChannels(
            $channels,
            $otp,
            $this->name,
            $this->email,
            $this->phone
        );

        // Log for testing if no channels work
        if (empty(array_filter($results))) {
            \Log::info("OTP for user {$this->email}: {$otp}");
        }

        return $results;
    }

    /**
     * Verify OTP
     */
    public function verifyOTP(string $otp): bool
    {
        if (!$this->otp_code || !$this->otp_expires_at) {
            return false;
        }

        if (now()->isAfter($this->otp_expires_at)) {
            return false; // OTP expired
        }

        return $this->otp_code === $otp;
    }

    /**
     * Clear OTP after successful verification
     */
    public function clearOTP(): void
    {
        $this->otp_code = null;
        $this->otp_expires_at = null;
        $this->save();
    }

    /**
     * Verify security PIN
     */
    public function verifySecurityPIN(string $pin): bool
    {
        if (!$this->security_pin) {
            return false;
        }

        return $this->security_pin === $pin;
    }

    /**
     * Set security PIN
     */
    public function setSecurityPIN(string $pin): void
    {
        $this->security_pin = $pin;
        $this->pin_setup_required = false;
        $this->pin_setup_completed_at = now();
        $this->save();
    }

    /**
     * Check if user has completed PIN setup
     */
    public function hasCompletedPINSetup(): bool
    {
        return !$this->pin_setup_required && $this->security_pin !== null;
    }

    /** Auto-fill code + default level + sponsor upgrade on child create */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->referral_code)) {
                $user->referral_code = self::generateReferralCode();
            }
            if (empty($user->level)) {
                $user->level = 1; // default
            }
        });

        static::created(function (User $user) {
            // jab koi naya user create ho aur kisi ke ref se aya ho
            if ($user->referred_by) {
                $sponsor = $user->referrer()->first();
                if ($sponsor) {
                    self::recalcAndUpgradeLevel($sponsor);
                }
            }
        });
    }
}
