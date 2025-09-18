<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tx_hash',
        'from_address',
        'to_address',
        'amount',
        'token_address',
        'token_symbol',
        'status',
        'block_number',
        'gas_used',
        'transaction_data',
        'confirmed_at'
    ];

    protected $casts = [
        'transaction_data' => 'array',
        'confirmed_at' => 'datetime',
        'amount' => 'decimal:8'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isConfirmed()
    {
        return $this->status === 'confirmed';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isFailed()
    {
        return $this->status === 'failed';
    }

    public function getBscScanUrl()
    {
        return "https://bscscan.com/tx/{$this->tx_hash}";
    }

    public function getFormattedAmount()
    {
        return number_format($this->amount, 6) . ' ' . ($this->token_symbol ?: 'BNB');
    }
}
