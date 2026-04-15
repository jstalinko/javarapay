<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    protected $fillable = [
        'group',
        'method_code',
        'method_name',
        'gateway',
        'image',
        'description',
        'is_qris',
        'destination',
        'destination_name',
        'min_amount',
        'max_amount',
        'fee_percent',
        'fee_flat',
        'active',
    ];


    public function totalFee($amount)
    {
        $fee = $amount * ($this->fee_percent / 100) + $this->fee_flat;
        return $fee;
    }

    public function totalPay($amount)
    {
        return $amount + $this->totalFee($amount);
    }
}
