<?php

namespace Modules\Administration\Entities;

use Illuminate\Database\Eloquent\Model;
use Auth;

class MasterNotificationTemplate extends Model
{
    
    protected $fillable = [];    
    protected $table = 'master_notification_templates';

    protected $casts = [
        'body' => 'array',
        'extras' => 'array',
        'via' => 'array',
    ];

}