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
        'referral',          // legacy raw code (optional)
        'referral_code',     // user ka apna unique code
        'referred_by',       // jis user ne refer kiya (users.id)
        'level',             // 👈 NEW: user level
    ];

    /** Hidden */
    protected $hidden = ['password', 'remember_token'];

    /** Casts */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
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
