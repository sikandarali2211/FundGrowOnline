<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PlanSelection extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function approve($adminId, $notes = null)
    {
        $this->status = self::STATUS_APPROVED;
        $this->admin_notes = $notes;
        $this->processed_by = $adminId;
        $this->processed_at = now();
        $this->save();
    }

    public function reject($adminId, $notes = null)
    {
        $this->status = self::STATUS_REJECTED;
        $this->admin_notes = $notes;
        $this->processed_by = $adminId;
        $this->processed_at = now();
        $this->save();
    }
}
