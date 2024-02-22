<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Notification extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasApiTokens, Notifiable, HasRoles;

    protected $table = 'notifications';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'related_resource_id', 'related_resource_type', 'related_resource_user_id', 'notification_title', 'notification_description',
        'created_by', 'read_at', 'created_at', 'updated_at'
    ];
    public static function createNotification($data)
    {
        return self::create($data);
    }
}
