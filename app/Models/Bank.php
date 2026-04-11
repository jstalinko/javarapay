<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    protected $fillable = [
        'is_primary',
        'bank_name',
        'account_name',
        'account_number',
        'user_id'
    ];
}
