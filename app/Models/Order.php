<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Order extends Model
{
    protected $fillable = [
        'project_id',
        'user_id',
        'amount',
        'note',
        'txid'
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }   
       protected static function booted()
    {
        static::creating(function ($order) {
            if (empty($order->txid)) {
                $order->txid = self::generateUniqueTxid();
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
}
