<?php

namespace Modules\Administration\Entities;

use Illuminate\Database\Eloquent\Model;
use Auth;

class NotificationTemplate extends Model
{
    
    protected $fillable = [];    
    protected $table = 'notification_templates';

    protected $casts = [
        'body' => 'array',
        'extras' => 'array',
        'via' => 'array',
    ];

}