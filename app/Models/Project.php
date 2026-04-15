<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $fillable = [
        'name',
        'apikey',
        'merchant_code',
        'is_production',
        'fee_type',
        'webhook_url',
        'active',
        'status',
        'user_id',
        'channels'
    ];

    protected $casts = [
        'channels' => 'array',
        'is_production' => 'boolean',
        'active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

