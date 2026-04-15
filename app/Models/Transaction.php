<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Transaction extends Model
{
    protected $fillable = [
                'project_id',
                'merchant_ref',
                'is_production',
                'payment_method_code',
                'payment_method_name',
                'amount',
                'total_fee',
                'total_amount',
                'notes',
                'status',
        'txid',
        'reference',
        'pay_url',
        'pay_code',
        'qr_url',
        'expired_at',
        'paid_at',
        'settled_at'
            ];

   protected static function booted()
    {
        static::creating(function ($transaction) {
            if (empty($transaction->txid)) {
                $transaction->txid = self::generateUniqueTxid();
            }
        });
    }

    protected static function generateUniqueTxid()
    {
        do {
            $txid = 'JPAY-' . now()->format('YmdHi') . strtoupper(Str::random(8));
        } while (self::where('txid', $txid)->exists());

        return $txid;
    }

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}

