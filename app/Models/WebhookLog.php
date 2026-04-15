<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{

    protected $fillable = [
        'project_id',
        'transaction_id',
        'payload',
        'response_body',
        'response_code',
        'last_sent_at',
        'retries',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
