<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanSelection extends Model
{
    protected $fillable = [
        'user_id',
        'plan_name',
        'plan_amount',
        'return_percentage',
        'duration_days',
        'expected_return',
        'status',
        'admin_notes',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'plan_amount' => 'decimal:2',
        'expected_return' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    // Status constants
    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function getStatusBadgeAttribute()
    {
        $badges = [
            self::STATUS_PENDING => 'warning',
            self::STATUS_APPROVED => 'success',
            self::STATUS_REJECTED => 'danger',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    public function getStatusTextAttribute()
    {
        $texts = [
            self::STATUS_PENDING => 'Pending',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
        ];

        return $texts[$this->status] ?? 'Unknown';
    }

    public function approve($adminId, $notes = null)
    {
        $this->update([
            'status' => self::STATUS_APPROVED,
            'processed_by' => $adminId,
            'processed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }

    public function reject($adminId, $notes = null)
    {
        $this->update([
            'status' => self::STATUS_REJECTED,
            'processed_by' => $adminId,
            'processed_at' => now(),
            'admin_notes' => $notes,
        ]);
    }
}

