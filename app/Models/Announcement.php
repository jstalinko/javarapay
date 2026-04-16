<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{

  
    protected $fillable = [
        'is_published',
        'content',
        'category',
        'title'
    ];
}
