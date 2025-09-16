<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentPlan extends Model
{
    protected $fillable = [
        'name',
        'entry_amount',
        'min_amount',
        'max_amount',
        'return_percentage',
        'total_return',
        'duration_days',
        'description',
        'is_active'
    ];

    protected $casts = [
        'entry_amount' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'return_percentage' => 'decimal:2',
        'total_return' => 'decimal:2',
        'duration_days' => 'integer',
        'is_active' => 'boolean'
    ];

    public function userInvestments()
    {
        return $this->hasMany(UserInvestment::class);
    }
}



