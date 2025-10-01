<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommissionTransaction extends Model
{
    protected $fillable = [
        'user_id',
        'plan_selection_id',
        'total_commission',
        'pool_commission',
        'profit_commission',
        'global_pool_commission',
        'commission_type',
        'description'
    ];

    protected $casts = [
        'total_commission' => 'decimal:2',
        'pool_commission' => 'decimal:2',
        'profit_commission' => 'decimal:2',
        'global_pool_commission' => 'decimal:2'
    ];

    // Commission type constants
    const TYPE_SECOND_PLAN = 'second_plan';
    const TYPE_THIRD_PLAN = 'third_plan';
    const TYPE_FOURTH_PLAN = 'fourth_plan';
    const TYPE_FIFTH_PLAN = 'fifth_plan';
    const TYPE_SIXTH_PLAN = 'sixth_plan';
    const TYPE_SEVENTH_PLAN = 'seventh_plan';
    const TYPE_EIGHTH_PLAN = 'eighth_plan';
    const TYPE_NINTH_PLAN = 'ninth_plan';
    const TYPE_TENTH_PLAN = 'tenth_plan';
    const TYPE_ELEVENTH_PLAN = 'eleventh_plan';
    const TYPE_TWELFTH_PLAN = 'twelfth_plan';
    const TYPE_THIRTEENTH_PLAN = 'thirteenth_plan';
    const TYPE_FOURTEENTH_PLAN = 'fourteenth_plan';
    const TYPE_FIFTEENTH_PLAN = 'fifteenth_plan';
    const TYPE_REFERRAL_CHAIN = 'referral_chain';

    /**
     * Get the user who received the commission
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the plan selection that triggered this commission
     */
    public function planSelection(): BelongsTo
    {
        return $this->belongsTo(PlanSelection::class);
    }

    /**
     * Get commission type badge class
     */
    public function getCommissionTypeBadgeAttribute(): string
    {
        return match($this->commission_type) {
            self::TYPE_SECOND_PLAN => 'success',
            self::TYPE_THIRD_PLAN => 'warning',
            self::TYPE_FOURTH_PLAN => 'danger',
            self::TYPE_FIFTH_PLAN => 'primary',
            self::TYPE_SIXTH_PLAN => 'dark',
            self::TYPE_SEVENTH_PLAN => 'light',
            self::TYPE_EIGHTH_PLAN => 'success',
            self::TYPE_NINTH_PLAN => 'warning',
            self::TYPE_TENTH_PLAN => 'danger',
            self::TYPE_ELEVENTH_PLAN => 'primary',
            self::TYPE_TWELFTH_PLAN => 'dark',
            self::TYPE_THIRTEENTH_PLAN => 'light',
            self::TYPE_FOURTEENTH_PLAN => 'success',
            self::TYPE_FIFTEENTH_PLAN => 'warning',
            self::TYPE_REFERRAL_CHAIN => 'info',
            default => 'secondary'
        };
    }

    /**
     * Get commission type text
     */
    public function getCommissionTypeTextAttribute(): string
    {
        return match($this->commission_type) {
            self::TYPE_SECOND_PLAN => 'Second Plan',
            self::TYPE_THIRD_PLAN => 'Third Plan',
            self::TYPE_FOURTH_PLAN => 'Fourth Plan',
            self::TYPE_FIFTH_PLAN => 'Fifth Plan',
            self::TYPE_SIXTH_PLAN => 'Sixth Plan',
            self::TYPE_SEVENTH_PLAN => 'Seventh Plan',
            self::TYPE_EIGHTH_PLAN => 'Eighth Plan',
            self::TYPE_NINTH_PLAN => 'Ninth Plan',
            self::TYPE_TENTH_PLAN => 'Tenth Plan',
            self::TYPE_ELEVENTH_PLAN => 'Eleventh Plan',
            self::TYPE_TWELFTH_PLAN => 'Twelfth Plan',
            self::TYPE_THIRTEENTH_PLAN => 'Thirteenth Plan',
            self::TYPE_FOURTEENTH_PLAN => 'Fourteenth Plan',
            self::TYPE_FIFTEENTH_PLAN => 'Fifteenth Plan',
            self::TYPE_REFERRAL_CHAIN => 'Referral Chain',
            default => 'Unknown'
        };
    }
}
