<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'bank_id',
        'user_id',
        'reference',
        'amount',
        'fee',
        'received_amount',
        'notes',
        'status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);

    }
    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    public function getPrimaryBank()
    {
        return $this->bank()->where('is_primary', true)->first();
    }
}
