<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class District extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use SoftDeletes;
    use HasApiTokens, Notifiable, HasRoles;



    protected $table = 'districts';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'district_name','state_id', 'created_by', 'updated_by', 'created_at', 'updated_at', 'deleted_at'
    ];

    public static function getAllDistricts(){
        return self::all();
    }
    public static function allDistricts(){
        return self::select('id as district_id','district_name')
        ->orderBy('district_name','asc')
        ->get();
    }
    public static function addDistrict(){
        return self::select('id as district_id','district_name')
        ->orderBy('district_name','asc')
        ->get();
    }
}
