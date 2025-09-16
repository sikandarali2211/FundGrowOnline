<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInvestment extends Model
{
    protected $fillable = [
        'user_id',
        'investment_plan_id',
        'amount',
        'status',
        'invested_at',
        'activated_at',
        'maturity_date',
        'return_amount',
        'payment_status'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'return_amount' => 'decimal:2',
        'invested_at' => 'datetime',
        'activated_at' => 'datetime',
        'maturity_date' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function investmentPlan()
    {
        return $this->belongsTo(InvestmentPlan::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_ACTIVE = 'active';
    const STATUS_COMPLETED = 'completed';
    const STATUS_CANCELLED = 'cancelled';

    // Payment status constants
    const PAYMENT_PENDING = 'pending';
    const PAYMENT_RECEIVED = 'received';
    const PAYMENT_REJECTED = 'rejected';

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', self::STATUS_COMPLETED);
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isActive()
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isCompleted()
    {
        return $this->status === self::STATUS_COMPLETED;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_ACTIVE => 'success',
            self::STATUS_COMPLETED => 'info',
            self::STATUS_CANCELLED => 'danger',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            self::STATUS_PENDING => 'Pending Payment',
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_COMPLETED => 'Completed',
            self::STATUS_CANCELLED => 'Cancelled',
        ];

        return $texts[$this->status] ?? 'Unknown';
    }
}



