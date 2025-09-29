<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class GlobalPool extends Model
{
    protected $fillable = [
        'total_amount',
        'transaction_count',
        'last_contribution',
        'last_updated'
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'last_contribution' => 'decimal:2',
        'last_updated' => 'datetime'
    ];

    /**
     * Add commission to global pool
     */
    public static function addCommission(float $amount): void
    {
        $globalPool = self::first();
        
        if (!$globalPool) {
            $globalPool = self::create([
                'total_amount' => $amount,
                'transaction_count' => 1,
                'last_contribution' => $amount,
                'last_updated' => now()
            ]);
        } else {
            $globalPool->increment('total_amount', $amount);
            $globalPool->increment('transaction_count');
            $globalPool->update([
                'last_contribution' => $amount,
                'last_updated' => now()
            ]);
        }
    }

    /**
     * Get current global pool amount
     */
    public static function getTotalAmount(): float
    {
        $globalPool = self::first();
        return $globalPool ? $globalPool->total_amount : 0;
    }

    /**
     * Get global pool statistics
     */
    public static function getStatistics(): array
    {
        $globalPool = self::first();
        
        if (!$globalPool) {
            return [
                'total_amount' => 0,
                'transaction_count' => 0,
                'last_contribution' => 0,
                'last_updated' => null
            ];
        }

        return [
            'total_amount' => $globalPool->total_amount,
            'transaction_count' => $globalPool->transaction_count,
            'last_contribution' => $globalPool->last_contribution,
            'last_updated' => $globalPool->last_updated
        ];
    }
}
