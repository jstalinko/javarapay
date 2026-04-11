<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
                'paid_at',
                'settled_at'
            ];
}
