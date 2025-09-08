<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivationInfo extends Model
{
    protected $fillable = ['user_id', 'status', 'activated_at'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
