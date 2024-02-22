<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use DB;
use App\Notifications\EmailVerificationMail;
use Illuminate\Support\Facades\Crypt;

class HighestQualification extends Authenticatable implements Auditable
{
    use \OwenIt\Auditing\Auditable;
    use HasApiTokens, Notifiable, HasRoles;



    protected $table = 'highest_qualification_types';
    protected $guard_name = 'web';


    protected $fillable = [
        'id', 'type','created_at', 'updated_at'
    ];
    public static function getAllHighestQualification(){
        return self::all();
    }
    public static function allHighestQualificationTypes(){
        return self::select('id as highest_qualification_type_id','type as highest_qualification_type')
        ->orderBy('type','asc')
        ->get();
    }
    
}
