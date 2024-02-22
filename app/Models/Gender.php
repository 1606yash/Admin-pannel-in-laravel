<?php

namespace App\Models;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class Gender extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasApiTokens, Notifiable, HasRoles;



    protected $table = 'gender_types';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'gender_name', 'created_at', 'updated_at'
    ];

    public static function getAllGenders(){
        return self::all();
    }
    public static function allGenderTypes(){
        return self::select('id as gender_type_id','gender_name as gender_type')
        ->orderBy('gender_name','asc')
        ->get();
    }
}
