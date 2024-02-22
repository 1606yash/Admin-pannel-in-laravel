<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class UserNotification extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use HasApiTokens, Notifiable, HasRoles;

    protected $table = 'user_notifications';
    protected $guard_name = 'web';

    protected $fillable = [
        'id', 'user_id', 'notification_id','is_read', 'read_date','created_at', 'created_by','updated_at', 'updated_by'
    ];

    public static function createUserNotification($data)
    {
        return self::insert($data);
    }
}
