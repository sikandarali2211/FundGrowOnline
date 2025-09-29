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
            self::TYPE_REFERRAL_CHAIN => 'Referral Chain',
            default => 'Unknown'
        };
    }
}
