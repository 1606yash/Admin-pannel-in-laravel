<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class DrivingLicenceType extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasApiTokens, Notifiable, HasRoles;



    protected $table = 'dl_types';
    protected $guard_name = 'web';


    protected $fillable = [
        'id','type','created_at', 'updated_at'
    ];

   
    public static function getAllDrivingLicenceTypes(){
        return self::all();
    }
    public static function allDrivingLicenceTypes(){
        return self::select('id as dl_type_id','type as dl_type')
        ->orderBy('type','asc')
        ->get();
    }
}
